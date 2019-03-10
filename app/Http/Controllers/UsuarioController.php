<?php

namespace App\Http\Controllers;

use App\User;
use App\Nivel;
use App\Entidade;
use App\Identificacao;
use App\EstadoCivil;
use App\TipoFone;
use App\Fone;
use App\Estado;
use App\Cidade;
use App\Endereco;
use App\Banco;
use App\TipoConta;
use App\RegistroBancario;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class UsuarioController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $usuarios = User::where('cd_conta_con', $this->cdContaCon)->orderBy('name')->get();
        
        return view('usuario/usuarios',['usuarios' => $usuarios]);
    }

    public function novo(){

        $niveis      = Nivel::orderBy('dc_nivel_niv')->get();
        $estadoCivis = EstadoCivil::orderBy('nm_estado_civil_esc')->get();
        $tiposFone   = TipoFone::orderBy('dc_tipo_fone_tfo')->get();
        $estados     = Estado::orderBy('nm_estado_est')->get();
        $bancos      = Banco::orderBy('cd_banco_ban')->get();
        $tiposConta  = TipoConta::orderBy('nm_tipo_conta_tcb')->get();

        return view('usuario/novo',['niveis' => $niveis,'estadoCivis' => $estadoCivis,'tiposFone' => $tiposFone,'estados' => $estados,'bancos' => $bancos,'tiposConta' => $tiposConta]);

    }

    public function editar($id){

        $niveis      = Nivel::orderBy('dc_nivel_niv')->get();
        $estadoCivis = EstadoCivil::orderBy('nm_estado_civil_esc')->get();
        $tiposFone   = TipoFone::orderBy('dc_tipo_fone_tfo')->get();
        $estados     = Estado::orderBy('nm_estado_est')->get();
        $bancos      = Banco::orderBy('cd_banco_ban')->get();
        $tiposConta  = TipoConta::orderBy('nm_tipo_conta_tcb')->get();

        $usuario = User::with('entidade')->where('cd_conta_con', $this->cdContaCon)->where('id',$id)->first();

        $usuario->entidade->load('cpf','oab','rg','fone','endereco','banco');

        $usuario->entidade->endereco->load('cidade');

        $usuario->data_nascimento = date('d/m/Y', strtotime($usuario->data_nascimento));

        // dd($usuario);

        return view('usuario/edit',['niveis' => $niveis,'estadoCivis' => $estadoCivis,'tiposFone' => $tiposFone,'estados' => $estados,'bancos' => $bancos,'tiposConta' => $tiposConta, "usuario" => $usuario]);

    }

    public function show($id)
    {
        $vara = Usuario::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(UsuarioRequest $request)
    {

        DB::beginTransaction();

        $entidade = Entidade::create([
            'cd_conta_con'         => $this->cdContaCon,
            'cd_tipo_entidade_tpe' => \TipoEntidade::USUARIO
        ]);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        if($entidade){

            $request->merge(['password' => \Hash::make($request->password)]);

            $request->merge(['data_nascimento' => date('Y-m-d',strtotime(str_replace('/','-',$request->data_nascimento)))]);
            $request->merge(['data_admissao'   => date('Y-m-d',strtotime(str_replace('/','-',$request->data_admissao)))]);
            $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

            $usuario = new user();

            $usuario->fill($request->all());
           
            if($usuario->saveOrFail()){

                if(!empty($request->oab)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                        'nu_identificacao_ide'      => $request->oab
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }                    

                }

                $request->merge(['cpf' => str_replace(array('.','-'),'',$request->cpf)]);

                if(!empty($request->cpf)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::CPF,
                        'nu_identificacao_ide'      => $request->cpf
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->rg)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::RG,
                        'nu_identificacao_ide'      => $request->rg
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->nu_fone_fon) && !empty($request->cd_tipo_fone_tfo)){
                    
                    $fone = Fone::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_fone_tfo'          => $request->cd_tipo_fone_tfo,
                        'nu_fone_fon'               => $request->nu_fone_fon
                    ]);

                    if(!$fone){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->cd_cidade_cde) || !empty($request->nm_bairro_ede) || !empty($request->dc_logradouro_ede)){
                    
                    $endereco = new Endereco();

                    $endereco->fill($request->all());

                    if(!$endereco->saveOrFail()){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->cd_banco_ban) && !empty($request->nu_agencia_dba) && !empty($request->cd_tipo_conta_tcb) && !empty($request->nu_conta_dba)){

                    $registro = new RegistroBancario();

                    $request->merge(['nm_titular_dba'  => $request->name]);
                    $request->merge(['nu_cpf_cnpj_dba' => $request->cpf]);

                    $registro->fill($request->all());
                    //dd($request->all());

                    if(!$registro->saveOrFail()){
                        
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

            }else{
                
                DB::rollBack();
                Flash::error('Erro ao inserir dados');
                return redirect('usuarios');
                  
            }

        }else{

            DB::rollBack();
            Flash::error('Erro ao inserir dados');
            return redirect('usuarios');
        }

        DB::commit();
        Flash::success('Dados inseridos com sucesso');
        return redirect('usuarios');

    }

    public function update(Request $request,$id)
    {
        
        dd($id);
        DB::rollBack();
                Flash::error('Erro ao inserir dados');
                return redirect('usuarios/novo');

        DB::beginTransaction();

        $entidade = Entidade::create([
            'cd_conta_con'         => $this->cdContaCon,
            'cd_tipo_entidade_tpe' => \TipoEntidade::USUARIO
        ]);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        if($entidade){

            $request->merge(['password' => \Hash::make($request->password)]);

            $request->merge(['data_nascimento' => date('Y-m-d',strtotime(str_replace('/','-',$request->data_nascimento)))]);
            $request->merge(['data_admissao'   => date('Y-m-d',strtotime(str_replace('/','-',$request->data_admissao)))]);
            $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

            $usuario = new user();

            $usuario->fill($request->all());
           
            if($usuario->saveOrFail()){

                if(!empty($request->oab)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                        'nu_identificacao_ide'      => $request->oab
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }                    

                }

                $request->merge(['cpf' => str_replace(array('.','-'),'',$request->cpf)]);

                if(!empty($request->cpf)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::CPF,
                        'nu_identificacao_ide'      => $request->cpf
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->rg)){
                    
                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::RG,
                        'nu_identificacao_ide'      => $request->rg
                    ]);

                    if(!$identificacao){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->nu_fone_fon) && !empty($request->cd_tipo_fone_tfo)){
                    
                    $fone = Fone::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_fone_tfo'          => $request->cd_tipo_fone_tfo,
                        'nu_fone_fon'               => $request->nu_fone_fon
                    ]);

                    if(!$fone){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->cd_cidade_cde) || !empty($request->nm_bairro_ede) || !empty($request->dc_logradouro_ede)){
                    
                    $endereco = new Endereco();

                    $endereco->fill($request->all());

                    if(!$endereco->saveOrFail()){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

                if(!empty($request->cd_banco_ban) && !empty($request->nu_agencia_dba) && !empty($request->cd_tipo_conta_tcb) && !empty($request->nu_conta_dba)){

                    $registro = new RegistroBancario();

                    $request->merge(['nm_titular_dba'  => $request->name]);
                    $request->merge(['nu_cpf_cnpj_dba' => $request->cpf]);

                    $registro->fill($request->all());
                    //dd($request->all());

                    if(!$registro->saveOrFail()){
                        
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('usuarios');
                    }   

                }

            }else{
                
                DB::rollBack();
                Flash::error('Erro ao inserir dados');
                return redirect('usuarios');
                  
            }

        }else{

            DB::rollBack();
            Flash::error('Erro ao inserir dados');
            return redirect('usuarios');
        }

        DB::commit();
        Flash::success('Dados inseridos com sucesso');
        return redirect('usuarios');

    }

    public function destroy($id)
    {
        $usuario = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if($usuario->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}