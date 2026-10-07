<?php

namespace App\Services\Autentique;

use App\ContaCorrespondente;
use App\ContratoAssinatura;
use App\ContratoSignatario;
use App\Services\Contrato\ContratoCorrespondenteGenerator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Envio do contrato do correspondente para assinatura eletrônica na Autentique e acompanhamento do documento.
 *
 * Ordem de assinatura: correspondente → DMK (assinada automaticamente pelo dono do token) → testemunhas.
 */
class ContratoAssinaturaService
{
    /** @var AutentiqueClient */
    private $client;

    /** @var ContratoCorrespondenteGenerator */
    private $generator;

    public function __construct(AutentiqueClient $client, ContratoCorrespondenteGenerator $generator)
    {
        $this->client = $client;
        $this->generator = $generator;
    }

    public function habilitadoParaConta($cdConta): bool
    {
        return in_array((int) $cdConta, config('autentique.contas', []), true);
    }

    public function ultimaAssinatura(ContaCorrespondente $vinculo): ?ContratoAssinatura
    {
        return ContratoAssinatura::with('signatarios')
            ->where('cd_conta_correspondente_ccr', $vinculo->cd_conta_correspondente_ccr)
            ->orderByDesc('cd_contrato_assinatura_cas')
            ->first();
    }

    public function assinaturaEmAndamento(ContaCorrespondente $vinculo): ?ContratoAssinatura
    {
        return ContratoAssinatura::where('cd_conta_correspondente_ccr', $vinculo->cd_conta_correspondente_ccr)
            ->whereIn('dc_status_cas', ContratoAssinatura::EM_ANDAMENTO)
            ->orderByDesc('cd_contrato_assinatura_cas')
            ->first();
    }

    /**
     * Dados do card de assinatura nas telas do correspondente; null quando a integração não se aplica.
     */
    public function painel(ContaCorrespondente $vinculo): ?array
    {
        if (! $this->habilitadoParaConta($vinculo->cd_conta_con)) {
            return null;
        }

        try {
            $assinatura = $this->ultimaAssinatura($vinculo);
        } catch (QueryException $e) {
            // Tabelas ainda não criadas (migration pendente).
            return null;
        }

        $emAndamento = $assinatura && $assinatura->emAndamento();

        return [
            'assinatura'   => $assinatura,
            'emAndamento'  => $emAndamento,
            'impedimentos' => $emAndamento ? [] : $this->impedimentosEnvio($vinculo),
            'contato'      => $this->contatoSugerido($vinculo),
            'sandbox'      => (bool) config('autentique.sandbox'),
        ];
    }

    /**
     * Contato sugerido para o envio (e-mail e telefone do cadastro do correspondente).
     */
    public function contatoSugerido(ContaCorrespondente $vinculo): array
    {
        $dados = $this->generator->dadosPartesContratado($vinculo);

        return [
            'email'    => $dados['email'],
            'telefone' => $dados['telefone'],
        ];
    }

    /**
     * @return string[] motivos que impedem o envio
     */
    public function impedimentosEnvio(ContaCorrespondente $vinculo): array
    {
        $motivos = [];

        if (! $this->habilitadoParaConta($vinculo->cd_conta_con)) {
            $motivos[] = 'Assinatura eletrônica não habilitada para esta conta.';
        }
        if (! $this->client->configurado()) {
            $motivos[] = 'Token da Autentique não configurado.';
        }
        if (! $this->generator->caminhoAbsoluto($vinculo)) {
            $motivos[] = 'Gere o contrato antes de enviar para assinatura.';
        }
        if ($this->assinaturaEmAndamento($vinculo)) {
            $motivos[] = 'Já existe um envio aguardando assinaturas. Cancele-o para enviar novamente.';
        }
        if (! config('autentique.contratante.email')) {
            $motivos[] = 'E-mail da contratante não configurado (AUTENTIQUE_CONTRATANTE_EMAIL).';
        }
        foreach (config('autentique.testemunhas', []) as $i => $testemunha) {
            if (empty($testemunha['email'])) {
                $motivos[] = 'E-mail da testemunha ' . ($i + 1) . ' não configurado.';
            }
        }

        return $motivos;
    }

