<?php

namespace App\Providers;

use Horizon;
use App\TaxaHonorario;
use App\Observers\TaxaHonorarioObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TaxaHonorario::observe(TaxaHonorarioObserver::class);

        Horizon::auth(function ($request) {
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
