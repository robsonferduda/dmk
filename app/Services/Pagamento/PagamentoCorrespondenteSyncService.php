<?php

namespace App\Services\Pagamento;

class PagamentoCorrespondenteSyncService
{
    private PagamentoCorrespondenteRefreshService $refreshService;

    public function __construct(PagamentoCorrespondenteRefreshService $refreshService)
    {
        $this->refreshService = $refreshService;
    }

    /**
     * Sincroniza honorário, despesas e vínculo do processo nos pagamentos consolidados.
     */
    public function syncProcesso(int $cdProcessoPro): void
    {
        $this->refreshService->syncProcesso($cdProcessoPro);
    }
}
