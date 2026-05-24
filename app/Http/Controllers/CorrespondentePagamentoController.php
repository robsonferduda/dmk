<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use App\Conta;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\Enums\StatusPagamentoCorrespondente;
use App\Services\ChatPro\ChatProClient;

class CorrespondentePagamentoController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    // ─── Listagem ─────────────────────────────────────────────────────────────

    public function pagamentos(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $pagamentos = PagamentoCorrespondente::with('correspondente')
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->get()
            ->sortBy(function ($pag) {
                return $pag->correspondente->nm_razao_social_con
                    ?? $pag->correspondente->nm_fantasia_con
                    ?? 'zzz';
            })
            ->values();

        $statusLabels  = StatusPagamentoCorrespondente::labels();
        $mesAnoAtual   = Carbon::now()->format('m/Y');
        $mesesNavegacao = $this->mesesDisponiveis();

        return view('correspondente/pagamentos', compact(
            'pagamentos', 'mes', 'ano', 'statusLabels', 'mesAnoAtual', 'mesesNavegacao'
        ));
    }

    // ─── Detalhe ──────────────────────────────────────────────────────────────

    public function detalhe($id)
    {
        $pagamento = PagamentoCorrespondente::with(['itens.processo', 'correspondente'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        $statusLabels = StatusPagamentoCorrespondente::labels();

        $dadosBancarios = \DB::select("
            SELECT
                dba.nm_titular_dba,
                dba.nu_cpf_cnpj_dba,
                dba.cd_banco_ban,
                ban.nm_banco_ban,
                dba.nu_agencia_dba,
                dba.nu_conta_dba,
                dba.dc_pix_dba,
                dba.cd_tipo_conta_tcb,
                tcb.nm_tipo_conta_tcb
            FROM conta_correspondente_ccr ccr
            LEFT JOIN dados_bancarios_dba dba ON (ccr.cd_entidade_ete = dba.cd_entidade_ete AND dba.deleted_at IS NULL)
            LEFT JOIN banco_ban ban ON (dba.cd_banco_ban = ban.cd_banco_ban)
            LEFT JOIN tipo_conta_banco_tcb tcb ON (dba.cd_tipo_conta_tcb = tcb.cd_tipo_conta_tcb)
            WHERE ccr.cd_correspondente_cor = ?
            LIMIT 1
        ", [$pagamento->cd_correspondente_cor]);

        $banco = !empty($dadosBancarios) ? (object) $dadosBancarios[0] : null;

        return view('correspondente/pagamento-detalhe', compact('pagamento', 'statusLabels', 'banco'));
    }

    // ─── Consolidar manualmente ───────────────────────────────────────────────

    public function consolidar(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        \Artisan::call('pagamentos:consolidar', [
            '--mes'   => $mes,
            '--ano'   => $ano,
            '--conta' => $this->conta,
        ]);

        $output = trim(\Artisan::output());

        Flash::success('Pagamentos consolidados com sucesso. ' . $output);

        return redirect(url("correspondente/pagamentos?mes={$mes}&ano={$ano}"));
    }

    // ─── Enviar para Aprovação ────────────────────────────────────────────────

    public function enviarAprovacao($id)
    {
        $pagamento = PagamentoCorrespondente::with(['correspondente', 'itens'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        if (! $pagamento->podeEnviarAprovacao()) {
            Flash::error('Este pagamento não pode ser enviado para aprovação no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento) {
            $pagamento->cd_status_pag           = StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
            $pagamento->dt_envio_aprovacao_pag  = Carbon::now();
            $pagamento->save();
        });

        // Notifica via e-mail e WhatsApp
        $this->notificarAprovacao($pagamento);

        Flash::success('Pagamento enviado para aprovação do correspondente.');
        return redirect()->back();
    }

    // ─── Aprovar (pelo escritório em nome do correspondente) ──────────────────

    public function aprovar($id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if (! $pagamento->podeAprovar()) {
            Flash::error('Este pagamento não pode ser aprovado no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento) {
            $pagamento->cd_status_pag     = StatusPagamentoCorrespondente::APROVADO;
            $pagamento->dt_aprovacao_pag  = Carbon::now();
            $pagamento->save();
        });

        Flash::success('Pagamento aprovado. Disponível para efetuar o pagamento.');
        return redirect()->back();
    }

    // ─── Marcar como Pago ─────────────────────────────────────────────────────

    public function pagar(Request $request, $id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if (! $pagamento->podePagar()) {
            Flash::error('Este pagamento não pode ser marcado como pago no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento, $request) {
            $pagamento->cd_status_pag      = StatusPagamentoCorrespondente::PAGO;
            $pagamento->dt_pagamento_pag   = Carbon::now();
            $pagamento->ds_observacao_pag  = $request->observacao;
            $pagamento->save();
        });

        Flash::success('Pagamento registrado com sucesso.');
        return redirect()->back();
    }

    // ─── Teste de Notificação ────────────────────────────────────────────────

    public function testarNotificacao($id)
    {
        $pagamento = PagamentoCorrespondente::with(['correspondente', 'itens'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        $mesAno      = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
        $valorFmt    = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
        $escritorio  = Conta::find($this->conta);
        $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';

        $emailTeste    = 'robsonferduda@gmail.com';
        $whatsappTeste = '48991030204';
        $msgs = [];

        // E-mail de teste
        try {
            \Mail::raw(
                "[TESTE] Olá!\n\nO escritório {$nmEscritorio} encaminhou para sua aprovação o demonstrativo de pagamento referente ao mês {$mesAno}.\n\nValor total: {$valorFmt}\n\nAtenciosamente,\n{$nmEscritorio}",
                function ($msg) use ($emailTeste, $mesAno, $nmEscritorio) {
                    $msg->to($emailTeste)
                        ->subject("[TESTE] Aprovação de Pagamento – {$mesAno} – {$nmEscritorio}");
                }
            );
            $msgs[] = 'E-mail enviado para ' . $emailTeste;
        } catch (\Throwable $e) {
            \Log::warning('[pagamento-teste] Falha ao enviar e-mail: ' . $e->getMessage());
            $msgs[] = 'Falha ao enviar e-mail: ' . $e->getMessage();
        }

        // WhatsApp de teste
        try {
            $conta   = Conta::find($this->conta);
            $chatpro = ChatProClient::forConta($conta);
            if ($chatpro) {
                $mensagem = "[TESTE] Olá! O escritório *{$nmEscritorio}* encaminhou para sua aprovação o demonstrativo de pagamento referente ao mês *{$mesAno}*.\n\n*Valor total: {$valorFmt}*";
                $chatpro->sendText($whatsappTeste, $mensagem);
                $msgs[] = 'WhatsApp enviado para ' . $whatsappTeste;
            } else {
                $msgs[] = 'WhatsApp: ChatPro não configurado para esta conta';
            }
        } catch (\Throwable $e) {
            \Log::warning('[pagamento-teste] Falha ao enviar WhatsApp: ' . $e->getMessage());
            $msgs[] = 'Falha ao enviar WhatsApp: ' . $e->getMessage();
        }

        Flash::success('[TESTE] ' . implode('. ', $msgs) . '.');
        return redirect()->back();
    }

    // ─── Privados ─────────────────────────────────────────────────────────────

    private function notificarAprovacao(PagamentoCorrespondente $pagamento): void
    {
        $correspondente = $pagamento->correspondente;

        if (! $correspondente) {
            return;
        }

        $mesAno    = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
        $valorFmt  = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
        $escritorio = Conta::find($this->conta);
        $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';

        // E-mail
        $emails = DB::table('endereco_eletronico_ele as e')
            ->join('entidade_ete as ent', 'ent.cd_entidade_ete', '=', 'e.cd_entidade_ete')
            ->where('ent.cd_conta_con', $correspondente->cd_conta_con)
            ->pluck('e.dc_endereco_eletronico_ede')
            ->filter()
            ->unique()
            ->values();

        foreach ($emails as $email) {
            try {
                \Mail::raw(
                    "Olá!\n\nO escritório {$nmEscritorio} encaminhou para sua aprovação o demonstrativo de pagamento referente ao mês {$mesAno}.\n\nValor total: {$valorFmt}\n\nAcesse o sistema para visualizar os detalhes e confirmar.\n\nAtenciosamente,\n{$nmEscritorio}",
                    function ($msg) use ($email, $mesAno, $nmEscritorio) {
                        $msg->to($email)
                            ->subject("Aprovação de Pagamento – {$mesAno} – {$nmEscritorio}");
                    }
                );
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar e-mail para ' . $email . ': ' . $e->getMessage());
            }
        }

        // WhatsApp
        $whatsapp = $correspondente->nu_telefone_whatsapp_con ?? null;
        if ($whatsapp) {
            try {
                $conta    = Conta::find($this->conta);
                $chatpro  = ChatProClient::forConta($conta);
                if ($chatpro) {
                    $mensagem = "Olá! O escritório *{$nmEscritorio}* encaminhou para sua aprovação o demonstrativo de pagamento referente ao mês *{$mesAno}*.\n\n*Valor total: {$valorFmt}*\n\nAcesse o sistema para visualizar os detalhes.";
                    $chatpro->sendText($whatsapp, $mensagem);
                }
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar WhatsApp: ' . $e->getMessage());
            }
        }
    }

    private function mesesDisponiveis(): array
    {
        $meses = PagamentoCorrespondente::where('cd_conta_con', $this->conta)
            ->selectRaw('nu_mes_pag, nu_ano_pag')
            ->distinct()
            ->orderByRaw('nu_ano_pag DESC, nu_mes_pag DESC')
            ->get();

        // Garante que o mês atual esteja sempre na lista
        $atual = ['mes' => Carbon::now()->month, 'ano' => Carbon::now()->year];
        $lista = [['mes' => $atual['mes'], 'ano' => $atual['ano']]];

        foreach ($meses as $m) {
            $entry = ['mes' => $m->nu_mes_pag, 'ano' => $m->nu_ano_pag];
            if ($entry !== $lista[0]) {
                $lista[] = $entry;
            }
        }

        return $lista;
    }
}