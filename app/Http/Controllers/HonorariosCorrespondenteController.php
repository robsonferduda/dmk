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
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
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

        $valores = array();
        $comarcas = array();
        $servicos = array();
        $id = \Crypt::decrypt($request->id);
        $cd_correspondente = $request->correspondente;
        
        $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor',$cd_correspondente)->first(); 
        
        $cidade = $request->lista_cidades;
        $servico = $request->lista_servicos;

        $lista_cidades = array();
        $lista_cidades_selecao = array();
        $lista_cidades_honorarios = array();
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
            $lista_cidades_selecao[] = Cidade::where('cd_cidade_cde',$cidade)->first(); 
        }elseif($cidade == 0){

            $correspondente = ContaCorrespondente::where('cd_conta_con', $this->conta)->where('cd_correspondente_cor',$correspondente->cd_correspondente_cor)->first();
            $atuacao = $correspondente->entidade->atuacao()->get();

            foreach ($atuacao as $a) {
                $lista_cidades_honorarios[] = $a->cidade;
            }

        }

        //Carrega cidades já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_cidades_honorarios[] = $honorario->cidade;
            }
        }

        //Junta os arrays e eleimina duplicidades
        $lista_merge = array_merge($lista_cidades_selecao, $lista_cidades_honorarios);

        foreach ($lista_merge as $cidade) {
            if(!in_array($cidade, $lista_cidades))
                $lista_cidades[] = $cidade;

        }

        //Ordena a lista de cidades
        usort($lista_cidades,
            function($a, $b) {

                $a = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $a->nm_cidade_cde ) );
                $b = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $b->nm_cidade_cde ) );

                if( $a == $b ) return 0;
                return (($a < $b) ? -1 : 1);
            }
        );

        //Ordena a lista de cidades
        usort($lista_servicos,
            function($a, $b) {

                $a = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $a->nm_tipo_servico_tse ) );
                $b = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $b->nm_tipo_servico_tse ) );

                if( $a == $b ) return 0;
                return (($a < $b) ? -1 : 1);
            }
        );
 
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

        $entidade = $request->entidade;
        //dd($request->all());
        //HonorarioCorrespondenteJob::dispatch($request);

        if(!empty($request->valores) && count(json_decode($request->valores)) > 0){

            $valores = json_decode($request->valores);
                
            for($i = 0; $i < count($valores); $i++) {

                $valor = TaxaHonorario::where('cd_conta_con',$this->conta)
                                      ->where('cd_entidade_ete',$entidade)
                                      ->where('cd_cidade_cde',$valores[$i]->cidade)
                                      ->where('cd_tipo_servico_tse',$valores[$i]->servico)->first();

                if(!empty($valor)){

                    $valor->nu_taxa_the = str_replace(",", ".", $valores[$i]->valor);
                    $valor->saveOrFail();

                }else{

                    $taxa = TaxaHonorario::create([
                        'cd_entidade_ete'           => $entidade,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_servico_tse'       => $valores[$i]->servico,
                        'cd_cidade_cde'             => $valores[$i]->cidade,
                        'nu_taxa_the'               => str_replace(",", ".", $valores[$i]->valor),
                        'dc_observacao_the'         => "--"
                    ]);
                }
            }
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