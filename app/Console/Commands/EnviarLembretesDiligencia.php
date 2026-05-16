<?php

namespace App\Console\Commands;

use App\Conta;
use App\Processo;
use App\ContaCorrespondente;
use App\WhatsappMensagem;
use App\Services\ChatPro\ChatProClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * [WHATSAPP-LEMBRETE]
 * Comando diário que envia ao CORRESPONDENTE uma mensagem WhatsApp para
 * cada diligência cujo prazo fatal é "hoje", contendo dados do processo
 * e o link para realizar o check-in.
 *
 * Agendamento padrão: todas as manhãs, via app/Console/Kernel.php.
 * Execução manual:
 *     php artisan whatsapp:lembrete-diligencias
 *     php artisan whatsapp:lembrete-diligencias --data=2026-05-16
 *     php artisan whatsapp:lembrete-diligencias --dry-run
 *     php artisan whatsapp:lembrete-diligencias --processo=97561
 */
class EnviarLembretesDiligencia extends Command
{
    protected $signature = 'whatsapp:lembrete-diligencias
                            {--data= : Data alvo (Y-m-d). Default: hoje.}
                            {--processo= : Limita a um cd_processo_pro espec\u00edfico (ignora filtro de data).}
                            {--force : Reenvia mesmo que j\u00e1 tenha sido enviado hoje (\u00fatil em testes).}
                            {--dry-run : N\u00e3o envia; apenas lista o que enviaria.}';

    protected $description = 'Envia lembretes de diligência via WhatsApp aos correspondentes (prazo fatal = hoje).';

    public function handle()
    {
        $data       = $this->option('data') ? Carbon::parse($this->option('data'))->toDateString() : Carbon::today()->toDateString();
        $cdProcesso = $this->option('processo');
        $dryRun     = (bool) $this->option('dry-run');
        $force      = (bool) $this->option('force');

        if ($cdProcesso) {
            $this->info("[lembrete] MODO TESTE: processo={$cdProcesso} (filtro de data ignorado)" . ($dryRun ? '  (DRY-RUN)' : ''));
        } else {
            $this->info("[lembrete] Data alvo: {$data}" . ($dryRun ? '  (DRY-RUN)' : ''));
        }

        // Quando --processo é passado, ignoramos o filtro de data: o
        // intuito é forçar o envio para um processo específico em teste.
        $q = Processo::whereNotNull('cd_correspondente_cor');
        if ($cdProcesso) {
            $q->where('cd_processo_pro', $cdProcesso);
        } else {
            $q->whereDate('dt_prazo_fatal_pro', $data);
        }

        $processos = $q->with('cliente', 'vara', 'cidade.estado')->get();

        $this->info('[lembrete] Processos encontrados: ' . $processos->count());

        $enviados = 0; $ignorados = 0; $falhas = 0;

        foreach ($processos as $proc) {
            try {
                // Conta (escritório) — fonte das credenciais ChatPro.
                $conta = Conta::where('cd_conta_con', $proc->cd_conta_con)->first();
                if (!$conta) { $ignorados++; continue; }

                $client = ChatProClient::forConta($conta);
                if (!$client) {
                    $this->warn("  processo {$proc->cd_processo_pro}: conta {$conta->cd_conta_con} sem ChatPro ativo.");
                    $ignorados++; continue;
                }

                // Conta do correspondente — fonte do telefone destino.
                $contaCorrespondente = Conta::where('cd_conta_con', $proc->cd_correspondente_cor)->first();
                if (!$contaCorrespondente) { $ignorados++; continue; }

                $destino = $contaCorrespondente->nu_telefone_whatsapp_con ?? null;
                if (empty($destino)) {
                    $this->warn("  processo {$proc->cd_processo_pro}: correspondente {$proc->cd_correspondente_cor} sem WhatsApp.");
                    $ignorados++; continue;
                }

                // Idempotência: não reenvia se já mandamos lembrete deste processo hoje.
                // --force ignora a checagem (útil em testes).
                if (!$force) {
                    $jaEnviado = WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                        ->where('cd_processo_pro', $proc->cd_processo_pro)
                        ->where('ds_tipo_wmm', 'lembrete_diligencia')
                        ->whereDate('created_at', $data)
                        ->exists();
                    if ($jaEnviado) {
                        $this->line("  processo {$proc->cd_processo_pro}: lembrete já enviado hoje, pulando (use --force para reenviar).");
                        $ignorados++; continue;
                    }
                }

                $mensagem = $this->montarMensagem($proc, $contaCorrespondente, $conta);

                $this->line("  -> {$proc->cd_processo_pro}  destino={$destino}");

                if ($dryRun) {
                    $this->line(str_repeat('-', 60));
                    $this->line($mensagem);
                    $this->line(str_repeat('-', 60));
                    $enviados++;
                    continue;
                }

                $res = $client->sendText($destino, $mensagem);

                $msgId = null;
                if (!empty($res['body']) && is_array($res['body'])) {
                    $msgId = $res['body']['resposeMessage']['id']
                          ?? $res['body']['responseMessage']['id']
                          ?? $res['body']['message_id']
                          ?? null;
                }

                $valores = [
                    'cd_conta_con'            => $conta->cd_conta_con,
                    'tp_direcao_wmm'          => 'O',
                    'nu_telefone_origem_wmm'  => null,
                    'nu_telefone_destino_wmm' => preg_replace('/\D+/', '', (string) $destino),
                    'ds_mensagem_wmm'         => $mensagem,
                    'ds_tipo_wmm'             => 'lembrete_diligencia',
                    'ds_status_wmm'           => $res['success'] ? 'sent' : 'failed',
                    'cd_processo_pro'         => $proc->cd_processo_pro,
                    'cd_correspondente_cor'   => $proc->cd_correspondente_cor,
                    'dt_evento_wmm'           => now(),
                ];

                if ($msgId) {
                    // Mesma estratégia race-aware do CheckinNotifier.
                    $row = WhatsappMensagem::firstOrNew(['ds_message_id_wmm' => $msgId]);
                    foreach ($valores as $k => $v) {
                        if ($k === 'ds_status_wmm') {
                            $atual = $row->{$k};
                            if (in_array($atual, [null, '', 'pending', 'sent'], true)) {
                                $row->{$k} = $v;
                            }
                            continue;
                        }
                        if ($row->{$k} === null || $row->{$k} === '') {
                            $row->{$k} = $v;
                        }
                    }
                    if (empty($row->ds_payload_raw_wmm)) {
                        $row->ds_payload_raw_wmm = ['response' => $res, 'context' => 'lembrete_diligencia'];
                    }
                    $row->save();
                } else {
                    $valores['ds_payload_raw_wmm'] = ['response' => $res, 'context' => 'lembrete_diligencia'];
                    WhatsappMensagem::create($valores);
                }

                if ($res['success']) { $enviados++; } else { $falhas++; }
            } catch (\Throwable $e) {
                $falhas++;
                Log::error('[WHATSAPP-LEMBRETE] processo ' . $proc->cd_processo_pro . ': ' . $e->getMessage());
                $this->error('  processo ' . $proc->cd_processo_pro . ' falhou: ' . $e->getMessage());
            }
        }

        $this->info("[lembrete] Concluído. Enviados={$enviados}  Ignorados={$ignorados}  Falhas={$falhas}");
        return 0;
    }

