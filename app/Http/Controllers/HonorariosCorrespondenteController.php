<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Cidade;
use App\TaxaHonorario;
use App\TipoServico;
use App\Correspondente;
use App\ContaCorrespondente;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Events\EventNotification;
use App\Jobs\HonorarioCorrespondenteJob;

class HonorariosCorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth');        
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }

    public function index()
    {

    }

    public function getHonorariosOrdenados(Request $request)
    {
        $valores = array();
        $comarcas = array();
        $servicos = array();
        
    	$id = \Crypt::decrypt($request->id);

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $comarcas[] = $honorario->cidade;
            }
        } 

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                if($honorario->tipoServico){
                    $servicos[] = $honorario->tipoServico;
                }
            }
        } 

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {

            	$chave = "key-".$honorario->cd_cidade_cde."-".$honorario->cd_tipo_servico_tse;

                $valores[$chave] = $honorario->nu_taxa_the;
                		
            }
        } 

        $dados = array('honorarios' => $valores,
    				   'comarcas' => $comarcas,
    				   'servicos' => $servicos);

    	return Response::json($dados);
    }

    public function getHonorariosInsercao(Request $request)
    {
        $id = \Crypt::decrypt($request->id);
        $cd_correspondente = $request->correspondente;

        $valores = array();
        $comarcas = array();
        $servicos = array();
        
        $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor',$cd_correspondente)->first(); 
        
        $estado = $request->estado;
        $cidade = $request->lista_cidades;
        $servico = $request->lista_servicos;

        $lista_cidades = array();
        $lista_merge = array();

        $lista_servicos = array();      

        //Carrega serviços já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_servicos[] = $honorario->tipoServico;
            }
        }

        $lista_servicos = TipoServico::whereIn('cd_tipo_servico_tse',$servico)->get();

        $lista_temp = array();
        foreach ($lista_servicos as $servico) {
            if(!in_array($servico, $lista_temp))
                $lista_temp[] = $servico;
        }
        $lista_servicos = $lista_temp;

        //Carrega cidade selecionada        
        if($cidade > 0){
            $lista_cidades[] = Cidade::where('cd_cidade_cde',$cidade)->first(); 
        }elseif($cidade == 0){

            $lista_cidades = $correspondente->entidade->atuacaoPorEstado($estado);

        }
 
        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {

                $chave = "key-".$honorario->cd_cidade_cde."-".$honorario->cd_tipo_servico_tse;

                $valores[$chave] = $honorario->nu_taxa_the;
                        
            }
        }  
        
        $dados = array('honorarios' => $valores,
                       'comarcas' => $lista_cidades,
                       'servicos' => $lista_servicos);

        return Response::json($dados);
    }

    //Classe que realiza a inserção na tabela honorários
    public function salvarHonorarios(Request $request){

        $estado = $request->estado;
        $entidade_correspondente = $request->entidade;
        $comarca = $request->comarca;
        $servico = $request->servico;
        $valor = $request->valor;
        $all_service = $request->all_service;
        $all_comarca = $request->all_comarca;
        $servicos = $request->servicos;

        if(!empty($request->valor) && !empty($comarca)){

            $valor = str_replace(",", ".", $valor);

            event(new EventNotification(array('canal' => 'notificacao', 'conta' => 999, 'visibilidade' => 1)));

            //Sempre irá atualizar o valor de serviço da comarca selecionada
            $honorario = TaxaHonorario::updateOrCreate(['cd_conta_con' => $this->conta,
                                                        'cd_entidade_ete' => $entidade_correspondente,
                                                        'cd_cidade_cde' => $comarca,
                                                        'cd_tipo_servico_tse' => $servico],
                                                        ['nu_taxa_the' => $valor,
                                                        'dc_observacao_the' => '--']);
               
            //Se a opção de "all_service" estiver marcada, atribui o mesmo valor para todos os servicos da comarca
            if($all_service == 'true')
            {

                for($i = 0; $i < count($servicos); $i++){

                    $honorario = TaxaHonorario::updateOrCreate(['cd_conta_con' => $this->conta,
                                                                'cd_entidade_ete' => $entidade_correspondente,
                                                                'cd_cidade_cde' => $comarca,
                                                                'cd_tipo_servico_tse' => $servicos[$i]],
                                                                ['nu_taxa_the' => $valor,
                                                                'dc_observacao_the' => '--']);

                    event(new EventNotification(array('canal' => 'notificacao', 'conta' => 999, 'total' => $i, 'mensagens' => "")));
                }
                
            }

            //Se a opção de "all_comarca" estiver marcada, atribui o mesmo valor de serviço para todas as comarcas
            if($all_comarca == 'true')
            {
                //Seleciona todas as comarcas do estado selecionado que o correspondente tem atuação
                $comarcas = DB::table('cidade_atuacao_cat')
                            ->select('cidade_cde.cd_cidade_cde','nm_cidade_cde')
                            ->join('cidade_cde', 'cidade_atuacao_cat.cd_cidade_cde', '=', 'cidade_cde.cd_cidade_cde')
                            ->join('estado_est', 'estado_est.cd_estado_est', '=', 'cidade_cde.cd_estado_est')
                            ->where('cd_entidade_ete',$entidade_correspondente)
                            ->where('estado_est.cd_estado_est',$estado)
                            ->get();

                foreach ($comarcas as $comarca) {
                    
                    $honorario = TaxaHonorario::updateOrCreate(['cd_conta_con' => $this->conta,
                                                                'cd_entidade_ete' => $entidade_correspondente,
                                                                'cd_cidade_cde' => $comarca->cd_cidade_cde,
                                                                'cd_tipo_servico_tse' => $servico],
                                                                ['nu_taxa_the' => $valor,
                                                                'dc_observacao_the' => '--']);
                }
            }

            event(new EventNotification(array('canal' => 'notificacao', 'conta' => 999, 'visibilidade' => 0)));
        }        
    }

    public function excluirHonorarios($entidade,$tipo,$id)
    {
        $chave = array();
        $entidade = \Crypt::decrypt($entidade);

        if(!empty($entidade) and !empty($tipo) and !empty($id)){

            if($tipo == 'unico') $chave = explode("-", $id);

            $taxa = TaxaHonorario::where('cd_conta_con',$this->conta)
                                        ->where('cd_entidade_ete',$entidade)
                                        ->when($tipo == 'comarca',
                                            function($q) use($id){
                                                return $q->where('cd_cidade_cde',$id);
                                        })
                                        ->when($tipo == 'servico',
                                            function($q) use($id){
                                                return $q->where('cd_tipo_servico_tse',$id);
                                        })
                                        ->when($tipo == 'unico',
                                            function($q) use($chave){
                                                return $q->where('cd_tipo_servico_tse',$chave[1])->where('cd_cidade_cde', $chave[0]);
                                        })
                                        ->delete();
        }

        if($taxa)
            return Response::json(array('message' => 'Registros excluídos com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir registros'), 500); 

    }

    public function excluirTodosHonorarios(Request $request)
    {
        if(TaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_entidade_ete',$request->entidade_correspondente_excluir)->count()){
        
            $retorno = TaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_entidade_ete',$request->entidade_correspondente_excluir)->delete();

            if($retorno){
                Flash::success('Honorários excluídos com sucesso');
            }else{
                Flash::error('Erro ao excluir honorários');
            }

        }else{
            Flash::warning('Não existem honorários salvos para esse correspondente');
        }

        return redirect('correspondente/honorarios/'.\Crypt::encrypt($request->cd_correspondente_excluir));        
    }
}