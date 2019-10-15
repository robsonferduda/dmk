<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        //Inicialmente as permissões serão por nível
        Gate::define('menu-agenda', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-calendario', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-cliente', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-correspondente', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-usuario', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-processos', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-financeiro', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-despesas', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-agenda', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-configuracoes', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });

        Gate::define('menu-permissoes', function ($user) {

            if($user->role()->first()->slug == 'administrator')
                return true;
            else
                return false;
        });
    }
}