    /**
     * Monta a mensagem a partir do template lembrete_diligencia.
     */
    private function montarMensagem(Processo $proc, Conta $contaCorrespondente, Conta $contaEscritorio)
    {
        $template = config('chatpro.templates.lembrete_diligencia', '');

        // Nome do correspondente: usa o vínculo conta_correspondente_ccr
        // (apelido que o escritório deu); fallback para o nome da conta.
        $nomeCorresp = $contaCorrespondente->nm_conta_con ?? $contaCorrespondente->nm_razao_social_con ?? 'Correspondente';
        $cc = ContaCorrespondente::where('cd_correspondente_cor', $proc->cd_correspondente_cor)
            ->where('cd_conta_con', $proc->cd_conta_con)
            ->first();
        if ($cc && !empty($cc->nm_conta_correspondente_ccr)) {
            $nomeCorresp = $cc->nm_conta_correspondente_ccr;
        }

        $cliente = ($proc->cliente) ? $proc->cliente->nm_razao_social_cli : '—';
        $vara    = ($proc->vara)    ? ($proc->vara->nm_vara_var ?? '—')   : '—';
        $cidade  = '—';
        if ($proc->cidade) {
            $cidade = $proc->cidade->nm_cidade_cde
                . (isset($proc->cidade->estado) ? '/' . $proc->cidade->estado->sg_estado_est : '');
        }
        $hora = $proc->hr_audiencia_pro ? date('H:i', strtotime($proc->hr_audiencia_pro)) : '—';

        // Link de check-in: deep-link com token público (sem login).
        // Token é gerado uma vez por processo e persistido. Se já existir,
        // reutiliza (o link continua válido enquanto o processo existir).
        if (empty($proc->ds_checkin_token_pro)) {
            $proc->ds_checkin_token_pro = bin2hex(random_bytes(16)); // 32 chars hex
            $proc->save();
        }
        $linkCheckin = url('/c/' . $proc->ds_checkin_token_pro);

        return strtr($template, [
            '{correspondente}'  => $nomeCorresp,
            '{processo}'        => $proc->nu_processo_pro ?: ('#' . $proc->cd_processo_pro),
            '{cliente}'         => $cliente,
            '{vara}'            => $vara,
            '{cidade}'          => $cidade,
            '{hora_audiencia}'  => $hora,
            '{link_checkin}'    => $linkCheckin,
        ]);
    }
}
