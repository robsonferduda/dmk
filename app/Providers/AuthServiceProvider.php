<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Kodeine\Acl\Models\Eloquent\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        foreach (Permission::all() as $key => $value) {

            $slug = array_keys($value->slug)[0];
            $name = $value->name;

            $perm = $name.'.'.$slug;

            Gate::define($perm, function ($user) use ($value) {

                return $user->getPermissao($value->id);

            });
           
        };

    }
}
