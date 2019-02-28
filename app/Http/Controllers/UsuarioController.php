<?php

namespace App\Http\Controllers;

use App\User;
use App\Nivel;
use App\Entidade;
use App\Identificacao;
use App\EstadoCivil;
use App\TipoFone;
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

        return view('usuario/novo',['niveis' => $niveis,'estadoCivis' => $estadoCivis,'tiposFone' => $tiposFone]);

    }

    public function show($id)
    {
        $vara = Usuario::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(Request $request)
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
        $vara = Usuario::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/usuarios');
    }

    public function destroy($id)
    {
        $vara = Usuario::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}