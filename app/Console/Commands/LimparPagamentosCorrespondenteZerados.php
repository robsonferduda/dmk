<?php

namespace App\Console\Commands;

use App\Enums\StatusPagamentoCorrespondente;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LimparPagamentosCorrespondenteZerados
 *
 * Remove cabeçalhos de pagamento que ficaram sem valor a pagar — normalmente
 * gerados quando um processo é atribuído a um correspondente e depois trocado,
 * cancelado ou excluído, deixando o pagamento sem itens.
 *
 * Por padrão apenas lista (dry-run) e considera somente o status Gerado.
 *
 * Uso:
 *   php artisan pagamentos:limpar-zerados
 *   php artisan pagamentos:limpar-zerados --mes=6 --ano=2026 --conta=64
 *   php artisan pagamentos:limpar-zerados --mes=6 --ano=2026 --conta=64 --force
 *   php artisan pagamentos:limpar-zerados --incluir-notificados --force
 */
class LimparPagamentosCorrespondenteZerados extends Command
{
    protected $signature = 'pagamentos:limpar-zerados
                            {--mes= : Mês de referência (1–12). Sem valor, considera todas as competências.}
                            {--ano= : Ano de referência. Sem valor, considera todas as competências.}
                            {--conta= : Restringe a um cd_conta_con específico.}
                            {--incluir-notificados : Inclui também Enviado/Aprovado/Recusado sem itens (exceto Pago).}
                            {--force : Efetiva a remoção. Sem esta opção apenas lista.}';

    protected $description = 'Remove pagamentos de correspondentes zerados e sem itens (cascas de competência).';

    public function handle(): int
    {
        $mes                = $this->option('mes');
        $ano                = $this->option('ano');
        $conta              = $this->option('conta');
        $incluirNotificados = (bool) $this->option('incluir-notificados');
        $force              = (bool) $this->option('force');

        $statusPermitidos = [StatusPagamentoCorrespondente::GERADO];

        if ($incluirNotificados) {
            $statusPermitidos = [
                StatusPagamentoCorrespondente::GERADO,
                StatusPagamentoCorrespondente::ENVIADO_APROVACAO,
                StatusPagamentoCorrespondente::APROVADO,
                StatusPagamentoCorrespondente::RECUSADO,
            ];
        }

        $query = PagamentoCorrespondente::with('correspondente')
            ->where('vl_total_pag', '<=', 0)
            ->whereIn('cd_status_pag', $statusPermitidos);

        if ($mes) {
            $query->where('nu_mes_pag', (int) $mes);
        }

        if ($ano) {
            $query->where('nu_ano_pag', (int) $ano);
        }

        if ($conta) {
            $query->where('cd_conta_con', (int) $conta);
        }

        $pagamentos = $query->orderBy('cd_conta_con')
            ->orderBy('nu_ano_pag')
            ->orderBy('nu_mes_pag')
            ->get();

        if ($pagamentos->isEmpty()) {
            $this->info('[limpar-zerados] Nenhum pagamento zerado encontrado com os filtros informados.');
            return 0;
        }

        $this->info('[limpar-zerados] ' . $pagamentos->count() . ' pagamento(s) zerado(s) encontrado(s)' . ($force ? '' : '  MODO LISTAGEM (use --force para remover)'));

        $removidos = 0;
        $mantidos  = 0;

        foreach ($pagamentos as $pagamento) {
            $qtdItens = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)->count();

            $nome = $pagamento->correspondente->nm_razao_social_con
                ?? $pagamento->correspondente->nm_fantasia_con
                ?? ('correspondente ' . $pagamento->cd_correspondente_cor);

            $descricao = sprintf(
                'pag=%d  conta=%d  competencia=%02d/%d  status=%s  itens=%d  %s',
                $pagamento->cd_pagamento_correspondente_pag,
                $pagamento->cd_conta_con,
                $pagamento->nu_mes_pag,
                $pagamento->nu_ano_pag,
                $pagamento->nm_status,
                $qtdItens,
                $nome
            );

            // Notificados/aprovados só são removidos quando não há nenhum item
            $ehGerado = (int) $pagamento->cd_status_pag === StatusPagamentoCorrespondente::GERADO;

            if (! $ehGerado && $qtdItens > 0) {
                $this->line('  MANTIDO   ' . $descricao . '  (possui itens e já foi notificado)');
                $mantidos++;
                continue;
            }

            if (! $force) {
                $this->line('  REMOVERIA ' . $descricao);
                continue;
            }

            DB::transaction(function () use ($pagamento) {
                PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)->delete();
                $pagamento->forceDelete();
            });

            $this->line('  REMOVIDO  ' . $descricao);
            $removidos++;
        }

        if ($force) {
            $this->info("[limpar-zerados] Concluído.  Removidos={$removidos}  Mantidos={$mantidos}");
            Log::info("[pagamentos:limpar-zerados] Removidos={$removidos}  Mantidos={$mantidos}");
        } else {
            $this->info('[limpar-zerados] Nada foi alterado. Repita com --force para efetivar.');
        }

        return 0;
    }
}
