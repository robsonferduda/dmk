<?php

namespace App\Http\Controllers;

use App\User;
use App\Entidade;
use App\Vara;
use App\Estado;
use App\Cidade;
use App\TipoProcesso;
use App\Processo;
use App\ProcessoDespesa;
use App\TipoDespesa;
use App\TipoServico;
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

    public function salvarDespesas(Request $request){

        $processo_id = $request->processo;
       
        DB::beginTransaction();

        $dados = json_decode($request->valores);

        foreach ($dados as $dado) {


            if(empty($dado->valor)){
                $dado->valor = NULL;
            }else{
                $dado->valor = str_replace(",", ".", $dado->valor);
            }


            //dd($dado);

            if($dado->entidade == 'cliente'){
                $tipoEntidade = \TipoEntidade::CLIENTE;             
            }else{
                $tipoEntidade = \TipoEntidade::CORRESPONDENTE;
            }
           
            $valor = ProcessoDespesa::where('cd_conta_con',$this->cdContaCon)
                                      ->where('cd_processo_pro',$processo_id)
                                      ->where('cd_tipo_despesa_tds',$dado->despesa)
                                      ->where('cd_tipo_entidade_tpe',$tipoEntidade)->first(); 

            if(!empty($valor)){

                $valor->vl_processo_despesa_pde = $dado->valor;
                $valor->fl_despesa_reembolsavel_pde = $dado->reembolso;
                if(!$valor->saveOrFail()){
                    Flash::error('Erro ao atualizar dados');
                    DB::rollBack();
                    return redirect('processos/financas/'.$processo_id);    
                }

            }else{

                $valor = ProcessoDespesa::create([
                    'cd_conta_con' => $this->cdContaCon,
                    'cd_processo_pro' => $processo_id,
                    'cd_tipo_despesa_tds' => $dado->despesa,
                    'cd_tipo_entidade_tpe' => $tipoEntidade,
                    'fl_despesa_reembolsavel_pde' => $dado->reembolso,
                    'vl_processo_despesa_pde' => $dado->valor
                ]);

                if(!$valor){
                    Flash::error('Erro ao atualizar dados');
                    DB::rollBack();
                    return redirect('processos/financas/'.$processo_id);    
                }
            }            
        }
                   
    
        Flash::success('Dados atualizados com sucesso');
        DB::commit(); 
 
        return redirect('processos/financas/'.$processo_id);       

    }

    public function financas($id){

        $processo = Processo::where('cd_conta_con',$this->cdContaCon)->where('cd_processo_pro',$id)->first();
     
        $despesas = DB::table('processo_pro')
                    ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                    ->join('tipo_despesa_tds','processo_pro.cd_conta_con','=','tipo_despesa_tds.cd_conta_con')
                    ->leftjoin('reembolso_tipo_despesa_rtd', function($join){
                               $join->on('cliente_cli.cd_entidade_ete', '=', 'reembolso_tipo_despesa_rtd.cd_entidade_ete');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'reembolso_tipo_despesa_rtd.cd_tipo_despesa_tds');                            
                     })
                    ->leftjoin('processo_despesa_pde', function($join){
                               $join->on('processo_pro.cd_processo_pro', '=', 'processo_despesa_pde.cd_processo_pro');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'processo_despesa_pde.cd_tipo_despesa_tds');                            
                     })
                    ->where('processo_pro.cd_processo_pro',$id)
                    ->where('processo_pro.cd_conta_con',$this->cdContaCon)
                    ->whereNull('tipo_despesa_tds.deleted_at')
                    ->orderBy('tipo_despesa_tds.nm_tipo_despesa_tds')
                    ->select('tipo_despesa_tds.cd_tipo_despesa_tds',
                             'tipo_despesa_tds.nm_tipo_despesa_tds',
                             DB::raw("coalesce(reembolso_tipo_despesa_rtd.fl_reembolso_tipo_despesa_rtd,'N') as fl_reembolsavel_cliente"),
                             DB::raw("coalesce(processo_despesa_pde.fl_despesa_reembolsavel_pde,'N') as fl_reembolsavel_processo"),
                             'processo_despesa_pde.cd_tipo_entidade_tpe',
                             'processo_despesa_pde.vl_processo_despesa_pde'
                            )
                    ->get();

        //$tiposDeServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->get();

        $tiposDeServico = DB::table('processo_pro')
                          ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                          ->join('tipo_servico_tse','processo_pro.cd_conta_con','=','tipo_servico_tse.cd_conta_con')
                          ->leftjoin('taxa_honorario_entidade_the', function($join){
                               $join->on('cliente_cli.cd_entidade_ete', '=', 'taxa_honorario_entidade_the.cd_entidade_ete');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'taxa_honorario_entidade_the.cd_tipo_servico_tse');
                               $join->on('processo_pro.cd_cidade_cde', '=', 'taxa_honorario_entidade_the.cd_cidade_cde');
                          })
                          ->where('processo_pro.cd_processo_pro',$id)
                          ->where('processo_pro.cd_conta_con',$this->cdContaCon)
                          ->whereNull('tipo_servico_tse.deleted_at')
                          ->orderBy('tipo_servico_tse.nm_tipo_servico_tse')
                          ->select('tipo_servico_tse.cd_tipo_servico_tse','tipo_servico_tse.nm_tipo_servico_tse','taxa_honorario_entidade_the.nu_taxa_the')->get();
       
        return view('processo/financas',['despesas' => $despesas,'tiposDeServico' => $tiposDeServico,'id' => $id]);

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