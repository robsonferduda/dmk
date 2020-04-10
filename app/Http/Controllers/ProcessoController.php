<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Entidade;
use App\Vara;
use App\Estado;
use App\Cidade;
use App\Cliente;
use App\Conta;
use App\Correspondente;
use App\TipoProcesso;
use App\Processo;
use App\ProcessoDespesa;
use App\TipoDespesa;
use App\Enums\TipoMensagem;
use App\TipoServico;
use App\ProcessoTaxaHonorario;
use App\TaxaHonorario;
use App\EnderecoEletronico;
use App\ContaCorrespondente;
use App\ProcessoMensagem;
use App\Http\Requests\ProcessoRequest;
use App\Http\Controllers\CalendarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class ProcessoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth',['except' => ['responderNotificacao']]);
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        if (!empty(\Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso')))
        {
            
            $tiposProcesso = \Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso');

        }else{

            $tiposProcesso = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
           \Cache::tags($this->cdContaCon,'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);

        }

        $tiposServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->get();
       
        $processos = Processo::with(array('correspondente' => function($query){
              $query->select('cd_conta_con','nm_razao_social_con','nm_fantasia_con');
              $query->with(array('contaCorrespondente' => function($query){
                    $query->where('cd_conta_con', $this->cdContaCon);
              }));
        }))->with(array('cidade' => function($query){
              $query->select('cd_cidade_cde','nm_cidade_cde','cd_estado_est');
              $query->with(array('estado' => function($query){
                  $query->select('sg_estado_est','cd_estado_est');
        }));
        }))->with(array('honorario' => function($query){
              $query->select('cd_processo_pro','cd_tipo_servico_tse');
              $query->with(array('tipoServico' => function($query){
                  $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
        }));
        }))->with('status')
        ->with(array('cliente' => function($query){
              $query->select('cd_cliente_cli','nm_fantasia_cli','nm_razao_social_cli');
        }))->where('cd_conta_con', $this->cdContaCon)->take(100)->orderBy('dt_prazo_fatal_pro')->orderBy('created_at')->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_cidade_cde','cd_correspondente_cor','hr_audiencia_pro','dt_solicitacao_pro','dt_prazo_fatal_pro','nm_autor_pro','cd_status_processo_stp')->get();          

        return view('processo/processos',['processos' => $processos,'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico]);
    }

    public function acompanhar()
    {

       if (!empty(\Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso')))
        {
            
            $tiposProcesso = \Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso');

        }else{

            $tiposProcesso = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
           \Cache::tags($this->cdContaCon,'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);

        }

        $responsaveis = User::where('cd_conta_con',$this->cdContaCon)->orderBy('name')->get();

        $tiposServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->get();
       
        $processos = Processo::with(array('correspondente' => function($query){
              $query->select('cd_conta_con','nm_razao_social_con','nm_fantasia_con');
              $query->with(array('contaCorrespondente' => function($query){
                    $query->where('cd_conta_con', $this->cdContaCon);
              }));
        }))->with(array('cidade' => function($query){
              $query->select('cd_cidade_cde','nm_cidade_cde','cd_estado_est');
              $query->with(array('estado' => function($query){
                  $query->select('sg_estado_est','cd_estado_est');
        }));
        }))->with(array('honorario' => function($query){
              $query->select('cd_processo_pro','cd_tipo_servico_tse');
              $query->with(array('tipoServico' => function($query){
                  $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
        }));
        }))->with(array('cliente' => function($query){
              $query->select('cd_cliente_cli','nm_fantasia_cli','nm_razao_social_cli');
        }))->with('status')
        ->where('cd_conta_con', $this->cdContaCon)
        ->whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO,\StatusProcesso::CANCELADO])
        ->orderBy('dt_prazo_fatal_pro')
        ->orderBy('hr_audiencia_pro')
        ->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_cidade_cde','cd_correspondente_cor','hr_audiencia_pro','dt_solicitacao_pro','dt_prazo_fatal_pro','nm_autor_pro','cd_status_processo_stp')->get();        

        return view('processo/acompanhamento',['processos' => $processos,'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico, 'responsaveis' => $responsaveis]);
    }

    public function acompanhamento($id){

        $id = \Crypt::decrypt($id); 

        $processo = Processo::with('anexos')->with('anexos.entidade.usuario')->where('cd_processo_pro',$id)->first();

        (new ProcessoMensagem)->atualizaMensagensLidas($id,$this->cdContaCon);

        $mensagens_externas = ProcessoMensagem::where('cd_processo_pro',$id)
                                                ->where('cd_tipo_mensagem_tim',TipoMensagem::EXTERNA)
                                                ->with('entidadeRemetente')
                                                ->with('entidadeDestinatario')
                                                ->withTrashed()
                                                ->orderBy('created_at', 'ASC')
                                                ->get();

        $mensagens_internas = ProcessoMensagem::where('cd_processo_pro',$id)
                                                ->where('cd_tipo_mensagem_tim',TipoMensagem::INTERNA)
                                                ->with('entidadeRemetente')
                                                ->with('entidadeDestinatario')
                                                ->withTrashed()
                                                ->orderBy('created_at', 'ASC')
                                                ->get();
    
        return view('processo/acompanhar',['processo' => $processo, 'mensagens_externas' => $mensagens_externas, 'mensagens_internas' => $mensagens_internas]);
    }

    public function atualizarStatus(Request $request)
    {

        $processo = Processo::where('cd_processo_pro',$request->processo)->first();
        
        if($request->status == 0){
            Flash::warning('Obrigatório selecionar uma situação');
        }else{
        
            $processo->cd_status_processo_stp = $request->status;
            if($processo->save())
                Flash::success('Situação atualizada com sucesso');
            else
                Flash::success('Erro ao atualizar situação do processo');
        }

        return redirect('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro));

    }

    public function finalizarProcesso(Request $request)
    {

        $processo = Processo::where('cd_processo_pro',$request->processo)->first();
        $cliente = Cliente::where('cd_cliente_cli',$processo->cd_cliente_cli)->first();
        $conta = Conta::where('cd_conta_con',$processo->cd_conta_con)->first();
        
        $processo->cd_status_processo_stp = $request->status;

        if($processo->save()){

            if($request->fl_envio_arquivo){
                //notificarCliente
                $emails = explode(",", $cliente->entidade->getEmailsNotificacao());

                for ($i=0; $i < count($emails); $i++) { 
                    
                    $processo->anexos = ($request->lista_arquivos) ? $request->lista_arquivos : array();
                    $processo->email = $emails[$i];
                    $processo->conta = $conta->nm_razao_social_con;
                    $processo->notificarCliente($processo);

                }
            }
            
            Flash::success('Situação atualizada com sucesso');
        }
        else
            Flash::success('Erro ao atualizar situação do processo');

        return redirect('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro));

    }

    public function relatorio($id){

        $id = \Crypt::decrypt($id);

        $processo = Processo::where('cd_processo_pro',$id)->where('cd_conta_con',$this->cdContaCon)->first();
    
        $despesasCliente = 0;
        $despesasReembolsaveisCliente = 0;
        $despesasCorrespondente = 0;
        $despesasReembolsaveisCorrespondente = 0;
        $honorarioCliente = 0;
        $honorarioCorrespondente = 0;
        $taxaCliente = 0;
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

        if(!empty($processo->honorario->vl_taxa_cliente_pth))
            $taxaCliente = $processo->honorario->vl_taxa_cliente_pth;            
        
        if(!empty($processo->honorario->vl_taxa_honorario_cliente_pth))
            $honorarioCliente = $processo->honorario->vl_taxa_honorario_cliente_pth;     

        if(!empty($processo->honorario->vl_taxa_honorario_correspondente_pth))
            $honorarioCorrespondente = $processo->honorario->vl_taxa_honorario_correspondente_pth;

        $conta = Conta::select('fl_despesa_nao_reembolsavel_con')->find($this->cdContaCon);

        if($conta->fl_despesa_nao_reembolsavel_con == 'N')
            $despesasCliente = 0;

        $entrada = $honorarioCliente + $despesasReembolsaveisCliente;
        $saida   = $despesasCliente + $despesasReembolsaveisCorrespondente + $honorarioCorrespondente + $taxaCliente;
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
                                          'receita' => $receita,
                                          'taxa' => $taxaCliente,
                                          'flDespesa' => $conta->fl_despesa_nao_reembolsavel_con]);
    }

    public function clonar($id){

        $id = \Crypt::decrypt($id);
        
        $processo              = Processo::where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();
        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_processo_pro',$id)->where('cd_conta_con', $this->cdContaCon)->first();
        
        $entidade = Entidade::create([
            'cd_conta_con'         => $this->cdContaCon,
            'cd_tipo_entidade_tpe' => \TipoEntidade::PROCESSO
        ]);

        $processo->cd_entidade_ete = $entidade->cd_entidade_ete;

        $novoProcesso = $processo->replicate();
        $novoProcesso->save();

        $processoTaxaHonorario->cd_processo_pro = $novoProcesso->cd_processo_pro;
        
        $novoProcessoTaxaHonorario = $processoTaxaHonorario->replicate();
        $novoProcessoTaxaHonorario->save();

        if(getenv('APP_ENV') == 'production')
            (new CalendarioController)->adicionarPorProcesso($novoProcesso);

        Flash::success('Processo clonado com sucesso');
        //Flash::error('Atenção! É preciso preencher os honorários para finalizar o cadastro.');
        DB::commit(); 

        return redirect('processos/editar/'.\Crypt::encrypt($novoProcesso->cd_processo_pro));  
    }

    private function salvarHonorarios($id,$dados){

        $processo_id = $id;

        if(empty($dados->nota_fiscal_cliente)){
            $dados->nota_fiscal_cliente = NULL;
        }else{
            $dados->nota_fiscal_cliente = str_replace(",", ".", $dados->nota_fiscal_cliente);
        }

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
            $valor->cd_tipo_servico_correspondente_tse = $dados->servicoCorrespondente;
            $valor->vl_taxa_cliente_pth = $dados->nota_fiscal_cliente;
           
            if(!$valor->saveOrFail()){
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('processos');    
            }

        }else{

            $valor = ProcessoTaxaHonorario::create([
                'cd_conta_con' => $this->cdContaCon,
                'cd_processo_pro' => $processo_id,
                'cd_tipo_servico_tse' => $dados->servico,
                'cd_tipo_servico_correspondente_tse' => $dados->servicoCorrespondente,
                'vl_taxa_honorario_cliente_pth' => $dados->valor_cliente,
                'vl_taxa_honorario_correspondente_pth' => $dados->valor_correspondente,
                'vl_taxa_cliente_pth' => $dados->nota_fiscal_cliente
            ]);

            if(!$valor){
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('processos');    
            }
        }       
        
        return true;


    }

    public function salvarDespesas(Request $request){

        $processo_id = $request->processo;
       
        $processo_id = \Crypt::decrypt($processo_id);

        DB::beginTransaction();

        $dados = json_decode($request->valores);

        $conta = Conta::select('fl_despesa_nao_reembolsavel_con')->find($this->cdContaCon);

        foreach ($dados as $dado) {

            if($conta->fl_despesa_nao_reembolsavel_con == 'N' && $dado->reembolso == 'N')
                continue;

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
                    return redirect('processos/despesas/'.\Crypt::encrypt($processo_id));    
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
                    return redirect('processos/despesas/'.\Crypt::encrypt($processo_id));    
                }
            }            
        }
                   
    
        Flash::success('Dados atualizados com sucesso');
        DB::commit(); 
 
        return redirect('processos/despesas/'.\Crypt::encrypt($processo_id));       

    }

    public function buscaValorCorrespondente($correspondente,$cidade,$tipoServico){

        $valor = '';
        $entidade = ContaCorrespondente::select('cd_entidade_ete')->where('cd_conta_con',$this->cdContaCon)->where('cd_correspondente_cor',$correspondente)->first();
       
        if(!empty($cidade) && !empty($entidade)){
            $valor = TaxaHonorario::where('cd_conta_con', $this->cdContaCon)
                                  ->where('cd_tipo_servico_tse',$tipoServico)
                                  ->where('cd_cidade_cde', $cidade)
                                  ->where('cd_entidade_ete',$entidade->cd_entidade_ete)
                                  ->select('nu_taxa_the')->first();
            if(empty($valor))
                $valor = '';

        }

        echo json_encode(str_replace('.',',',$valor));
    }

    public function buscaValorCliente($cliente,$cidade,$tipoServico){

        $valor = '';
        $entidade = Cliente::select('cd_entidade_ete')->where('cd_cliente_cli',$cliente)->first();
       
        if(!empty($cidade) && !empty($entidade)){
            $valor = TaxaHonorario::where('cd_conta_con', $this->cdContaCon)
                                  ->where('cd_tipo_servico_tse',$tipoServico)
                                  ->where('cd_cidade_cde', $cidade)
                                  ->where('cd_entidade_ete',$entidade->cd_entidade_ete)
                                  ->select('nu_taxa_the')->first();
            if(empty($valor))
                $valor = '';

        }

        echo json_encode(str_replace('.',',',$valor));

    }

    public function financas($id){

        $id = \Crypt::decrypt($id); 

        //$processo = Processo::where('cd_conta_con',$this->cdContaCon)->where('cd_processo_pro',$id)->first();
     
        $despesas = DB::table('processo_pro')
                    ->join('cliente_cli','processo_pro.cd_cliente_cli', '=', 'cliente_cli.cd_cliente_cli')
                    ->leftjoin('conta_con','processo_pro.cd_correspondente_cor', '=', 'conta_con.cd_conta_con')
                    ->leftjoin('entidade_ete','conta_con.cd_conta_con', '=', 'entidade_ete.cd_conta_con')
                    ->join('tipo_despesa_tds', function($join){
                        $join->on('processo_pro.cd_conta_con','=','tipo_despesa_tds.cd_conta_con');
                        $join->where('fl_reembolso_tds','S');
                    })
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
      
        $conta = Conta::select('fl_despesa_nao_reembolsavel_con')->find($this->cdContaCon);
                               
        return view('processo/financas',['despesas' => $despesas,
                                         'tiposDeServico' => $tiposDeServico,
                                         'id' => $id,
                                         'honorariosProcesso' => $honorariosProcesso,
                                         'conta' => $conta
                                        ]);

    }


    public function detalhes($id){

        //Traz os detalhes de processo para a conta e para os correspondentes

        $id = \Crypt::decrypt($id); 

        switch (Auth::user()->role()->first()->slug) {

            case 'correspondente':
                $processo = Processo::where('cd_processo_pro',$id)->where('cd_correspondente_cor',$this->cdContaCon)->first();
                break;
                
            default:
                $processo = Processo::where('cd_processo_pro',$id)->where('cd_conta_con',$this->cdContaCon)->first();
                break;
        }
    
        return view('processo/detalhes',['processo' => $processo]);
    }

    public function buscar(Request $request)
    {
     
        $numero   = $request->get('nu_processo_pro');
        $tipo = $request->get('cd_tipo_processo_tpo');
        $tipoServico = $request->get('cd_tipo_servico_tse');
        $autor = $request->get('nm_autor_pro');
        $reu = $request->get('nm_reu_pro');
        $acompanhamento = $request->get('nu_acompanhamento_pro');
        $dtInicio =  $request->get('dtInicio');
        $dtFim =  $request->get('dtFim');

        if(!empty($dtInicio))
            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));       

        if(!empty($dtFim))
            $dtFim = date('Y-m-d', strtotime(str_replace('/','-',$dtFim))); 

        if (!empty(\Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso')))
        {
            
            $tiposProcesso = \Cache::tags($this->cdContaCon,'listaTiposProcesso')->get('tiposProcesso');

        }else{

            $tiposProcesso = TipoProcesso::All();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
           \Cache::tags($this->cdContaCon,'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);

        }

        $tiposServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->get();

        if(!empty($request->acompanhamento)){

            $processos = Processo::with(array('correspondente' => function($query){
              $query->select('cd_conta_con','nm_razao_social_con','nm_fantasia_con');
              $query->with(array('contaCorrespondente' => function($query){
                    $query->where('cd_conta_con', $this->cdContaCon);
              }));
            }))->with(array('cidade' => function($query){
                  $query->select('cd_cidade_cde','nm_cidade_cde','cd_estado_est');
                  $query->with(array('estado' => function($query){
                      $query->select('sg_estado_est','cd_estado_est');
            }));
            }))->with(array('honorario' => function($query){
                  $query->select('cd_processo_pro','cd_tipo_servico_tse');
                  $query->with(array('tipoServico' => function($query){
                      $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
            }));
            }))->with(array('cliente' => function($query){
                  $query->select('cd_cliente_cli','nm_fantasia_cli','nm_razao_social_cli');
            }))->with('status')
            ->where('cd_conta_con', $this->cdContaCon)
            ->when(!empty($tipoServico), function($query) use($tipoServico){
                    $query->whereHas('honorario', function($query) use ($tipoServico) {

                        $query->where('cd_tipo_servico_tse', $tipoServico);

                    });
            })
            ->when(!empty($tipo), function($query) use($tipo){
                    
                $query->where('cd_tipo_processo_tpo',$tipo);
            })
            ->when(!empty($numero), function($query) use($numero){
                    
                $query->where('nu_processo_pro','like',"%$numero%");
            })
            ->whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO,\StatusProcesso::CANCELADO])->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_cidade_cde','cd_correspondente_cor','hr_audiencia_pro','dt_solicitacao_pro','dt_prazo_fatal_pro','nm_autor_pro','cd_status_processo_stp')->get();  


        }else{

            $processos = Processo::where('cd_conta_con', $this->cdContaCon);

            if(!empty($tipoServico)) $processos->whereHas('honorario', function($query) use ($tipoServico) {

                $query->where('cd_tipo_servico_tse', $tipoServico);

            });
            if(!empty($numero))  $processos->where('nu_processo_pro','like',"%$numero%");
            if(!empty($tipo))   $processos->where('cd_tipo_processo_tpo',$tipo);
            if(!empty($autor)) $processos->where('nm_autor_pro', 'ilike', '%'. $autor. '%');
            if(!empty($reu)) $processos->where('nm_reu_pro', 'ilike', '%'. $reu. '%');
            if(!empty($acompanhamento)) $processos->where('nu_acompanhamento_pro', 'ilike', '%'. $acompanhamento. '%');

            if(!empty($dtInicio) && !empty($dtFim)){
                $processos = $processos->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
            }else{
                if(!empty($dtInicio)){
                    $processos = $processos->where('dt_prazo_fatal_pro',$dtInicio);
                }else{
                    if(!empty($dtFim)){
                        $processos = $processos->where('dt_prazo_fatal_pro',$dtFim);
                    }
                }
            }

            $processos = $processos->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->get();

        }

        if(!empty($dtInicio))
            $dtInicio = date('d/m/Y', strtotime($dtInicio));       

        if(!empty($dtFim))
            $dtFim = date('d/m/Y', strtotime($dtFim)); 

        if(!empty($request->acompanhamento)){

            $responsaveis = User::where('cd_conta_con',$this->cdContaCon)->orderBy('name')->get();

            return view('processo/acompanhamento',['processos' => $processos,'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico, 'responsaveis' => $responsaveis,'numero' => $numero,'tipoProcesso' => $tipo,'tipoServico' => $tipoServico]);
        }else{
            return view('processo/processos',['processos' => $processos,'numero' => $numero,'tipoProcesso' => $tipo,'tipoServico' => $tipoServico, 'tiposServico' => $tiposServico, 'tiposProcesso' => $tiposProcesso, 'autor' => $autor, 'reu' => $reu, 'acompanhamento' => $acompanhamento, 'dtInicio' => $dtInicio,'dtFim' => $dtFim]);
        }
    }

    public function novo(){

        if (!\Cache::has('estados')) {
            $estados = Estado::orderBy('nm_estado_est')->get();
            \Cache::put('estados', $estados, now()->addMinutes(1440));
        }else{
            $estados =  \Cache::get('estados');
        }
        
        $sub = \DB::table('vara_var')->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->whereRaw("cd_conta_con = $this->cdContaCon")->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
        ->get();

        $tiposProcesso  = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->orderBy('nm_tipo_processo_tpo')->get();
        $tiposDeServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->orderBy('nm_tipo_servico_tse')->get();
       
        return view('processo/novo',['estados' => $estados,'varas' => $varas, 'tiposProcesso' => $tiposProcesso, 'tiposDeServico' => $tiposDeServico]);

    }

    public function editar($id){

        $id = \Crypt::decrypt($id); 
        
        if (!\Cache::has('estados')) {
            $estados = Estado::orderBy('nm_estado_est')->get();
            \Cache::put('estados', $estados, now()->addMinutes(1440));
        }else{
            $estados =  \Cache::get('estados');
        
        
        
        
        
        
        
        
        }

        $sub = \DB::table('vara_var')->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->whereRaw("cd_conta_con = $this->cdContaCon")->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
        ->get();

        $tiposProcesso = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->orderBy('nm_tipo_processo_tpo')->get();

        $processo = Processo::with('cliente')->with('correspondente')->with('cidade')->with('responsavel')->where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();

        if(!empty($processo->cliente->nm_fantasia_cli)){
                $nome =  $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli.' ('.$processo->cliente->nm_fantasia_cli.')';
        }else{
                $nome = $processo->cliente->nu_cliente_cli.' - '.$processo->cliente->nm_razao_social_cli;
        }

        $nomeCorrespondente = '';
        if(!empty($processo->correspondente)){ 
                            
            $nomeCorrespondente = ($processo->correspondente->load('contaCorrespondente')->contaCorrespondente) ? $processo->correspondente->load('contaCorrespondente')->contaCorrespondente->nm_conta_correspondente_ccr : '';
            
        }

        if(!empty($processo->dt_solicitacao_pro))
            $processo->dt_solicitacao_pro = date('d/m/Y', strtotime($processo->dt_solicitacao_pro));
        
        if(!empty($processo->dt_prazo_fatal_pro))
            $processo->dt_prazo_fatal_pro = date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro));

        $tiposDeServico = TipoServico::where('cd_conta_con',$this->cdContaCon)->orderBy('nm_tipo_servico_tse')->get();
        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_processo_pro',$id)->where('cd_conta_con', $this->cdContaCon)->select('cd_tipo_servico_tse','cd_tipo_servico_correspondente_tse','vl_taxa_honorario_correspondente_pth','vl_taxa_honorario_cliente_pth','vl_taxa_cliente_pth')->first();

        return view('processo/edit',['estados' => $estados, 'varas' => $varas, 'tiposProcesso' => $tiposProcesso, 'processo' => $processo, 'nome' => $nome,'nomeCorrespondente' => $nomeCorrespondente, 'tiposDeServico' => $tiposDeServico,'processoTaxaHonorario' => $processoTaxaHonorario]);

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

        if(!empty($request->dt_solicitacao_pro))
            $request->merge(['dt_solicitacao_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_solicitacao_pro)))]);
        if(!empty($request->dt_prazo_fatal_pro))
            $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_prazo_fatal_pro)))]);
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        if($entidade){

            $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

            $processo = new Processo();

            //verifica se o campo correspondente foi setado. Se sim, atualiza o status para "Em andamento", sobrescrevendo o status default "Contratar Correspondente"
            if($request->cd_correspondente_cor)
                $request->merge(['cd_status_processo_stp' => \StatusProcesso::ANDAMENTO]);

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

        $dados = new \stdClass();
        $dados->valor_cliente = $request->taxa_honorario_cliente;
        $dados->valor_correspondente = $request->taxa_honorario_correspondente;
        $dados->servico = $request->cd_tipo_servico_tse;
        $dados->servicoCorrespondente = $request->cd_tipo_servico_correspondente_tse;
        $dados->nota_fiscal_cliente = $request->nota_fiscal_cliente;
        $this->salvarHonorarios($processo->cd_processo_pro,$dados);

        DB::commit();

        if(getenv('APP_ENV') == 'production')
            (new CalendarioController)->adicionarPorProcesso($processo);

        Flash::success('Processo cadastrado com sucesso');
        return redirect('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro));
    }

    public function update(ProcessoRequest $request,$id)
    {
        
        DB::beginTransaction();

        if(!empty($request->dt_solicitacao_pro))
            $request->merge(['dt_solicitacao_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_solicitacao_pro)))]);
        
        if(!empty($request->dt_prazo_fatal_pro))
            $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d',strtotime(str_replace('/','-',$request->dt_prazo_fatal_pro)))]);
    
        $processo = Processo::where('cd_conta_con', $this->cdContaCon)->where('cd_processo_pro',$id)->first();

        /* Verifica se o campo correspondente foi setado E se o status anterior era "Contratar Correspondente".
           Se sim, atualiza o status para "Em andamento", sobrescrevendo o status default "Contratar Correspondente" */

            if($request->cd_correspondente_cor and $processo->cd_status_processo_stp == \StatusProcesso::CONTRATAR_CORRESPONDENTE )
                $request->merge(['cd_status_processo_stp' => \StatusProcesso::ANDAMENTO]);

        $processo->fill($request->all());

        if(!$processo->saveOrFail()){
          
            DB::rollBack();
            Flash::error('Erro ao atualizar dados');
            return redirect('processos');
        }    

        $dados = new \stdClass();
        $dados->valor_cliente = $request->taxa_honorario_cliente;
        $dados->valor_correspondente = $request->taxa_honorario_correspondente;
        $dados->servico = $request->cd_tipo_servico_tse;
        $dados->servicoCorrespondente = $request->cd_tipo_servico_correspondente_tse;
        $dados->nota_fiscal_cliente = $request->nota_fiscal_cliente;
    
        $this->salvarHonorarios($processo->cd_processo_pro,$dados);

        DB::commit();

        if(getenv('APP_ENV') == 'production')
            (new CalendarioController)->adicionarPorProcesso($processo);

        Flash::success('Processo atualizado com sucesso');
        return redirect('processos/acompanhamento/'.\Crypt::encrypt($id));
        
    }

    public function destroy($id)
    {
        $processo = Processo::where('cd_conta_con',$this->cdContaCon)->find($id);

        if(!empty($processo) && $processo->delete()){
            (new CalendarioController)->excluirEventoProcesso($id);
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        }else{
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
    }

    public function notificarCorrespondente($id_processo){

        $id = \Crypt::decrypt($id_processo);
        $processo = Processo::with('cliente')->with('conta')->where('cd_processo_pro',$id)->first();
        $vinculo = ContaCorrespondente::where('cd_conta_con', $this->cdContaCon)->where('cd_correspondente_cor',$processo->cd_correspondente_cor)->first();

        if(empty($processo->cd_correspondente_cor) or is_null($vinculo)){

            Flash::error('Nenhum correspondente informado para o processo');

        }else{

            
            $emails = EnderecoEletronico::where('cd_entidade_ete',$vinculo->cd_entidade_ete)->where('cd_tipo_endereco_eletronico_tee',\App\Enums\TipoEnderecoEletronico::NOTIFICACAO)->get();

            if(count($emails) == 0){

                Flash::error('Nenhum email de notificação cadastrado para o correspondente');

            }else{

                $lista = '';

                $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::AGUARDANDO_CORRESPONDENTE;
                $processo->save();

                foreach ($emails as $email) {

                    $processo->email =  $email->dc_endereco_eletronico_ede;
                    $processo->correspondente = $vinculo->nm_conta_correspondente_ccr;
                    $processo->notificarCorrespondente($processo);
                    $lista .= $email->dc_endereco_eletronico_ede.', ';
                }

                Flash::success('Notificação enviada com sucesso para: '.substr(trim($lista),0,-1));

            }

        }

        return redirect('processos/acompanhamento/'.\Crypt::encrypt($id));
    }

    public function responderNotificacao($resposta,$token)
    {
        $id = \Crypt::decrypt($token);
        $processo = Processo::with('cliente')->where('cd_processo_pro',$id)->first();

    
        //Atualiza o status do processo de acordo com a resposta do correspondente
        if($resposta == 'S'){

            $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::ACEITO_CORRESPONDENTE;
            $processo->save();

        }else{

            $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::RECUSADO_CORRESPONDENTE;
            $processo->save();
        }

        //Notifica o escritório sobre a decisão do correspondente
        $email = User::where('cd_conta_con',$processo->cd_conta_con)->where('cd_nivel_niv',1)->first()->email;

        $processo->email = $email;
        $processo->token = $token;
        $processo->parecer = $resposta;
        $processo->correspondente = $processo->correspondente->nm_razao_social_con;
        $processo->notificarConta($processo);

        \Session::put('retorno', array('tipo' => 'sucesso','msg' => 'Sua resposta foi recebida e o interessado notificado sobre a decisão.'));
        return Redirect::route('msg-filiacao');

    }

    public function atualizaAnexosEnviados($id)
    {
        $flag = 'N';
        $processo = Processo::findOrFail($id);
        $vinculo = ContaCorrespondente::where('cd_conta_con', $this->cdContaCon)->where('cd_correspondente_cor',$processo->cd_correspondente_cor)->first();

        if($vinculo){

            if($processo->fl_envio_anexos_pro == 'N') $flag = 'S'; else $flag = 'N';

            $processo->fl_envio_anexos_pro = $flag;
            
            if($processo->save()){

                if($flag == 'S')
                    $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::AGUARDANDO_DOCS_CORRESPONDENTE;
                else
                    $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::ACEITO_CORRESPONDENTE;

                if($processo->save()){

                    if($flag == 'S'){
                        $emails = EnderecoEletronico::where('cd_entidade_ete',$vinculo->cd_entidade_ete)->where('cd_tipo_endereco_eletronico_tee',\App\Enums\TipoEnderecoEletronico::NOTIFICACAO)->get();

                        if(count($emails) == 0){

                            Flash::warning('Nenhum email de notificação cadastrado para o correspondente. O status foi atualizado, porém o correspondente não foi nitificado.');

                        }else{

                            $lista = '';

                            foreach ($emails as $email) {

                                $processo->email = $email->dc_endereco_eletronico_ede;
                                $processo->correspondente = $vinculo->nm_conta_correspondente_ccr;

                                try{
                                    $processo->notificarEnvioDocumentos($processo);
                                } catch (\Swift_RfcComplianceException $e) {

                                    //Retorna o status anterior
                                    $processo = Processo::findOrFail($id);
                                    $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::ACEITO_CORRESPONDENTE;
                                    $processo->save();

                                    return Response::json(array('message' => 'Houve um erro ao atualizar o status, pois o email "<strong>'.$email->dc_endereco_eletronico_ede.'</strong>" possui problemas em sua formatação. Verifique o email e tente novamente.'), 500);
                                }
                                
                                $lista .= $email->dc_endereco_eletronico_ede.', ';
                            }

                        }
                    }
                }

                return Response::json(array('message' => 'Registro atualizado com sucesso'), 200);

            }else{
                return Response::json(array('message' => 'Houve um erro ao atualizar o status do processo'), 500);
            }

        }else{
            return Response::json(array('message' => 'Informe um correspondente para atualizar o valor do campo'), 500);
        }
    }

    public function atualizaAnexosRecebidos($id)
    {

        $flag = 'N';
        $processo = Processo::findOrFail($id);

        if($processo->fl_recebimento_anexos_pro == 'N') $flag = 'S'; else $flag = 'N';

        $processo->fl_recebimento_anexos_pro = $flag;
        
        if($processo->save()){

            if($flag == 'S')
                $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::ANDAMENTO;
            else
                $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::AGUARDANDO_DOCS_CORRESPONDENTE;
            
            $processo->save();

            return Response::json(array('message' => 'Registro atualizado com sucesso'), 200);
        }else{
            return Response::json(array('message' => 'Erro ao atualizar registro'), 500);
        }

    }

    public function requisitarDados($id_processo)
    {
        $id_processo = \Crypt::decrypt($id_processo);

        $processo = Processo::findOrFail($id_processo);
        $vinculo = ContaCorrespondente::where('cd_conta_con', $this->cdContaCon)->where('cd_correspondente_cor',$processo->cd_correspondente_cor)->first();
        $emails = EnderecoEletronico::where('cd_entidade_ete',$vinculo->cd_entidade_ete)->where('cd_tipo_endereco_eletronico_tee',\App\Enums\TipoEnderecoEletronico::NOTIFICACAO)->get();

        if($processo){

            if(count($emails) > 0){

                $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::AGUARDANDO_DADOS;

                if($processo->save()){

                    $lista = '';

                    foreach ($emails as $email) {

                        $processo->email =  $email->dc_endereco_eletronico_ede;
                        $processo->correspondente = $vinculo->nm_conta_correspondente_ccr;
                        $processo->notificarRequisitarDados($processo);
                        $lista .= $email->dc_endereco_eletronico_ede.', ';
                    }

                    Flash::success('Notificação enviada com sucesso para: '.substr(trim($lista),0,-1));


                }else{

                    Flash::error('Erro ao requisitar dados, o processo não foi atualizado.');

                }

            }else{

                Flash::error('Nenhum email de notificação cadastrado para o correspondente, a operação foi cancelada. Cadastre um email de notificação para o correspondente e tente novamente');
            }

        }else{
            Flash::error('Erro ao requisitar dados, o processo não foi encontrado.');
        }
        return redirect('processos/acompanhamento/'.\Crypt::encrypt($id_processo));
    }

    public function atualizarDadosAdvogadoPreposto(Request $request)
    {

        $id_processo = \Crypt::decrypt($request->cd_processo_pro);
        $processo = Processo::findOrFail($id_processo);
        $conta = Conta::where('cd_conta_con',$processo->cd_conta_con)->first();
        $emails = EnderecoEletronico::where('cd_entidade_ete',$conta->entidade()->first()->cd_entidade_ete)->where('cd_tipo_endereco_eletronico_tee',\App\Enums\TipoEnderecoEletronico::NOTIFICACAO)->get();

        if($processo){

            if(count($emails) > 0){

                    $processo->nm_advogado_pro = $request->dados_advogado;
                    $processo->nm_preposto_pro = $request->dados_preposto;
                    $processo->cd_status_processo_stp = \App\Enums\StatusProcesso::DADOS_ENVIADOS;

                    if($processo->save()){

                        $lista = '';

                        foreach ($emails as $email) {

                            $processo->email =  $email->dc_endereco_eletronico_ede;
                            $processo->notificarAtualizacaoDados($processo);
                            $lista .= $email->dc_endereco_eletronico_ede.', ';
                        }

                        Flash::success('Dados atualizados com sucesso e o escritório foi notificado sobre a atualização dos dados. Mensagem enviada para '.substr(trim($lista),0,-1));

                    }else{

                        Flash::error('Erro ao atualizar o processo');

                    }

            }else{

                Flash::error('Nenhum email de notificação cadastrado para o escritório, a operação foi cancelada. Requisite o cadastro de um email de notificação para o escritório e tente novamente');
            }

        }else{

            Flash::error('Erro ao requisitar dados, o processo não foi encontrado.');
        }

        return redirect('processos/acompanhamento/'.\Crypt::encrypt($id_processo));

    }

}