    /**
     * @param string $canal ContratoAssinatura::CANAL_EMAIL ou CANAL_WHATSAPP
     * @param string $destino e-mail ou telefone do correspondente
     */
    public function enviar(ContaCorrespondente $vinculo, string $canal, string $destino, ?int $cdUsuario): ContratoAssinatura
    {
        $impedimentos = $this->impedimentosEnvio($vinculo);
        if ($impedimentos) {
            throw new AutentiqueException(implode(' ', $impedimentos));
        }

        $correspondente = $this->signatarioCorrespondente($vinculo, $canal, $destino);
        $contratante = config('autentique.contratante');
        $testemunhas = array_values(config('autentique.testemunhas', []));

        $emails = array_filter([
            $correspondente['dc_email_csi'],
            $contratante['email'],
            $testemunhas[0]['email'] ?? null,
            $testemunhas[1]['email'] ?? null,
        ]);
        $emailsNormalizados = array_map('mb_strtolower', $emails);
        if (count($emailsNormalizados) !== count(array_unique($emailsNormalizados))) {
            throw new AutentiqueException('O e-mail do correspondente não pode ser o mesmo da contratante ou das testemunhas.');
        }

        $sandbox = (bool) config('autentique.sandbox');

        $assinatura = DB::transaction(function () use ($vinculo, $canal, $cdUsuario, $sandbox, $correspondente, $contratante, $testemunhas) {
            $assinatura = ContratoAssinatura::create([
                'cd_conta_correspondente_ccr' => $vinculo->cd_conta_correspondente_ccr,
                'cd_conta_con'                => $vinculo->cd_conta_con,
                'dc_status_cas'               => ContratoAssinatura::ENVIANDO,
                'dc_canal_cas'                => $canal,
                'dc_caminho_original_cas'     => $vinculo->dc_caminho_contrato_ccr,
                'fl_sandbox_cas'              => $sandbox,
                'cd_usuario_envio_cas'        => $cdUsuario,
                'dt_envio_cas'                => Carbon::now(),
            ]);

            $linhas = [
                $correspondente,
                [
                    'dc_papel_csi'      => ContratoSignatario::CONTRATANTE,
                    'nm_signatario_csi' => $contratante['nome'],
                    'dc_email_csi'      => $contratante['email'],
                ],
            ];
            foreach ([ContratoSignatario::TESTEMUNHA_1, ContratoSignatario::TESTEMUNHA_2] as $i => $papel) {
                $linhas[] = [
                    'dc_papel_csi'      => $papel,
                    'nm_signatario_csi' => $testemunhas[$i]['nome'] ?? null,
                    'dc_email_csi'      => $testemunhas[$i]['email'] ?? null,
                ];
            }

            foreach ($linhas as $ordem => $linha) {
                $assinatura->signatarios()->create($linha + ['nu_ordem_csi' => $ordem + 1]);
            }

            return $assinatura;
        });

        $assinatura->load('signatarios');

        try {
            $documento = $this->client->criarDocumento(
                $this->generator->caminhoAbsoluto($vinculo),
                [
                    'name'      => 'Contrato de Correspondência – ' . ($vinculo->nm_conta_correspondente_ccr ?: 'Correspondente'),
                    'message'   => config('autentique.mensagem'),
                    'sortable'  => true,
                    'refusable' => true,
                ],
                $this->montarSignatarios($assinatura),
                $sandbox
            );
        } catch (\Throwable $e) {
            $assinatura->update([
                'dc_status_cas' => ContratoAssinatura::ERRO,
                'dc_erro_cas'   => mb_substr($e->getMessage(), 0, 1000),
            ]);
            Log::error('[autentique] Falha ao criar documento para CCR ' . $vinculo->cd_conta_correspondente_ccr . ': ' . $e->getMessage());

            throw $e instanceof AutentiqueException ? $e : new AutentiqueException($e->getMessage(), 0, $e);
        }

        $assinatura->update([
            'cd_documento_autentique_cas' => $documento['id'],
            'dc_status_cas'               => ContratoAssinatura::PENDENTE,
        ]);
        $this->vincularPublicIds($assinatura, $documento['signatures'] ?? []);

        return $assinatura->fresh('signatarios');
    }

