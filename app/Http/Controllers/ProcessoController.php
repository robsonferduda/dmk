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
use App\ProcessoTaxaHonorario;
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

        $processos = Processo::where('cd_conta_con', $this->cdContaCon)->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->get();

        return view('processo/processos',['processos' => $processos]);
    }

     public function acompanhar()
    {

        $processos = Processo::where('cd_conta_con', $this->cdContaCon)->orderBy('nu_processo_pro')->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->get();

        return view('processo/acompanhamento',['processos' => $processos]);
    }

    public function relatorio($id){

        $processo = Processo::where('cd_processo_pro',$id)->where('cd_conta_con',$this->cdContaCon)->first();
    
        $despesasCliente = 0;
        $despesasReembolsaveisCliente = 0;
        $despesasCorrespondente = 0;
        $despesasReembolsaveisCorrespondente = 0;
        $honorarioCliente = 0;
        $honorarioCorrespondente = 0;
        foreach ($processo->tiposDespesa as $despesa) {

            if(!empty($despesa->pivot->vl_processo_despesa_pde) && $despesa->pivot->fl_despesa_reembolsavel_pde == 'N' && $despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE){
                $despesasCliente += $despesa->pivot->vl_processo_despesa_pde;
                continue;
            }

            if(!empty($despesa->pivot->vl_processo_despesa_pde) && $despesa->pivot->fl_despesa_reembolsavel_pde == 'N' && $despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE){
                $despesasCorrespondente += $despesa->pivot->vl_processo_despesa_pde;
                continue;
            }

            if(!empty($despesa->pivot->vl_processo_despesa_pde) && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S' && $despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE){
                $despesasReembolsaveisCliente += $despesa->pivot->vl_processo_despesa_pde;
                continue;
            }

            if(!empty($despesa->pivot->vl_processo_despesa_pde) && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S' && $despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE){
                $despesasReembolsaveisCorrespondente += $despesa->pivot->vl_processo_despesa_pde;
                continue;
            }
        }

        if(!empty($processo->honorario->vl_taxa_honorario_cliente_pth))
            $honorarioCliente = $processo->honorario->vl_taxa_honorario_cliente_pth;            
        
        if(!empty($processo->honorario->vl_taxa_honorario_correspondente_pth))
            $honorarioCorrespondente = $processo->honorario->vl_taxa_honorario_correspondente_pth;


        $entrada = $honorarioCliente + $honorarioCorrespondente;
        $saida   = $despesasCliente + $despesasReembolsaveisCorrespondente;
        $receita = $entrada - $saida;

        //dd($despesasCliente);
        return view('processo/relatorio',['processo' => $processo,
                                          'despesasCliente' => $despesasCliente,
                                          'despesasCorrespondente' => $despesasCorrespondente,
                                          'despesasReembolsaveisCliente' => $despesasReembolsaveisCliente,
                                          'despesasReembolsaveisCorrespondente' => $despesasReembolsaveisCorrespondente,
                                          'honorarioCliente' => $honorarioCliente,
                                          'honorarioCorrespondente' => $honorarioCorrespondente,
                                          'entrada' => $entrada,
                                          'saida' => $saida,
                                          'receita' => $receita]);
    }

