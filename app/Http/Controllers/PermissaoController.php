<?php

namespace App\Http\Controllers;

use App\User;
use App\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kodeine\Acl\Models\Eloquent\Role;

class PermissaoController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  

        $roleAdmin = new Role();
        $roleAdmin->name = 'Correspondente';
        $roleAdmin->slug = 'correspondente';
        $roleAdmin->description = 'Gerencia a conta de correspondente, seus dados e seus processos';
        //$roleAdmin->save();

        //dd($roleAdmin);
        //dd(Role::all());

        //dd($roleAdmin);

        $roleAdmin = Role::find(4);
        //$roleAdmin->description ="Responsável pela conta e por todas as tarefas do sistema";
        //$roleAdmin->save();

        //dd($roleAdmin);

        $user = User::find(70);

        // by object
        $user->assignRole($roleAdmin);
        // or by id
        //$user->assignRole($roleAdmin->id);
        // or by just a slug
        //$user->assignRole('administrator');
      
    }

}