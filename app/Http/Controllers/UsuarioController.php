<?php

namespace App\Http\Controllers;

use Auth;
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
use App\Departamento;
use App\Cargo;
use App\Http\Requests\UsuarioRequest;
use App\Http\Requests\UsuarioSenhaRequest;
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
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {        

        $usuarios = User::with('tipoPerfil')->where('cd_conta_con', $this->cdContaCon)->where('cd_nivel_niv','!=',1)->orderBy('name')->get();

        return view('usuario/usuarios',['usuarios' => $usuarios]);
    }

    public function detalhes($id)
    {

        $id = \Crypt::decrypt($id);

        $usuario = User::where('cd_conta_con', $this->cdContaCon)->where('id',$id)->first();

        return view('usuario/detalhes',['usuario' => $usuario]);
    }

    public function buscar(Request $request)
    {
        $nome   = $request->get('nome');
        $perfil = $request->get('perfil');

        $usuarios = User::with('tipoPerfil')->where('cd_conta_con', $this->cdContaCon);
        if(!empty($nome))   $usuarios->where('name','ilike',"%$nome%");
        if(!empty($perfil)) $usuarios->where('cd_nivel_niv',$perfil);
        $usuarios = $usuarios->orderBy('name')->get();

         return view('usuario/usuarios',['usuarios' => $usuarios,'nome' => $nome, 'perfil' => $perfil]);
    }

    public function novo(){

        Auth::user()->addPermission('user'); 
        
        $niveis        = Nivel::where('fl_nivel_conta_niv','S')->orderBy('dc_nivel_niv')->get();
        $estadoCivis   = EstadoCivil::orderBy('nm_estado_civil_esc')->get();
        $tiposFone     = TipoFone::orderBy('dc_tipo_fone_tfo')->get();
        $estados       = Estado::orderBy('nm_estado_est')->get();
        $bancos        = Banco::orderBy('cd_banco_ban')->get();
        $tiposConta    = TipoConta::orderBy('nm_tipo_conta_tcb')->get();
        $departamentos = Departamento::orderBy('nm_departamento_dep')->get();
        $cargos         = Cargo::orderBy('nm_cargo_car')->get();

        return view('usuario/novo',['niveis' => $niveis,'estadoCivis' => $estadoCivis,'tiposFone' => $tiposFone,'estados' => $estados,'bancos' => $bancos,'tiposConta' => $tiposConta, 'departamentos' => $departamentos, 'cargos' => $cargos]);

    }

    public function editar($id){

        $id = \Crypt::decrypt($id);

        $niveis        = Nivel::where('fl_nivel_conta_niv','S')->orderBy('dc_nivel_niv')->get();
        $estadoCivis   = EstadoCivil::orderBy('nm_estado_civil_esc')->get();
        $tiposFone     = TipoFone::orderBy('dc_tipo_fone_tfo')->get();
        $estados       = Estado::orderBy('nm_estado_est')->get();
        $bancos        = Banco::orderBy('cd_banco_ban')->get();
        $tiposConta    = TipoConta::orderBy('nm_tipo_conta_tcb')->get();
        $departamentos = Departamento::orderBy('nm_departamento_dep')->get();
        $cargos        = Cargo::orderBy('nm_cargo_car')->get();

        $usuario = User::with('entidade')->where('cd_conta_con', $this->cdContaCon)->where('id',$id)->first();

        $usuario->entidade->load('cpf','oab','rg','fone','endereco','banco');

        if(empty($usuario->entidade->fone))
            $usuario->entidade->fone = new Fone();

        if(empty($usuario->entidade->oab))
            $usuario->entidade->oab = new Identificacao();

        if(empty($usuario->entidade->cpf))
            $usuario->entidade->cpf = new Identificacao();

        if(empty($usuario->entidade->rg))
            $usuario->entidade->rg = new Identificacao();

        if(empty($usuario->entidade->endereco))
            $usuario->entidade->endereco = new Endereco();

        if(empty($usuario->entidade->banco))
            $usuario->entidade->banco = new RegistroBancario();


        $usuario->entidade->endereco->load('cidade');

         if(empty($usuario->entidade->endereco->cidade))
            $usuario->entidade->endereco->cidade = new Cidade();


        if(!empty($usuario->data_nascimento))
            $usuario->data_nascimento = date('d/m/Y', strtotime($usuario->data_nascimento));
        
        if(!empty($usuario->data_admissao))
            $usuario->data_admissao = date('d/m/Y', strtotime($usuario->data_admissao));

        return view('usuario/edit',['niveis' => $niveis,'estadoCivis' => $estadoCivis,'tiposFone' => $tiposFone,'estados' => $estados,'bancos' => $bancos,'tiposConta' => $tiposConta, "usuario" => $usuario,'departamentos' => $departamentos, 'cargos' => $cargos]);

    }

    public function show($id)
    {
        
        $usuario = User::where('cd_conta_con', $this->cdContaCon)->where('id',$id)->first();
        return view('usuario/detalhes', ['usuario' => $usuario]);  
    }

    public function store(UsuarioRequest $request)
    {

        DB::beginTransaction();

        $entidade = Entidade::create([
            'cd_conta_con'         => $this->cdContaCon,
            'cd_tipo_entidade_tpe' => \TipoEntidade::USUARIO
        ]);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);
        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);

        $request->merge(['observacao' => $request->observacao_usu]);
        
        if($entidade){

            $request->merge(['password' => \Hash::make($request->password)]);

            $request->merge(['cd_nivel_niv' => \Nivel::COLABORADOR]);

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

                if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                    $fones = json_decode($request->telefones);
                    for($i = 0; $i < count($fones); $i++) {

                        $fone = Fone::create([
                            'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                            'cd_conta_con'              => $this->cdContaCon, 
                            'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                            'nu_fone_fon'               => $fones[$i]->numero
                        ]);

                        if(!$fone){
                            DB::rollBack();
                            Flash::error('Erro ao inserir dados');
                            return redirect('usuarios');
                        }   

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

    public function update(UsuarioRequest $request,$id)
    {
        
        DB::beginTransaction();

        $usuario  = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);
        $request->merge(['cd_entidade_ete' => $usuario->cd_entidade_ete]);
        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);

        $request->merge(['observacao' => $request->observacao_usu]);
        
        if($usuario->cd_entidade_ete){

            if(!empty($request->data_nascimento))
                $request->merge(['data_nascimento' => date('Y-m-d',strtotime(str_replace('/','-',$request->data_nascimento)))]);
            if(!empty($request->data_admissao))
                $request->merge(['data_admissao'   => date('Y-m-d',strtotime(str_replace('/','-',$request->data_admissao)))]);
            
            $usuario->fill($request->all());

            if($usuario->saveOrFail()){

                if(!empty($request->oab)){

                    $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::OAB)->first();

                    if($identificacao){
                    
                        $request->merge(['nu_identificacao_ide' => $request->oab]);
                        $identificacao->fill($request->all());

                        if(!$identificacao->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }     
                    }else{

                        $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $usuario->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                        'nu_identificacao_ide'      => $request->oab
                        ]);

                        if(!$identificacao){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }   

                    }              

                }else{

                    $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::OAB)->first();

                    if($identificacao)
                        if(!$identificacao->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }
                }

                $request->merge(['cpf' => str_replace(array('.','-'),'',$request->cpf)]);

                if(!empty($request->cpf)){
                    
                    $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first();

                    if($identificacao){
                    
                        $request->merge(['nu_identificacao_ide' => $request->cpf]);
                        $identificacao->fill($request->all());

                        if(!$identificacao->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }     
                    }else{

                        $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $usuario->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::CPF,
                        'nu_identificacao_ide'      => $request->cpf
                        ]);

                        if(!$identificacao){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }   

                    }                        

                }else{

                    $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first();

                    if($identificacao)
                        if(!$identificacao->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }
                }

                if(!empty($request->rg)){
                    
                   $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::RG)->first();

                    if($identificacao){
                    
                        $request->merge(['nu_identificacao_ide' => $request->rg]);
                        $identificacao->fill($request->all());

                        if(!$identificacao->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }     
                    }else{

                        $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $usuario->cd_entidade_ete,
                        'cd_conta_con'              => $this->cdContaCon, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::RG,
                        'nu_identificacao_ide'      => $request->rg
                        ]);

                        if(!$identificacao){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }   

                    }        

                }else{

                    $identificacao = Identificacao::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::RG)->first();

                    if($identificacao)
                        if(!$identificacao->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }
                }

                    //Inserção de telefones
                if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                    $fones = json_decode($request->telefones);
                    for($i = 0; $i < count($fones); $i++) {

                        $fone = Fone::create([
                            'cd_entidade_ete'           => $usuario->cd_entidade_ete,
                            'cd_conta_con'              => $this->cdContaCon, 
                            'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                            'nu_fone_fon'               => $fones[$i]->numero
                        ]);

                    }
                }


                if(!empty($request->cd_cidade_cde) || !empty($request->nm_bairro_ede) || !empty($request->dc_logradouro_ede)){

                    $endereco = Endereco::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();

                    if($endereco){
                        
                        $endereco->fill($request->all());

                        if(!$endereco->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        } 

                    }else{

                        $endereco = new Endereco();

                        $endereco->fill($request->all());

                        if(!$endereco->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        } 

                    }
                    
                }else{

                    $endereco = Endereco::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();

                    if($endereco)
                        if(!$endereco->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }

                }

                if(!empty($request->cd_banco_ban) && !empty($request->nu_agencia_dba) && !empty($request->cd_tipo_conta_tcb) && !empty($request->nu_conta_dba)){

                    $request->merge(['nm_titular_dba'  => $request->name]);
                    $request->merge(['nu_cpf_cnpj_dba' => $request->cpf]);

                    $registro = RegistroBancario::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();

                    if($registro){
                        
                        $registro->fill($request->all());

                        if(!$registro->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        } 

                    }else{

                        $registro = new RegistroBancario();

                        $registro->fill($request->all());

                        if(!$registro->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        } 

                    }
                }else{

                    $registro = RegistroBancario::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();

                    if($registro)
                        if(!$registro->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }

                }
                        
            }else{
                
                DB::rollBack();
                Flash::error('Erro ao atualizar dados');
                return redirect('usuarios');
                  
            }

        }else{

            DB::rollBack();
            Flash::error('Erro ao atualizar dados');
            return redirect('usuarios');
        }

        DB::commit();
        Flash::success('Dados Atualizados com sucesso');
        return redirect('usuarios');

    }

    public function alterarSenha(UsuarioSenhaRequest $request,$id){

        DB::beginTransaction();

        $id = \Crypt::decrypt($id);
        
        $usuario  = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['password' => \Hash::make($request->password)]);

        $usuario->fill($request->all());
        if($usuario->saveOrFail()){
        
            DB::commit();
            Flash::success('Senha alterada com sucesso');
            return redirect('usuarios');
        
        }else{
            
            DB::rollBack();
            Flash::error('Erro ao alterar senha');
            return redirect('usuarios');
            
        }


    }

    public function destroy($id)
    {

        $id = \Crypt::decrypt($id);
        
        $usuario = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if($usuario->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}