    /**
     * Reenvia o pedido ao próximo signatário pendente. Se a vez é da DMK, assina automaticamente.
     */
    public function reenviar(ContratoAssinatura $assinatura): string
    {
        $this->exigirEmAndamento($assinatura);

        $proximo = $assinatura->signatarios->first(function (ContratoSignatario $s) {
            return ! $s->dt_assinado_csi && ! $s->dt_recusado_csi;
        });

        if (! $proximo) {
            $this->sincronizar($assinatura);

            return 'Todas as partes já assinaram; situação atualizada.';
        }

        if ($proximo->dc_papel_csi === ContratoSignatario::CONTRATANTE) {
            $this->sincronizar($assinatura);

            return 'Situação atualizada.';
        }

        if (! $proximo->dc_public_id_csi) {
            throw new AutentiqueException('Signatário sem identificador na Autentique; sincronize o envio.');
        }

        $this->client->reenviar([$proximo->dc_public_id_csi]);

        return 'Pedido de assinatura reenviado para ' . $proximo->rotulo() . '.';
    }

    public function cancelar(ContratoAssinatura $assinatura): void
    {
        $this->exigirEmAndamento($assinatura);

        if ($assinatura->cd_documento_autentique_cas) {
            try {
                $this->client->cancelar($assinatura->cd_documento_autentique_cas);
            } catch (AutentiqueException $e) {
                $documento = $this->buscarDocumentoOuNulo($assinatura->cd_documento_autentique_cas);
                if ($documento && empty($documento['deleted_at'])) {
                    throw $e;
                }
            }
        }

        $assinatura->update([
            'dc_status_cas'    => ContratoAssinatura::CANCELADO,
            'dt_cancelado_cas' => Carbon::now(),
        ]);
    }

    /**
     * Consulta o documento na Autentique e reconcilia o estado local. Idempotente e seguro para chamadas concorrentes.
     */
    public function sincronizar(ContratoAssinatura $assinatura): ContratoAssinatura
    {
        if (! $assinatura->cd_documento_autentique_cas) {
            return $assinatura;
        }

        return Cache::lock('autentique:documento:' . $assinatura->cd_documento_autentique_cas, 120)
            ->block(60, function () use ($assinatura) {
                $assinatura = $assinatura->fresh('signatarios');
                if (! $assinatura->emAndamento()) {
                    return $assinatura;
                }

                $documento = $this->buscarDocumentoOuNulo($assinatura->cd_documento_autentique_cas);

                if (! $documento || ! empty($documento['deleted_at'])) {
                    $assinatura->update([
                        'dc_status_cas'    => ContratoAssinatura::CANCELADO,
                        'dt_cancelado_cas' => Carbon::now(),
                        'dc_erro_cas'      => 'Documento excluído na Autentique.',
                    ]);

                    return $assinatura;
                }

                $this->atualizarSignatarios($assinatura, $documento['signatures'] ?? []);
                $assinatura->load('signatarios');

                $recusa = $assinatura->signatarios->first(function (ContratoSignatario $s) {
                    return (bool) $s->dt_recusado_csi;
                });
                if ($recusa) {
                    $assinatura->update([
                        'dc_status_cas'   => ContratoAssinatura::RECUSADO,
                        'dt_recusado_cas' => $recusa->dt_recusado_csi,
                    ]);

                    return $assinatura;
                }

                $this->assinarComoContratanteSeFor($assinatura);

                $pendentes = $assinatura->signatarios->filter(function (ContratoSignatario $s) {
                    return ! $s->dt_assinado_csi;
                });
                if ($pendentes->isEmpty() && ! empty($documento['files']['signed'])) {
                    $this->concluir($assinatura, $documento['files']['signed']);
                }

                return $assinatura;
            });
    }

    public function caminhoAssinado(ContratoAssinatura $assinatura): ?string
    {
        if (! $assinatura->dc_caminho_assinado_cas) {
            return null;
        }

        $caminho = ContratoCorrespondenteGenerator::caminhoPrivado($assinatura->dc_caminho_assinado_cas);

        return is_file($caminho) ? $caminho : null;
    }

    private function signatarioCorrespondente(ContaCorrespondente $vinculo, string $canal, string $destino): array
    {
        $base = [
            'dc_papel_csi'      => ContratoSignatario::CORRESPONDENTE,
            'nm_signatario_csi' => $vinculo->nm_conta_correspondente_ccr,
            'dc_email_csi'      => null,
            'dc_telefone_csi'   => null,
        ];

        if ($canal === ContratoAssinatura::CANAL_EMAIL) {
            $email = mb_strtolower(trim($destino));
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new AutentiqueException('Informe um e-mail válido para o correspondente.');
            }

            return ['dc_email_csi' => $email] + $base;
        }

        if ($canal === ContratoAssinatura::CANAL_WHATSAPP) {
            return ['dc_telefone_csi' => self::telefoneInternacional($destino)] + $base;
        }

