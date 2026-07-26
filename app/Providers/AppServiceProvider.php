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
        if ($this->app->environment('local')) {
            ini_set('memory_limit', '512M');
        }

        TaxaHonorario::observe(TaxaHonorarioObserver::class);
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
