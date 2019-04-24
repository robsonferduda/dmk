<?php

namespace App\Http\Controllers;

use App\User;
use App\Entidade;
use App\Vara;
use App\Estado;
use App\Cidade;
use App\TipoProcesso;
use App\Processo;
use App\TipoDespesa;
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
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $processos = Processo::where('cd_conta_con', $this->cdContaCon)->orderBy('nu_processo_pro')->get();

        return view('processo/processos',['processos' => $processos]);
    }

    public function financas($id){

        $processo = Processo::where('cd_conta_con',$this->cdContaCon)->where('cd_processo_pro',$id)->first();
        $despesasReembolsaveis = $processo->cliente->reembolsoTipoDespesa->pluck('cd_tipo_despesa_tds')->toArray();

        dd($despesasReembolsaveis);

        // if(in_array(16, $despesasReembolsaveis)){
        //     dd('passou16');
        // }
        // if(in_array(14, $despesasReembolsaveis)){
        //     dd('passou14');
        // }


        $despesas = TipoDespesa::where('cd_conta_con',$this->cdContaCon)->get();

        return view('processo/financas',['despesas' => $despesas]);

    }


    public function detalhes($id){

        $processo = Processo::where('cd_processo_pro',$id)->where('cd_conta_con',$this->cdContaCon)->first();
    
        return view('processo/detalhes',['processo' => $processo]);
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

        $estados       = Estado::orderBy('nm_estado_est')->get();
        $varas         = Vara::orderBy('nm_vara_var')->get();  
        $tiposProcesso = TipoProcesso::orderBy('nm_tipo_processo_tpo')->get();

        $processo = Processo::with('cliente')->with('cidade')->where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();

        if(!empty($processo->cliente->nm_fantasia_cli)){
                $nome =  $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli.' ('.$processo->cliente->nm_fantasia_cli.')';
        }else{
                $nome = $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli;
        }

        if(!empty($processo->dt_audiencia_pro))
            $processo->dt_audiencia_pro = date('d/m/Y', strtotime($processo->dt_audiencia_pro));
        
        if(!empty($processo->dt_prazo_fatal_pro))
            $processo->dt_prazo_fatal_pro = date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro));


        return view('processo/edit',['estados' => $estados, 'varas' => $varas, 'tiposProcesso' => $tiposProcesso, 'processo' => $processo, 'nome' => $nome]);

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

        $request->merge(['dt_audiencia_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_audiencia_pro)))]);
        $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_prazo_fatal_pro)))]);
    
        $processo = Processo::where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();
        $processo->fill($request->all());

        if(!$processo->saveOrFail()){
          
            DB::rollBack();
            Flash::error('Erro ao atualizar dados');
            return redirect('processos');
        }    
         
        DB::commit();
        Flash::success('Dados inseridos com sucesso');
        return redirect('processos');


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