        throw new AutentiqueException('Canal de envio inválido.');
    }

    /**
     * Celular brasileiro no formato +55DDNNNNNNNNN.
     */
    public static function telefoneInternacional(string $telefone): string
    {
        $digitos = preg_replace('/\D+/', '', $telefone);
        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            $digitos = '55' . $digitos;
        }
        if (! preg_match('/^55\d{2}9\d{8}$/', $digitos)) {
            throw new AutentiqueException('Informe um celular válido com DDD (ex.: 48 99999-9999) para o envio por WhatsApp.');
        }

        return '+' . $digitos;
    }

    private function montarSignatarios(ContratoAssinatura $assinatura): array
    {
        $testemunhas = array_values(config('autentique.testemunhas', []));
        $signatarios = [];

        foreach ($assinatura->signatarios as $s) {
            switch ($s->dc_papel_csi) {
                case ContratoSignatario::CORRESPONDENTE:
                    $signatarios[] = $s->dc_telefone_csi
                        ? ['phone' => $s->dc_telefone_csi, 'delivery_method' => 'DELIVERY_METHOD_WHATSAPP', 'action' => 'SIGN']
                        : ['email' => $s->dc_email_csi, 'action' => 'SIGN'];
                    break;
                case ContratoSignatario::CONTRATANTE:
                    $signatarios[] = ['email' => $s->dc_email_csi, 'action' => 'SIGN'];
                    break;
                default:
                    $indice = $s->dc_papel_csi === ContratoSignatario::TESTEMUNHA_1 ? 0 : 1;
                    $signatario = ['email' => $s->dc_email_csi, 'action' => 'SIGN_AS_A_WITNESS'];
                    $cpf = preg_replace('/\D+/', '', (string) ($testemunhas[$indice]['cpf'] ?? ''));
                    if (strlen($cpf) === 11) {
                        $signatario['configs'] = ['cpf' => $cpf];
                    }
                    $signatarios[] = $signatario;
            }
        }

        return $signatarios;
    }

    /**
     * Associa cada assinatura retornada pela Autentique ao signatário local: por e-mail e, no WhatsApp, por telefone.
     */
    private function vincularPublicIds(ContratoAssinatura $assinatura, array $assinaturasRemotas): void
    {
        $remotas = array_values(array_filter($assinaturasRemotas, function ($r) {
            return ! empty($r['public_id']) && ! empty($r['action']['name']);
        }));
        $usadas = [];

        foreach ($assinatura->signatarios as $s) {
            foreach ($remotas as $i => $r) {
                if (isset($usadas[$i]) || ! $this->mesmoSignatario($s, $r)) {
                    continue;
                }
                $usadas[$i] = true;
                $s->update([
                    'dc_public_id_csi' => $r['public_id'],
                    'dc_link_csi'      => $r['link']['short_link'] ?? null,
                ]);
                break;
            }
        }

        $correspondente = $assinatura->signatario(ContratoSignatario::CORRESPONDENTE);
        if ($correspondente && ! $correspondente->dc_public_id_csi) {
            foreach ($remotas as $i => $r) {
                if (! isset($usadas[$i]) && strtoupper($r['action']['name']) === 'SIGN') {
                    $correspondente->update([
                        'dc_public_id_csi' => $r['public_id'],
                        'dc_link_csi'      => $r['link']['short_link'] ?? null,
                    ]);
                    break;
                }
            }
        }
    }

    private function mesmoSignatario(ContratoSignatario $s, array $remota): bool
    {
        if ($s->dc_email_csi) {
            $emails = array_filter([$remota['email'] ?? null, $remota['user']['email'] ?? null]);
            foreach ($emails as $email) {
                if (mb_strtolower($email) === mb_strtolower($s->dc_email_csi)) {
                    return true;
                }
            }

            return false;
        }

        if ($s->dc_telefone_csi && ! empty($remota['user']['phone'])) {
            return preg_replace('/\D+/', '', $remota['user']['phone']) === preg_replace('/\D+/', '', $s->dc_telefone_csi);
        }

        return false;
    }

    private function atualizarSignatarios(ContratoAssinatura $assinatura, array $assinaturasRemotas): void
    {
        $porPublicId = [];
        foreach ($assinaturasRemotas as $r) {
            if (! empty($r['public_id'])) {
                $porPublicId[$r['public_id']] = $r;
            }
        }

        if ($assinatura->signatarios->contains(function ($s) {
            return ! $s->dc_public_id_csi;
        })) {
            $this->vincularPublicIds($assinatura, $assinaturasRemotas);
            $assinatura->load('signatarios');
        }

        foreach ($assinatura->signatarios as $s) {
            $r = $porPublicId[$s->dc_public_id_csi] ?? null;
            if (! $r) {
                continue;
            }

            $dados = array_filter([
                'dt_visualizado_csi' => self::data($r['viewed']['created_at'] ?? null),
                'dt_assinado_csi'    => self::data($r['signed']['created_at'] ?? null),
                'dt_recusado_csi'    => self::data($r['rejected']['created_at'] ?? null),
                'dc_motivo_csi'      => $r['rejected']['reason'] ?? null,
            ]);

            if (! empty($r['email_events']['refused'])) {
                $dados['dt_falha_entrega_csi'] = $s->dt_falha_entrega_csi ?: Carbon::now();
                $dados['dc_motivo_csi'] = $dados['dc_motivo_csi'] ?? ($r['email_events']['reason'] ?? 'E-mail recusado pelo servidor do destinatário.');
            }

            if ($dados) {
                $s->update($dados);
            }
        }
    }

    private function assinarComoContratanteSeFor(ContratoAssinatura $assinatura): void
    {
        $correspondente = $assinatura->signatario(ContratoSignatario::CORRESPONDENTE);
        $contratante = $assinatura->signatario(ContratoSignatario::CONTRATANTE);

        if (! $correspondente || ! $contratante || ! $correspondente->dt_assinado_csi || $contratante->dt_assinado_csi) {
            return;
        }

        try {
            $this->client->assinarComoDono($assinatura->cd_documento_autentique_cas);
            $contratante->update(['dt_assinado_csi' => Carbon::now()]);
            if ($assinatura->dc_erro_cas) {
                $assinatura->update(['dc_erro_cas' => null]);
            }
        } catch (AutentiqueException $e) {
            Log::error('[autentique] Assinatura automática da contratante falhou no documento '
                . $assinatura->cd_documento_autentique_cas . ': ' . $e->getMessage());
            $assinatura->update(['dc_erro_cas' => mb_substr('Assinatura automática da DMK falhou: ' . $e->getMessage(), 0, 1000)]);
        }
    }

    private function concluir(ContratoAssinatura $assinatura, string $urlAssinado): void
    {
        $relativo = 'contratos-correspondente/' . $assinatura->cd_conta_correspondente_ccr
            . '/contrato-assinado-' . Carbon::now()->format('Ymd-His') . '.pdf';
        $destino = ContratoCorrespondenteGenerator::caminhoPrivado($relativo);

        if (! File::isDirectory(dirname($destino))) {
            File::makeDirectory(dirname($destino), 0775, true, true);
        }

        $this->client->baixarArquivo($urlAssinado, $destino);

        $inicio = (string) @file_get_contents($destino, false, null, 0, 5);
        if ($inicio !== '%PDF-') {
            @unlink($destino);
            throw new AutentiqueException('O arquivo assinado baixado da Autentique não é um PDF.');
        }

        DB::transaction(function () use ($assinatura, $relativo) {
            $assinatura->update([
                'dc_status_cas'           => ContratoAssinatura::ASSINADO,
                'dc_caminho_assinado_cas' => $relativo,
                'dt_concluido_cas'        => Carbon::now(),
                'dc_erro_cas'             => null,
            ]);

            if (! $assinatura->fl_sandbox_cas) {
                ContaCorrespondente::where('cd_conta_correspondente_ccr', $assinatura->cd_conta_correspondente_ccr)
                    ->update(['fl_contrato_assinado_ccr' => true]);
            }
        });
    }

    private function buscarDocumentoOuNulo(string $id): ?array
    {
        try {
            return $this->client->documento($id);
        } catch (AutentiqueException $e) {
            if (stripos($e->getMessage(), 'not_found') !== false || stripos($e->getMessage(), 'not found') !== false) {
                return null;
            }
            throw $e;
        }
    }

    private function exigirEmAndamento(ContratoAssinatura $assinatura): void
    {
        if (! $assinatura->emAndamento()) {
            throw new AutentiqueException('Este envio não está mais aguardando assinaturas.');
        }
    }

    private static function data($valor): ?Carbon
    {
        return $valor ? Carbon::parse($valor) : null;
    }
}
