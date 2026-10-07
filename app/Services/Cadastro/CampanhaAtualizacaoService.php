<?php

namespace App\Services\Cadastro;

use App\CampanhaCadastro;
use App\CampanhaCadastroEnvio;
use App\ContaCorrespondente;
use App\Enums\Nivel;
use App\Mail\AtualizacaoCadastroMail;
use App\Services\Contrato\ContratoCorrespondenteGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Campanha de e-mails pedindo aos correspondentes que atualizem o cadastro, enviada em lotes com teto diário.
 */
class CampanhaAtualizacaoService
{
    const CHAVE_BLOQUEIO_SMTP = 'cadastro:campanha:smtp-bloqueado';

    /** @var ContratoCorrespondenteGenerator */
    private $generator;

    public function __construct(ContratoCorrespondenteGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Correspondentes da conta com processo nos últimos $meses e login de correspondente com e-mail,
     * do mais recente para o mais antigo. Prazos digitados com ano errado (ex.: 9019) não contam como "último processo".
     */
    public function candidatos(int $cdConta, int $meses): array
    {
        return DB::select("
            WITH proc AS (
                SELECT cd_correspondente_cor, max(dt_prazo_fatal_pro) AS ultimo
                FROM processo_pro
                WHERE cd_conta_con = ? AND deleted_at IS NULL
                  AND dt_prazo_fatal_pro <= current_date + interval '1 year'
                GROUP BY cd_correspondente_cor
            ), usr AS (
                SELECT cd_conta_con, min(trim(email)) AS email
                FROM users
                WHERE cd_nivel_niv = ? AND deleted_at IS NULL AND email IS NOT NULL AND trim(email) <> ''
                GROUP BY cd_conta_con
            )
            SELECT ccr.cd_conta_correspondente_ccr, ccr.nm_conta_correspondente_ccr, usr.email, proc.ultimo
            FROM conta_correspondente_ccr ccr
            JOIN proc ON proc.cd_correspondente_cor = ccr.cd_correspondente_cor
            JOIN usr ON usr.cd_conta_con = ccr.cd_correspondente_cor
            WHERE ccr.cd_conta_con = ? AND ccr.deleted_at IS NULL
              AND COALESCE(ccr.fl_correspondente_escritorio_ccr, 'N') <> 'S'
              AND proc.ultimo >= current_date - make_interval(months => CAST(? AS integer))
            ORDER BY proc.ultimo DESC, ccr.cd_conta_correspondente_ccr
        ", [$cdConta, Nivel::CORRESPONDENTE, $cdConta, $meses]);
    }

    public function campanhaAberta(int $cdConta): ?CampanhaCadastro
    {
        return CampanhaCadastro::where('cd_conta_con', $cdConta)
            ->whereIn('dc_status_cmc', [CampanhaCadastro::ATIVA, CampanhaCadastro::PAUSADA])
            ->orderByDesc('cd_campanha_cadastro_cmc')
            ->first();
    }

    /**
     * Monta a lista de envio. A campanha nasce pausada: nada é enviado até alguém iniciar.
     */
    public function preparar(int $cdConta, int $meses, ?int $cdUsuario, int $prazoDias = 7): CampanhaCadastro
    {
        if ($this->campanhaAberta($cdConta)) {
            throw new RuntimeException('Já existe uma campanha de atualização cadastral em andamento para esta conta.');
        }

        $candidatos = $this->candidatos($cdConta, $meses);
        if (! $candidatos) {
            throw new RuntimeException('Nenhum correspondente atende ao critério.');
        }

        return DB::transaction(function () use ($cdConta, $meses, $cdUsuario, $prazoDias, $candidatos) {
            $campanha = CampanhaCadastro::create([
                'cd_conta_con'      => $cdConta,
                'nm_campanha_cmc'   => 'Atualização cadastral ' . Carbon::now()->format('m/Y'),
                'dc_criterio_cmc'   => "Correspondentes com processo nos últimos {$meses} meses",
                'dc_status_cmc'     => CampanhaCadastro::PAUSADA,
                'nu_prazo_dias_cmc' => $prazoDias,
                'cd_usuario_cmc'    => $cdUsuario,
            ]);

            $agora = Carbon::now();
            $linhas = [];
            foreach ($candidatos as $ordem => $c) {
                $linhas[] = [
                    'cd_campanha_cadastro_cmc'    => $campanha->cd_campanha_cadastro_cmc,
                    'cd_conta_correspondente_ccr' => $c->cd_conta_correspondente_ccr,
                    'nm_destinatario_cme'         => trim((string) $c->nm_conta_correspondente_ccr) ?: null,
                    'dc_email_cme'                => mb_strtolower(trim($c->email)),
                    'dt_ultimo_processo_cme'      => $c->ultimo ? Carbon::parse($c->ultimo)->toDateString() : null,
                    'nu_ordem_cme'                => $ordem + 1,
                    'dc_token_cme'                => Str::random(40),
                    'dc_status_cme'               => CampanhaCadastroEnvio::PENDENTE,
                    'created_at'                  => $agora,
                    'updated_at'                  => $agora,
                ];
            }
            foreach (array_chunk($linhas, 500) as $lote) {
                CampanhaCadastroEnvio::insert($lote);
            }

            return $campanha;
        });
    }

    public function iniciar(CampanhaCadastro $campanha): void
    {
        $campanha->update([
            'dc_status_cmc' => CampanhaCadastro::ATIVA,
            'dt_inicio_cmc' => $campanha->dt_inicio_cmc ?: Carbon::now(),
        ]);
    }

    public function pausar(CampanhaCadastro $campanha): void
    {
        $campanha->update(['dc_status_cmc' => CampanhaCadastro::PAUSADA]);
    }

    public function enviadosHoje(): int
    {
        return CampanhaCadastroEnvio::where('dt_envio_cme', '>=', Carbon::today())->count();
    }

    /**
     * Envia o próximo lote das campanhas ativas respeitando o teto diário.
     *
     * @return array{enviados:int, falhas:int, motivo:?string}
     */
    public function enviarLote(?int $tamanho = null, ?callable $aposEnvio = null): array
    {
        $resultado = ['enviados' => 0, 'falhas' => 0, 'motivo' => null];

        if (Cache::get(self::CHAVE_BLOQUEIO_SMTP)) {
            $resultado['motivo'] = 'Envio suspenso até amanhã: o servidor de e-mail recusou por limite diário.';
            return $resultado;
        }

        $restanteHoje = config('cadastro.limite_diario') - $this->enviadosHoje();
        $quantidade = min($tamanho ?: config('cadastro.lote'), $restanteHoje);
        if ($quantidade <= 0) {
            $resultado['motivo'] = 'Limite diário atingido.';
            return $resultado;
        }

        $fila = CampanhaCadastroEnvio::with('campanha')
            ->join('campanha_cadastro_cmc as cmc', 'cmc.cd_campanha_cadastro_cmc', '=', 'campanha_cadastro_envio_cme.cd_campanha_cadastro_cmc')
            ->where('cmc.dc_status_cmc', CampanhaCadastro::ATIVA)
            ->where('dc_status_cme', CampanhaCadastroEnvio::PENDENTE)
            ->whereNull('dt_confirmacao_cme')
            ->orderBy('cmc.cd_campanha_cadastro_cmc')
            ->orderBy('nu_ordem_cme')
            ->limit($quantidade)
            ->select('campanha_cadastro_envio_cme.*')
            ->get();

        foreach ($fila as $i => $envio) {
            if ($i > 0 && config('cadastro.intervalo_segundos') > 0) {
                sleep(config('cadastro.intervalo_segundos'));
            }

            try {
                $this->enviar($envio);
                $resultado['enviados']++;
            } catch (\Throwable $e) {
                if (self::erroDeCota($e->getMessage())) {
                    Cache::put(self::CHAVE_BLOQUEIO_SMTP, true, Carbon::tomorrow());
                    Log::warning('[cadastro] SMTP recusou por cota; envios suspensos até amanhã: ' . $e->getMessage());
                    $resultado['motivo'] = 'O servidor de e-mail recusou por limite diário; envios retomam amanhã.';
                    break;
                }

                $tentativas = $envio->nu_tentativas_cme + 1;
                $envio->update([
                    'nu_tentativas_cme' => $tentativas,
                    'dc_erro_cme'       => mb_substr($e->getMessage(), 0, 1000),
                    'dc_status_cme'     => $tentativas >= config('cadastro.tentativas') ? CampanhaCadastroEnvio::FALHA : CampanhaCadastroEnvio::PENDENTE,
                ]);
                $resultado['falhas']++;
                Log::error('[cadastro] Falha ao enviar para ' . $envio->dc_email_cme . ': ' . $e->getMessage());
            }

            if ($aposEnvio) {
                $aposEnvio($envio);
            }
        }

        $this->concluirCampanhasEsgotadas();

        return $resultado;
    }

    public function enviar(CampanhaCadastroEnvio $envio): void
    {
        $prazo = Carbon::today()->addDays($envio->campanha->nu_prazo_dias_cmc);

        Mail::to($envio->dc_email_cme)->send($this->montarEmail($envio, $prazo));

        $envio->update([
            'dc_status_cme'     => CampanhaCadastroEnvio::ENVIADO,
            'dt_envio_cme'      => Carbon::now(),
            'dt_prazo_cme'      => $prazo,
            'nu_envios_cme'     => $envio->nu_envios_cme + 1,
            'nu_tentativas_cme' => 0,
            'dc_erro_cme'       => null,
        ]);
    }

    public function montarEmail(CampanhaCadastroEnvio $envio, Carbon $prazo): AtualizacaoCadastroMail
    {
        return new AtualizacaoCadastroMail(
            AtualizacaoCadastroMail::nomeFormatado($envio->nm_destinatario_cme),
            $envio->dc_email_cme,
            $prazo,
            self::urlClique($envio->dc_token_cme),
            self::urlAbertura($envio->dc_token_cme)
        );
    }

    public static function urlClique(string $token): string
    {
        return config('cadastro.url_publica') . '/cadastro/atualizar/' . $token;
    }

    public static function urlAbertura(string $token): string
    {
        return config('cadastro.url_publica') . '/cadastro/aberto/' . $token . '.gif';
    }

    public function registrarAbertura(string $token): void
    {
        CampanhaCadastroEnvio::where('dc_token_cme', $token)
            ->whereNull('dt_abertura_cme')
            ->update(['dt_abertura_cme' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }

    public function registrarClique(string $token): ?CampanhaCadastroEnvio
    {
        $envio = CampanhaCadastroEnvio::where('dc_token_cme', $token)->first();
        if (! $envio) {
            return null;
        }

        $agora = Carbon::now();
        $envio->update([
            'dt_clique_cme'   => $envio->dt_clique_cme ?: $agora,
            'dt_abertura_cme' => $envio->dt_abertura_cme ?: $agora,
        ]);

        ContaCorrespondente::where('cd_conta_correspondente_ccr', $envio->cd_conta_correspondente_ccr)
            ->update(['fl_atualizacao_cadastro_ccr' => true]);

        return $envio;
    }

    /**
     * Chamado quando o próprio correspondente salva a ficha de cadastro.
     */
    public function registrarConfirmacao(ContaCorrespondente $vinculo): void
    {
        $envios = CampanhaCadastroEnvio::where('cd_conta_correspondente_ccr', $vinculo->cd_conta_correspondente_ccr)
            ->whereNull('dt_confirmacao_cme')
            ->get();

        foreach ($envios as $envio) {
            $envio->update(['dt_confirmacao_cme' => Carbon::now()]);
            $this->avaliarPendencias($envio, $vinculo);
        }
    }

    /**
     * Guarda o que ainda falta no cadastro para gerar o contrato.
     */
    public function avaliarPendencias(CampanhaCadastroEnvio $envio, ?ContaCorrespondente $vinculo = null): void
    {
        // Sempre recarrega: as relações do vínculo podem ter mudado no mesmo request (ficha recém-salva).
        $vinculo = ContaCorrespondente::find($vinculo ? $vinculo->cd_conta_correspondente_ccr : $envio->cd_conta_correspondente_ccr);
        if (! $vinculo) {
            return;
        }

        $envio->update([
            'dc_pendencias_cme' => json_encode(array_values($this->generator->pendenciasGeracao($vinculo)), JSON_UNESCAPED_UNICODE),
            'dt_pendencias_cme' => Carbon::now(),
        ]);
    }

    /**
     * Reavalia quem confirmou e ainda tinha pendências (o correspondente pode completar comarcas depois de salvar a ficha).
     */
    public function atualizarPendencias(int $limite = 20): int
    {
        $envios = CampanhaCadastroEnvio::whereNotNull('dt_confirmacao_cme')
            ->where(function ($q) {
                $q->whereNull('dt_pendencias_cme')
                    ->orWhere(function ($q) {
                        $q->where('dc_pendencias_cme', '<>', '[]')
                            ->where('dt_pendencias_cme', '<', Carbon::now()->subHour());
                    });
            })
            ->orderByRaw('dt_pendencias_cme IS NOT NULL, dt_pendencias_cme')
            ->limit($limite)
            ->get();

        foreach ($envios as $envio) {
            $this->avaliarPendencias($envio);
        }

        return $envios->count();
    }

    /**
     * Volta para a fila (no topo) quem não confirmou até o prazo.
     */
    public function reenviarVencidos(CampanhaCadastro $campanha): int
    {
        return CampanhaCadastroEnvio::where('cd_campanha_cadastro_cmc', $campanha->cd_campanha_cadastro_cmc)
            ->where('dc_status_cme', CampanhaCadastroEnvio::ENVIADO)
            ->whereNull('dt_confirmacao_cme')
            ->where('dt_prazo_cme', '<', Carbon::today())
            ->update(['dc_status_cme' => CampanhaCadastroEnvio::PENDENTE, 'nu_ordem_cme' => 0, 'nu_tentativas_cme' => 0, 'updated_at' => Carbon::now()]);
    }

    public function reenviar(CampanhaCadastroEnvio $envio): void
    {
        $envio->update([
            'dc_status_cme'     => CampanhaCadastroEnvio::PENDENTE,
            'nu_ordem_cme'      => 0,
            'nu_tentativas_cme' => 0,
            'dc_erro_cme'       => null,
        ]);

        if ($envio->campanha->dc_status_cmc === CampanhaCadastro::CONCLUIDA) {
            $envio->campanha->update(['dc_status_cmc' => CampanhaCadastro::ATIVA, 'dt_conclusao_cmc' => null]);
        }
    }

    public function resumo(CampanhaCadastro $campanha): array
    {
        $r = DB::selectOne("
            SELECT
                count(*) AS total,
                count(*) FILTER (WHERE dc_status_cme = 'pendente' AND dt_confirmacao_cme IS NULL) AS aguardando,
                count(dt_envio_cme) AS enviados,
                count(*) FILTER (WHERE dc_status_cme = 'falha') AS falhas,
                count(dt_abertura_cme) AS abertos,
                count(dt_clique_cme) AS cliques,
                count(dt_confirmacao_cme) AS confirmados,
                count(*) FILTER (WHERE dt_confirmacao_cme IS NOT NULL AND dc_pendencias_cme = '[]') AS prontos,
                count(*) FILTER (WHERE dt_confirmacao_cme IS NOT NULL AND dc_pendencias_cme <> '[]') AS com_pendencias,
                count(*) FILTER (WHERE dt_confirmacao_cme IS NULL AND dc_status_cme = 'enviado' AND dt_prazo_cme < current_date) AS vencidos
            FROM campanha_cadastro_envio_cme
            WHERE cd_campanha_cadastro_cmc = ?
        ", [$campanha->cd_campanha_cadastro_cmc]);

        $resumo = array_map('intval', (array) $r);
        $resumo['enviados_hoje'] = $this->enviadosHoje();
        $resumo['limite_diario'] = (int) config('cadastro.limite_diario');
        $resumo['previsao'] = $this->previsaoTermino($resumo['aguardando'], $resumo['enviados_hoje']);
        $resumo['smtp_bloqueado'] = (bool) Cache::get(self::CHAVE_BLOQUEIO_SMTP);

        return $resumo;
    }

    /**
     * Série diária de envios, cliques e confirmações desde o início da campanha.
     */
    public function serieDiaria(CampanhaCadastro $campanha): array
    {
        $inicio = ($campanha->dt_inicio_cmc ?: $campanha->created_at)->copy()->startOfDay();
        $linhas = DB::select("
            SELECT dia::date AS dia,
                count(*) FILTER (WHERE tipo = 'envio') AS enviados,
                count(*) FILTER (WHERE tipo = 'clique') AS cliques,
                count(*) FILTER (WHERE tipo = 'confirmacao') AS confirmados
            FROM (
                SELECT date_trunc('day', dt_envio_cme) AS dia, 'envio' AS tipo FROM campanha_cadastro_envio_cme WHERE cd_campanha_cadastro_cmc = ? AND dt_envio_cme IS NOT NULL
                UNION ALL
                SELECT date_trunc('day', dt_clique_cme), 'clique' FROM campanha_cadastro_envio_cme WHERE cd_campanha_cadastro_cmc = ? AND dt_clique_cme IS NOT NULL
                UNION ALL
                SELECT date_trunc('day', dt_confirmacao_cme), 'confirmacao' FROM campanha_cadastro_envio_cme WHERE cd_campanha_cadastro_cmc = ? AND dt_confirmacao_cme IS NOT NULL
            ) eventos
            WHERE dia >= ?
            GROUP BY 1 ORDER BY 1
        ", array_merge(array_fill(0, 3, $campanha->cd_campanha_cadastro_cmc), [$inicio]));

        $porDia = [];
        foreach ($linhas as $l) {
            $porDia[Carbon::parse($l->dia)->toDateString()] = $l;
        }

        $serie = [];
        for ($dia = $inicio->copy(); $dia->lte(Carbon::today()); $dia->addDay()) {
            $l = $porDia[$dia->toDateString()] ?? null;
            $serie[] = [
                'rotulo'      => $dia->format('d/m'),
                'enviados'    => $l ? (int) $l->enviados : 0,
                'cliques'     => $l ? (int) $l->cliques : 0,
                'confirmados' => $l ? (int) $l->confirmados : 0,
            ];
        }

        return $serie;
    }

    /**
     * Data estimada do último envio, contando só dias úteis.
     */
    private function previsaoTermino(int $aguardando, int $enviadosHoje): ?Carbon
    {
        if ($aguardando <= 0) {
            return null;
        }

        $limite = max(1, (int) config('cadastro.limite_diario'));
        $dia = Carbon::today();
        $capacidadeHoje = $dia->isWeekday() && Carbon::now()->hour < 18 ? max(0, $limite - $enviadosHoje) : 0;
        $restante = $aguardando - $capacidadeHoje;

        while ($restante > 0) {
            $dia->addDay();
            if ($dia->isWeekday()) {
                $restante -= $limite;
            }
        }

        return $dia;
    }

    private function concluirCampanhasEsgotadas(): void
    {
        $abertas = CampanhaCadastro::where('dc_status_cmc', CampanhaCadastro::ATIVA)->get();
        foreach ($abertas as $campanha) {
            $restam = CampanhaCadastroEnvio::where('cd_campanha_cadastro_cmc', $campanha->cd_campanha_cadastro_cmc)
                ->where('dc_status_cme', CampanhaCadastroEnvio::PENDENTE)
                ->whereNull('dt_confirmacao_cme')
                ->exists();
            if (! $restam) {
                $campanha->update(['dc_status_cmc' => CampanhaCadastro::CONCLUIDA, 'dt_conclusao_cmc' => Carbon::now()]);
            }
        }
    }

    public static function erroDeCota(string $mensagem): bool
    {
        return (bool) preg_match('/5\.4\.5|daily (user )?sending (quota|limit)|quota exceeded|too many (messages|emails)|limit exceeded/i', $mensagem);
    }
}
