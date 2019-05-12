<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Hash;
use App\User;
use App\Fone;
use App\Conta;
use App\Estado;
use App\Entidade;
use App\TipoFone;
use App\Enums\Nivel;
use App\Enums\Roles;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ContaRequest;
use Kodeine\Acl\Models\Eloquent\Role;
use Illuminate\Support\Facades\Session;

class ContaController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function detalhes($id)
    {
        //Verifica se o usuário logado é o mesmo que requisitou os dados
        if(Auth::user()->cd_conta_con != $id){ 
            return redirect('erro-permissao');
        }

        $conta     = Conta::where('cd_conta_con',$id)->first();
        $usuarios  = User::where('cd_conta_con',$id)->get();

        return view('conta/detalhes',['conta' => $conta, 'usuarios' => $usuarios]);

    }

    public function editar($id)
    {
        //Verifica se o usuário logado é o mesmo que requisitou os dados
        if(Auth::user()->cd_conta_con != $id){ 
            return redirect('erro-permissao');
        }

        $conta   = Conta::with('entidade')->where('cd_conta_con',$id)->first();
        $estados = Estado::orderBy('nm_estado_est')->get();
        $tiposFone = TipoFone::orderBy('dc_tipo_fone_tfo')->get();

        return view('conta/editar',['conta' => $conta, 'estados' => $estados, 'tiposFone' => $tiposFone]);

    }

    public function store(ContaRequest $request)
    {

        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];

        	$conta = new Conta();        
            $conta->fill($request->all());
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTA;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::ADMIN;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    $role = Role::find(Roles::ADMINISTRADOR);
                    $user->assignRole($role);

                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }

    public function update(Request $request)
    {
        $input = $request->all();

        DB::transaction(function() use ($input){            

            $conta = Conta::where('cd_conta_con',$input['cd_conta_con'])->first();        
            $conta->nm_fantasia_con = $input['nm_fantasia_con'];
            $conta->nm_razao_social_con = $input['nm_razao_social_con'];
            $conta->cd_tipo_pessoa_tpp = $input['cd_tipo_pessoa_tpp'];

            $conta->saveOrFail();

            Flash::success('Dados inseridos com sucesso');
            
        });
        return redirect('conta/detalhes/'.$input['cd_conta_con']);
    }

    public function adicionarTelefone(Request $request)
    {
        $input = $request->all();
        $cd_entidade_ete = Auth::user()->cd_entidade_ete;

        $fone = Fone::create([
            'cd_entidade_ete'           => $cd_entidade_ete,
            'cd_conta_con'              => $request->cd_conta_con, 
            'cd_tipo_fone_tfo'          => $request->cd_tipo_fone_tfo,
            'nu_fone_fon'               => $request->nu_fone_fon
        ]);
    }
}