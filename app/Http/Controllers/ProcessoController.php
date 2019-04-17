<?php

namespace App\Http\Controllers;

use App\User;
use App\Entidade;
use App\Vara;
use App\Estado;
use App\Cidade;
use App\TipoProcesso;
use App\Processo;
use App\Http\Requests\ProcessoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class ProcessoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $processos = Processo::where('cd_conta_con', $this->cdContaCon)->orderBy('nu_processo_pro')->get();
        
        return view('processo/processos',['processos' => $processos]);
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

        $estados       = Estado::orderBy('nm_estado_est')->get();
        $varas         = Vara::orderBy('nm_vara_var')->get();  
        $tiposProcesso = TipoProcesso::orderBy('nm_tipo_processo_tpo')->get();
       
        return view('processo/novo',['estados' => $estados,'varas' => $varas, 'tiposProcesso' => $tiposProcesso]);

    }

    public function editar($id){

        $niveis        = Nivel::orderBy('dc_nivel_niv')->get();
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
        $usuario = User::findOrFail($id); 
        return view('usuario/perfil', ['usuario' => $usuario]);  
    }

    public function store(ProcessoRequest $request)
    {

        DB::beginTransaction();

        $entidade = Entidade::create([
            'cd_conta_con'         => $this->cdContaCon,
            'cd_tipo_entidade_tpe' => \TipoEntidade::PROCESSO
        ]);

        $request->merge(['dt_audiencia_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_audiencia_pro)))]);
        $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_prazo_fatal_pro)))]);
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        if($entidade){

            $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

            $processo = new Processo();
            $processo->fill($request->all());

            if(!$processo->saveOrFail()){
          
               DB::rollBack();
               Flash::error('Erro ao atualizar dados');
               return redirect('processos');
            }    
         
        }else{

            DB::rollBack();
            Flash::error('Erro ao inserir dados');
            return redirect('processos');
        }

        DB::commit();
        Flash::success('Dados inseridos com sucesso');
        return redirect('processos');

    }

    public function update(ProcessoRequest $request,$id)
    {
        
        DB::beginTransaction();

        $usuario  = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);
        $request->merge(['cd_entidade_ete' => $usuario->cd_entidade_ete]);

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

                if(!empty($request->nu_fone_fon) && !empty($request->cd_tipo_fone_tfo)){

                    $fone = Fone::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();
                             
                    if($fone){

                        $fone->fill($request->all());

                        if(!$fone->saveOrFail()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        } 
                    }else{

                        $fone = Fone::create([
                            'cd_entidade_ete'           => $usuario->cd_entidade_ete,
                            'cd_conta_con'              => $this->cdContaCon, 
                            'cd_tipo_fone_tfo'          => $request->cd_tipo_fone_tfo,
                            'nu_fone_fon'               => $request->nu_fone_fon
                        ]);

                        if(!$fone){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
                        }   
                    }

                }else{

                    $fone = Fone::where('cd_conta_con',$this->cdContaCon)->where('cd_entidade_ete',$usuario->cd_entidade_ete)->first();

                    if($fone)
                        if(!$fone->delete()){
                            DB::rollBack();
                            Flash::error('Erro ao atualizar dados');
                            return redirect('usuarios');
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

    public function destroy($id)
    {
        $usuario = User::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if($usuario->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}