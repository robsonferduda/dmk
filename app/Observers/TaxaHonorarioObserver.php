<?php

namespace App\Observers;

use App\TaxaHonorario;

class TaxaHonorarioObserver
{
    /**
     * Handle the taxa honorario "created" event.
     *
     * @param  \App\TaxaHonorario  $taxaHonorario
     * @return void
     */
    public function created(TaxaHonorario $taxaHonorario)
    {
        dd("Criou!");
    }

    /**
     * Handle the taxa honorario "updated" event.
     *
     * @param  \App\TaxaHonorario  $taxaHonorario
     * @return void
     */
    public function updated(TaxaHonorario $taxaHonorario)
    {
        //
    }

    /**
     * Handle the taxa honorario "deleted" event.
     *
     * @param  \App\TaxaHonorario  $taxaHonorario
     * @return void
     */
    public function deleted(TaxaHonorario $taxaHonorario)
    {
        //
    }

    /**
     * Handle the taxa honorario "restored" event.
     *
     * @param  \App\TaxaHonorario  $taxaHonorario
     * @return void
     */
    public function restored(TaxaHonorario $taxaHonorario)
    {
        //
    }

    /**
     * Handle the taxa honorario "force deleted" event.
     *
     * @param  \App\TaxaHonorario  $taxaHonorario
     * @return void
     */
    public function forceDeleted(TaxaHonorario $taxaHonorario)
    {
        //
    }
}
