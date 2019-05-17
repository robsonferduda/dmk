<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kodeine\Acl\Models\Eloquent\User;
use Kodeine\Acl\Models\Eloquent\Role;
use Kodeine\Acl\Models\Eloquent\Permission;

class PermissaoController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  
        return view('permissoes/permissoes',['permissoes' => Permission::all()]);
    }

    public function users()
    {  
        return view('permissoes/users',['users' => User::with('roles')->get()]);
    }

}