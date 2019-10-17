<?php

namespace App\Http\Controllers;

use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kodeine\Acl\Models\Eloquent\User;
use Kodeine\Acl\Models\Eloquent\Role;
use Kodeine\Acl\Models\Eloquent\Permission;

class PermissaoController extends Controller
{
    
    private $user;
    private $conta;
    private $entidade;

    public function __construct()
    {
        $this->middleware('auth');
        $this->user = Auth::user();
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }

    public function index()
    {  
        return view('permissoes/permissoes',['permissoes' => Permission::all()]);
    }

    public function users()
    {  
        return view('permissoes/users',['users' => User::with('roles')->where('cd_conta_con', $this->conta)->get()]);
    }

    public function permissaoUsuario($id)
    {

        $user = User::where('id',$id)->first();

        $user->addPermission('index.agenda', true);
        $user->addPermission('index.calendario', true);
        $user->addPermission('index.cliente', true);
        $user->addPermission('index.correspondente', true);
        $user->addPermission('index.processo', true);

        Flash::success('Permissões atualizadas com sucesso');

        return redirect('users')->withInput();

    }

    public function atribuirRole(){

        $user = User::where('id',$this->user->id)->first();

        $role = Role::find(1);

        $user->assignRole($role);

    }

    public function atribuirPermissao()
    {
        $user = User::where('id',$this->user->id)->first();

        $role = Role::find(1);
        $role->assignPermission(Permission::all());
        
    }

    public function adicionar()
    {
        
        $permission = new Permission();
        $permPost = $permission->create([ 
            'name'        => 'agenda',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Permissões de agenda'
        ]);        

        $permPost = Permission::create([ 
            'name'        => 'agenda',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Permissões de agenda'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'calendario',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Permissões de calendário'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'cliente',
            'slug'        => [          // pass an array of permissions.
                'novo'     => true,
                'listar'     => true,
                'index'     => true
            ],
            'description' => 'Permissões de calendário'
        ]);         

        $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'buscar'     => true,
                'categorias'     => true,
                'novo' => true,
                'meus-correspondentes' => true,
                'relatorios' => true
                
            ],
            'description' => 'Permissões de correspondentes'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'usuario',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'novo'     => true,
                'listar'     => true
                
            ],
            'description' => 'Permissões de usuários'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'novo'     => true,
                'listar'     => true,
                'acompanhamento' => true,
                'relatorios' => true
                
            ],
            'description' => 'Permissões de usuários'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'financeiro',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'entradas'     => true,
                'saidas'     => true,
                'balanco' => true
                
            ],
            'description' => 'Permissões do financeiro'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'despesas',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'novo'     => true,
                'lancamentos'     => true
                
            ],
            'description' => 'Permissões de despesas'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'configuracoes',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Permissões de configurações'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'permissoes',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                'perfis' => true,
                'permissoes' => true,
                'usuarios' =>true
                
            ],
            'description' => 'Controle de permissões'
        ]);

        
    }

}