/*    public function salvarHonorarios(Request $request){

        $processo_id = $request->processo;
       
        DB::beginTransaction();

        $dados = json_decode($request->valores);

        foreach ($dados as $dado) {

            if(empty($dado->valor)){
                $dado->valor = NULL;
            }else{
                $dado->valor = str_replace(",", ".", $dado->valor);
            }

            if($dado->entidade == 'cliente'){
                $tipoEntidade = \TipoEntidade::CLIENTE;             
            }else{
                $tipoEntidade = \TipoEntidade::CORRESPONDENTE;
            }

            $valor = ProcessoTaxaHonorario::where('cd_conta_con',$this->cdContaCon)
                                      ->where('cd_processo_pro',$processo_id)
                                      ->where('cd_tipo_servico_tse',$dado->servico)
                                      ->where('cd_tipo_entidade_tpe',$tipoEntidade)->first(); 

            if(!empty($valor)){

                $valor->vl_taxa_honorario_pth = $dado->valor;
                if(!$valor->saveOrFail()){
                    Flash::error('Erro ao atualizar dados');
                    DB::rollBack();
                    return redirect('processos/financas/'.$processo_id);    
                }

            }else{

                $valor = ProcessoTaxaHonorario::create([
                    'cd_conta_con' => $this->cdContaCon,
                    'cd_processo_pro' => $processo_id,
                    'cd_tipo_servico_tse' => $dado->servico,
                    'cd_tipo_entidade_tpe' => $tipoEntidade,
                    'vl_taxa_honorario_pth' => $dado->valor
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


    }*/


    public function clonar($id){
        
        $processo = Processo::where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();
        $novoProcesso = $processo->replicate();
        $novoProcesso->save();

        Flash::success('Processo clonado com sucesso');
        DB::commit(); 

        return redirect('processos/editar/'.$novoProcesso->cd_processo_pro);  
    }

    public function salvarHonorarios(Request $request){

        $processo_id = $request->processo;
       
        DB::beginTransaction();

        $dados = json_decode($request->dados);

        if(empty($dados->valor_cliente)){
            $dados->valor_cliente = NULL;
        }else{
            $dados->valor_cliente = str_replace(",", ".", $dados->valor_cliente);
        }

        if(empty($dados->valor_correspondente)){
            $dados->valor_correspondente = NULL;
        }else{
            $dados->valor_correspondente = str_replace(",", ".", $dados->valor_correspondente);
        }

        $valor = ProcessoTaxaHonorario::where('cd_conta_con',$this->cdContaCon)
                                  ->where('cd_processo_pro',$processo_id)->first();
                                
        if(!empty($valor)){

            $valor->vl_taxa_honorario_cliente_pth = $dados->valor_cliente;
            $valor->vl_taxa_honorario_correspondente_pth = $dados->valor_correspondente;
            $valor->cd_tipo_servico_tse = $dados->servico;
           
            if(!$valor->saveOrFail()){
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('processos/financas/'.$processo_id);    
            }

        }else{

            $valor = ProcessoTaxaHonorario::create([
                'cd_conta_con' => $this->cdContaCon,
                'cd_processo_pro' => $processo_id,
                'cd_tipo_servico_tse' => $dados->servico,
                'vl_taxa_honorario_cliente_pth' => $dados->valor_cliente,
                'vl_taxa_honorario_correspondente_pth' => $dados->valor_correspondente
            ]);

            if(!$valor){
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('processos/financas/'.$processo_id);    
            }
        }       

        Flash::success('Dados atualizados com sucesso');
        DB::commit(); 
 
        return redirect('processos/financas/'.$processo_id);      


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

        //$processo = Processo::where('cd_conta_con',$this->cdContaCon)->where('cd_processo_pro',$id)->first();
     
        $despesas = DB::table('processo_pro')
                    ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                    ->leftjoin('conta_con','processo_pro.cd_correspondente_cor', '=', 'conta_con.cd_conta_con')
                    ->leftjoin('entidade_ete','conta_con.cd_conta_con', '=', 'entidade_ete.cd_conta_con')
                    ->join('tipo_despesa_tds','processo_pro.cd_conta_con','=','tipo_despesa_tds.cd_conta_con')
                    ->leftjoin('reembolso_tipo_despesa_rtd as reembolso_correspondente', function($join){
                               $join->on('entidade_ete.cd_entidade_ete', '=', 'reembolso_correspondente.cd_entidade_ete');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'reembolso_correspondente.cd_tipo_despesa_tds');
                               $join->whereNull('reembolso_correspondente.deleted_at');                            
                     })                   
                    ->leftjoin('reembolso_tipo_despesa_rtd as reembolso_cliente', function($join){
                               $join->on('cliente_cli.cd_entidade_ete', '=', 'reembolso_cliente.cd_entidade_ete');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'reembolso_cliente.cd_tipo_despesa_tds');
                               $join->whereNull('reembolso_cliente.deleted_at');                          
                     })
                    ->leftjoin('processo_despesa_pde as processo_despesa_pde_cliente', function($join){
                               $join->on('processo_pro.cd_processo_pro', '=', 'processo_despesa_pde_cliente.cd_processo_pro');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'processo_despesa_pde_cliente.cd_tipo_despesa_tds');
                               $join->where('processo_despesa_pde_cliente.cd_tipo_entidade_tpe', '=', \TipoEntidade::CLIENTE);                            
                     })
                    ->leftjoin('processo_despesa_pde as processo_despesa_pde_correspondente', function($join){
                               $join->on('processo_pro.cd_processo_pro', '=', 'processo_despesa_pde_correspondente.cd_processo_pro');
                               $join->on('tipo_despesa_tds.cd_tipo_despesa_tds', '=', 'processo_despesa_pde_correspondente.cd_tipo_despesa_tds');
                               $join->where('processo_despesa_pde_correspondente.cd_tipo_entidade_tpe', '=', \TipoEntidade::CORRESPONDENTE);                            
                     })
                    ->where('processo_pro.cd_processo_pro',$id)
                    ->where('processo_pro.cd_conta_con',$this->cdContaCon)
                    ->whereNull('tipo_despesa_tds.deleted_at')
                    ->orderBy('tipo_despesa_tds.nm_tipo_despesa_tds')
                    ->select('tipo_despesa_tds.cd_tipo_despesa_tds',
                             'tipo_despesa_tds.nm_tipo_despesa_tds',
                             DB::raw("coalesce(reembolso_cliente.fl_reembolso_tipo_despesa_rtd,'N') as fl_reembolsavel_cliente"),
                             DB::raw("coalesce(reembolso_correspondente.fl_reembolso_tipo_despesa_rtd,'N') as fl_reembolsavel_correspondente"),
                             DB::raw("coalesce(processo_despesa_pde_cliente.fl_despesa_reembolsavel_pde,'N') as fl_reembolsavel_processo_cliente"),
                             DB::raw("coalesce(processo_despesa_pde_correspondente.fl_despesa_reembolsavel_pde,'N') as fl_reembolsavel_processo_correspondente"),
                             'processo_despesa_pde_cliente.vl_processo_despesa_pde as vl_despesa_cliente',
                             'processo_despesa_pde_correspondente.vl_processo_despesa_pde as vl_despesa_correspondente'
                            )
                    ->get();

        $tiposDeServico = DB::table('processo_pro')
                          ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                          ->join('tipo_servico_tse','processo_pro.cd_conta_con','=','tipo_servico_tse.cd_conta_con')
                          ->leftjoin('taxa_honorario_entidade_the as taxa_honorario_cliente', function($join){
                               $join->on('cliente_cli.cd_entidade_ete', '=', 'taxa_honorario_cliente.cd_entidade_ete');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'taxa_honorario_cliente.cd_tipo_servico_tse');
                               $join->on('processo_pro.cd_cidade_cde', '=', 'taxa_honorario_cliente.cd_cidade_cde');
                          })   
                          ->leftjoin('conta_con','processo_pro.cd_correspondente_cor', '=', 'conta_con.cd_conta_con')   
                          ->leftjoin('entidade_ete','conta_con.cd_conta_con', '=', 'entidade_ete.cd_conta_con')       
                          ->leftjoin('taxa_honorario_entidade_the as taxa_honorario_correspondente', function($join){
                               $join->on('entidade_ete.cd_entidade_ete', '=', 'taxa_honorario_correspondente.cd_entidade_ete');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'taxa_honorario_correspondente.cd_tipo_servico_tse');
                               $join->on('processo_pro.cd_cidade_cde', '=', 'taxa_honorario_correspondente.cd_cidade_cde');
                          })           
                          ->where('processo_pro.cd_processo_pro',$id)
                          ->where('processo_pro.cd_conta_con',$this->cdContaCon)
                          ->whereNull('tipo_servico_tse.deleted_at')
                          ->orderBy('tipo_servico_tse.nm_tipo_servico_tse')
                          ->select('tipo_servico_tse.cd_tipo_servico_tse',
                                   'tipo_servico_tse.nm_tipo_servico_tse',
                                   'taxa_honorario_cliente.nu_taxa_the as nu_taxa_the_cliente',
                                   'taxa_honorario_correspondente.nu_taxa_the as nu_taxa_the_correspondente'
                                  
                               )
                          ->get();
        #dd($tiposDeServico);
        $honorariosProcesso = ProcessoTaxaHonorario::where('cd_conta_con', $this->cdContaCon)
                                                   ->where('cd_processo_pro',$id)
                                                   ->orderBy('updated_at','DESC')->first();
                                                
        /*$qtdProcessoTiposServicoCliente = ProcessoTaxaHonorario::where('cd_conta_con',$this->cdContaCon)
                                                                ->where('cd_processo_pro',$id)
                                                                ->where('cd_tipo_entidade_tpe',\TipoEntidade::CLIENTE)->count();
        
        $qtdProcessoTiposServicoCorrespondente = ProcessoTaxaHonorario::where('cd_conta_con',$this->cdContaCon)
                                                                ->where('cd_processo_pro',$id)
                                                                ->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE)->count();  */                                                                                             
        /*$tiposDeServico = DB::table('processo_pro')
                          ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                          ->join('tipo_servico_tse','processo_pro.cd_conta_con','=','tipo_servico_tse.cd_conta_con')
                          ->leftjoin('taxa_honorario_entidade_the', function($join){
                               $join->on('cliente_cli.cd_entidade_ete', '=', 'taxa_honorario_entidade_the.cd_entidade_ete');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'taxa_honorario_entidade_the.cd_tipo_servico_tse');
                               $join->on('processo_pro.cd_cidade_cde', '=', 'taxa_honorario_entidade_the.cd_cidade_cde');
                          })                    
                          ->leftjoin('processo_taxa_honorario_pth as processo_taxa_honorario_pth_cli', function($join){
                               $join->on('processo_pro.cd_processo_pro', '=', 'processo_taxa_honorario_pth_cli.cd_processo_pro');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'processo_taxa_honorario_pth_cli.cd_tipo_servico_tse');
                               $join->where('processo_taxa_honorario_pth_cli.cd_tipo_entidade_tpe', '=', \TipoEntidade::CLIENTE);
                          })
                          ->leftjoin('processo_taxa_honorario_pth as processo_taxa_honorario_pth_cor', function($join){
                               $join->on('processo_pro.cd_processo_pro', '=', 'processo_taxa_honorario_pth_cor.cd_processo_pro');
                               $join->on('tipo_servico_tse.cd_tipo_servico_tse', '=', 'processo_taxa_honorario_pth_cor.cd_tipo_servico_tse');
                               $join->where('processo_taxa_honorario_pth_cor.cd_tipo_entidade_tpe', '=', \TipoEntidade::CORRESPONDENTE);
                          })
                          ->where('processo_pro.cd_processo_pro',$id)
                          ->where('processo_pro.cd_conta_con',$this->cdContaCon)
                          ->whereNull('tipo_servico_tse.deleted_at')
                          ->orderBy('tipo_servico_tse.nm_tipo_servico_tse')
                          ->select('tipo_servico_tse.cd_tipo_servico_tse',
                                   'tipo_servico_tse.nm_tipo_servico_tse',
                                   'taxa_honorario_entidade_the.nu_taxa_the',
                                   'processo_taxa_honorario_pth_cli.vl_taxa_honorario_pth as vl_taxa_honorario_pth_cliente',
                                   'processo_taxa_honorario_pth_cor.vl_taxa_honorario_pth as vl_taxa_honorario_pth_correspondente'
                               )
                          ->get();*/
        
        /*return view('processo/financas',['despesas' => $despesas,
                                         'tiposDeServico' => $tiposDeServico,
                                         'id' => $id,
                                         'qtdServicoCliente' => $qtdProcessoTiposServicoCliente,
                                         'qtdServicoCorrespondente' => $qtdProcessoTiposServicoCorrespondente
                                        ]);*/

        return view('processo/financas',['despesas' => $despesas,
                                         'tiposDeServico' => $tiposDeServico,
                                         'id' => $id,
                                         'honorariosProcesso' => $honorariosProcesso
                                        ]);

    }


    public function detalhes($id){

        $processo = Processo::where('cd_processo_pro',$id)->where('cd_conta_con',$this->cdContaCon)->first();
    
        return view('processo/detalhes',['processo' => $processo]);
    }

    public function buscar(Request $request)
    {
     
        $numero   = $request->get('nu_processo_pro');
        $tipo = $request->get('cd_tipo_processo_tpo');

        $processos = Processo::where('cd_conta_con', $this->cdContaCon);
        if(!empty($numero))  $processos->where('nu_processo_pro','like',"%$numero%");
        if(!empty($tipo))   $processos->where('cd_tipo_processo_tpo',$tipo);
          $processos = $processos->orderBy('nu_processo_pro')->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->get();

        return view('processo/processos',['processos' => $processos,'numero' => $numero,'tipoProcesso' => $tipo]);
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

        $processo = Processo::with('cliente')->with('correspondente')->with('cidade')->where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();

        if(!empty($processo->cliente->nm_fantasia_cli)){
                $nome =  $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli.' ('.$processo->cliente->nm_fantasia_cli.')';
        }else{
                $nome = $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli;
        }

        $nomeCorrespondente = '';
        if(!empty($processo->correspondente)){ 
            if(!empty($processo->correspondente->nm_fantasia_con)){
                    $nomeCorrespondente =  $processo->correspondente->nm_razao_social_con.' ('.$processo->cliente->nm_fantasia_con.')';
            }else{
                    $nomeCorrespondente = $processo->correspondente->nm_razao_social_con;
            }
        }

        if(!empty($processo->dt_solicitacao_pro))
            $processo->dt_solicitacao_pro = date('d/m/Y', strtotime($processo->dt_solicitacao_pro));
        
        if(!empty($processo->dt_prazo_fatal_pro))
            $processo->dt_prazo_fatal_pro = date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro));


        return view('processo/edit',['estados' => $estados, 'varas' => $varas, 'tiposProcesso' => $tiposProcesso, 'processo' => $processo, 'nome' => $nome,'nomeCorrespondente' => $nomeCorrespondente]);

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

        $request->merge(['dt_solicitacao_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_solicitacao_pro)))]);
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

        $request->merge(['dt_solicitacao_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_solicitacao_pro)))]);
        $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_prazo_fatal_pro)))]);
    
        $processo = Processo::where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();
        $processo->fill($request->all());

        if(!$processo->saveOrFail()){
          
            DB::rollBack();
            Flash::error('Erro ao atualizar dados');
            return redirect('processos');
        }    
         
        DB::commit();
        Flash::success('Dados atualizados com sucesso');
        return redirect('processos');


    }

    public function destroy($id)
    {
        $processo = Processo::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if($processo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}