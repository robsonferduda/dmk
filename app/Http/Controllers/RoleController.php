<?php

namespace App\Http\Controllers;

use App\User;
use App\Conta;
use App\Role as RoleSistema;
use Illuminate\Http\Request;
use Kodeine\Acl\Models\Eloquent\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  
        return view('permissoes/roles',['roles' => Role::all()]);
    }

    public function roleUser($id)
    {  
    	$rolesUser = User::find($id)->roles;
		return Response::json($rolesUser);
        
    }

    public function adicionarRole(Request $request)
    {  

    	$user = User::find($request->id);
    	$role = RoleSistema::find($request->role);

        //Remove roles anteriores
        $rolesUser = User::find($request->id)->roles;

        if($user->assignRole($role)){

            foreach ($user->permissao()->get() as $p) {
                $user->permissao()->detach($p);
            }

            //Adicona permissoes do perfil
            $perms = $role->permissao()->get();
            foreach ($perms as $p) {
                $user->permissao()->attach($p);
            }

            foreach($rolesUser as $r){

                $user->revokeRole($role);
            }

    		$msg = array('status' => true, 'msg' => 'Perfil adicionado com sucesso');
        }
    	else
    		$msg = array('status' => false, 'msg' => 'Erro ao adicionar perfil');

    	return Response::json($msg);
        
    }

    public function deleteRoleUser($role,$user)
    {  

    	$user = User::find($user);
    	$role = Role::find($role);

    	if($user->revokeRole($role))
    		$msg = array('status' => true, 'msg' => 'Perfil adicionado com sucesso');
    	else
    		$msg = array('status' => false, 'msg' => 'Erro ao adicionar perfil');

    	return Response::json($msg);

    }

    public function novo()
    {  
    	/*
        $roleAdmin = new Role();
		$roleAdmin->name = 'Super Usuário';
		$roleAdmin->slug = 'super-user';
		$roleAdmin->description = 'Gerencia todas os opções do sistema';
		$roleAdmin->save();
		*/

    }
}