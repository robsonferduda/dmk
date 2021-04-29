<?php

namespace App\Providers;

use App\User;
use App\Permissao;
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

                /*
                if(empty(\Session::get('lista_perm_user_'.$user->id))){
                    \Session::put('lista_perm_user_'.$user->id, User::where('id',$user->id)->first()->getArrayOfIdPermissao());
                }
                    
                $permissoes_usuario = \Session::get('lista_perm_user_'.$user->id);
              
                return in_array($value->id, $permissoes_usuario);
                */

                $permissoes_usuario = User::where('id',$user->id)->first()->getArrayOfIdPermissao();
                return in_array($value->id, $permissoes_usuario);

            });
           
        };

    }
}
