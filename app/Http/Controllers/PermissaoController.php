<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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

        $id = \Crypt::decrypt($id);
        $user = User::where('id',$id)->first();

        $ids = array();
        foreach ($user->permissao()->get() as $key => $value) {
            $ids[] = $value->id;
        }
        
        $permissoes_disponiveis = Permission::whereNotIn('id',$ids)->get();
        $permissoes = $user->permissao()->get();

        return view('permissoes/user-permission',['user' => $user, 'permissoes' => $permissoes, 'permissoes_disponiveis' => $permissoes_disponiveis]);
    }

    public function atribuirRole(){

        //Role administrador
        $role = Role::find(1);
        dd(Permission::all());
        foreach (Permission::all() as $p) {
            
            $role->assignPermission($p);

        }

        //Role Colaborador
        $role = Role::find(2);
        $array_permissoes = array(31,32,33,34,35,36,37,38,39,40,41,45,46,47,48,49,57);

        foreach (Permission::whereIn('id',$array_permissoes)->get() as $p) {
            
            $role->assignPermission($p);

        }
        

    }

    public function atribuirPermissao($permissao, $usuario)
    {
        $permissao = \Crypt::decrypt($permissao);
        $usuario = \Crypt::decrypt($usuario);

        $user = User::where('id',$usuario)->first();

        if($user){
            $user->permissao()->attach($permissao);
            Flash::success('Permissão adicionada com sucesso');

        }else{
             Flash::error('Usuário não encontrado');
        }

        return redirect('permissoes/usuario/'.\Crypt::encrypt($usuario));
        
    }

    public function revogarPermissao($permissao, $usuario)
    {
        $permissao = \Crypt::decrypt($permissao);
        $usuario = \Crypt::decrypt($usuario);

        $user = User::where('id',$usuario)->first();

        if($user){
            $user->permissao()->detach($permissao);
            Flash::success('Permissão removida com sucesso');

        }else{
             Flash::error('Usuário não encontrado');
        }

        return redirect('permissoes/usuario/'.\Crypt::encrypt($usuario));
        
    }

    public function adicionar()
    {     

        /*
            select * from public.permissions

            delete from public.permissions

            truncate table public.permissions

            select * from public.permission_user

            truncate table public.permission_user

            select * from public.permission_role

            truncate table  public.permission_role

        */

        $permPost = Permission::create([ 
            'name'        => 'agenda',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Agenda'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'calendario',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Calendário'
        ]);

        $indexCliente = Permission::create([ 
            'name'        => 'cliente',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
            ],
            'description' => 'Cliente'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'cliente',
            'slug'        => [          // pass an array of permissions.
                'novo'     => true
            ],
            'description' => 'Cliente > Novo'
        ]);  

        $permPost = Permission::create([ 
            'name'        => 'cliente',
            'slug'        => [          // pass an array of permissions.
                'listar'     => true
            ],
            'description' => 'Cliente > Listar'
        ]);   

        $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
                'index'     => true,
                
            ],
            'description' => 'Correspondentes'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
                
                'buscar'     => true
                
            ],
            'description' => 'Correspondentes > Buscar'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
                
                'categorias'     => true
                
            ],
            'description' => 'Correspondentes > Categorias'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
               
                'novo' => true
               
                
            ],
            'description' => 'Correspondentes > Novo'
        ]);

            $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
               
                
                'meus-correspondentes' => true
                
            ],
            'description' => 'Correspondentes > Meus Correspondentes'
        ]);

             $permPost = Permission::create([ 
            'name'        => 'correspondente',
            'slug'        => [          // pass an array of permissions.
                
                'relatorios' => true
                
            ],
            'description' => 'Correspondentes > Relatórios'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'usuario',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Usuários'
        ]);

          $permPost = Permission::create([ 
            'name'        => 'usuario',
            'slug'        => [          // pass an array of permissions.
                
                'novo'     => true
                
            ],
            'description' => 'Usuário > Novo'
        ]);

            $permPost = Permission::create([ 
            'name'        => 'usuario',
            'slug'        => [          // pass an array of permissions.
                
                'listar'     => true
                
            ],
            'description' => 'Usuário > Listar'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Processos'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                
                'novo'     => true,
                
            ],
            'description' => 'Processos > Novo'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                
                'listar'     => true
                
            ],
            'description' => 'Processos Listar'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                
                'acompanhamento' => true
                
            ],
            'description' => 'Processos > Acompanhamento'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'processo',
            'slug'        => [          // pass an array of permissions.
                
                'relatorios' => true
                
            ],
            'description' => 'Processos > Relatórios'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'financeiro',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Financeiro'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'financeiro',
            'slug'        => [          // pass an array of permissions.
                
                'entradas'     => true
                
            ],
            'description' => 'Financeiro > Entradas'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'financeiro',
            'slug'        => [          // pass an array of permissions.
                
                'saidas'     => true
                
                
            ],
            'description' => 'Financeiro > Saídas'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'financeiro',
            'slug'        => [          // pass an array of permissions.
               
                'balanco' => true
                
            ],
            'description' => 'Financeiro > Balanço'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'despesas',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Despesas'
        ]);

         $permPost = Permission::create([ 
            'name'        => 'despesas',
            'slug'        => [          // pass an array of permissions.
                
                'novo'     => true
                
            ],
            'description' => 'Despesas > Novo'
        ]);

         $permPost = Permission::create([ 
            'name'        => 'despesas',
            'slug'        => [          // pass an array of permissions.
                
                'lancamentos'     => true
                
            ],
            'description' => 'Despesas > Lançamentos'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'configuracoes',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Configurações'
        ]);

        $permPost = Permission::create([ 
            'name'        => 'permissoes',
            'slug'        => [          // pass an array of permissions.
                'index'     => true
                
            ],
            'description' => 'Permissões'
        ]);


        $permPost = Permission::create([ 
            'name'        => 'permissoes',
            'slug'        => [          // pass an array of permissions.
                
                'perfis' => true
                
            ],
            'description' => 'Permissões > Perfis'
        ]);


        $permPost = Permission::create([ 
            'name'        => 'permissoes',
            'slug'        => [          // pass an array of permissions.
               
                'permissoes' => true
                
            ],
            'description' => 'Permissões > Listar'
        ]);


        $permPost = Permission::create([ 
            'name'        => 'permissoes',
            'slug'        => [          // pass an array of permissions.
                'usuarios' =>true
                
            ],
            'description' => 'Permissões > Usuários'
        ]);
        
    }

}