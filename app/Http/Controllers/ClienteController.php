<?php

namespace App\Http\Controllers;

use App\Fone;
use App\Cidade;
use App\Cliente;
use App\Contato;
use App\Endereco;
use App\Entidade;
use App\Identificacao;
use App\TipoServico;
use App\GrupoCidade;
use App\TaxaHonorario;
use App\ReembolsoTipoDespesa;
use App\GrupoCidadeRelacionamento;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class ClienteController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $clientes = Cliente::with('tipoPessoa')->take(10)->orderBy('created_at','DESC')->get();

        //dd($clientes);
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function detalhes($id)
    {
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        return view('cliente/detalhes',['cliente' => $cliente]);
    }

    public function honorarios($id)
    {
        $conta = \Session::get('SESSION_CD_CONTA');
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        
        //Dados para combos
        $grupos = GrupoCidade::all();
        $servicos = TipoServico::where('cd_conta_con',$conta)->get();

        //Inicialização de variáveis
        $lista_servicos = array();
        $cidades = array();
        $valores = array();
        $organizar = 0;        

        //Limpa dados da sessão
        \Session::forget('lista_cidades');

        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$conta)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $valores[$honorario->cd_cidade_cde][$honorario->cd_tipo_servico_tse] = $honorario->nu_taxa_the;
            }
        } 

        //Carrega as cidades
        $honorarios = TaxaHonorario::where('cd_conta_con',$cliente->cd_conta_con)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $cidades[] = $honorario->cidade;
            }
        } 

        //Carrega os serviços
        $honorarios = TaxaHonorario::where('cd_conta_con',$cliente->cd_conta_con)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_servicos[] = $honorario->tipoServico;
            }
        } 

        return view('cliente/honorarios',['cliente' => $cliente, 'grupos' => $grupos, 'servicos' => $servicos, 'cidades' => $cidades, 'valores' => $valores, 'organizar' => $organizar, 'lista_servicos' => $lista_servicos]);
    }

    public function buscarHonorarios(Request $request)
    {
        $conta = \Session::get('SESSION_CD_CONTA');
        $id = $request->cd_cliente;
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        $grupo = $request->grupo_cidade;
        $cidade = $request->cd_cidade_cde;
        $servico = $request->servico;
        $organizar = $request->organizar;
        $valores = null;

        $lista_cidades = array();
        $lista_cidades_selecao = array();
        $lista_cidades_grupo = array();
        $lista_cidades_honorarios = array();
        $lista_merge = array();

        $lista_servicos = array();
        

        //Carrega dados do combo        
        $grupos = GrupoCidade::all();
        $servicos = TipoServico::all();

        if(empty(session('lista_cidades'))){
            \Session::put('lista_cidades', array());
        }

        if(empty(session('lista_servicos'))){
            \Session::put('lista_servicos', array());
        }

        //Carrega serviços já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$cliente->cd_conta_con)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_servicos[] = $honorario->tipoServico;
            }
        }

        //Carrega lista de serviços da tabela
        if($servico == 0){
            $lista_servicos = TipoServico::all();
        }else{
            $lista_servicos[] = TipoServico::where('cd_tipo_servico_tse',$servico)->first();
        }

        $lista_temp = array();
        foreach ($lista_servicos as $servico) {
            if(!in_array($servico, $lista_temp))
                $lista_temp[] = $servico;
        }
        $lista_servicos = $lista_temp;

        //Carrega cidades do grupo
        if($grupo > 0 and $cidade == 0) {

            $grupo = GrupoCidadeRelacionamento::with('cidade')->where('cd_grupo_cidade_grc',$grupo)->get();
            foreach($grupo as $g){
                $lista_cidades_grupo[] = $g->cidade()->first();
            }
        }

        //Carrega cidade selecionada        
        if($cidade > 0){
            $lista_cidades_selecao[] = Cidade::where('cd_cidade_cde',$cidade)->first(); 
        }

        //Carrega cidades já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$cliente->cd_conta_con)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_cidades_honorarios[] = $honorario->cidade;
            }
        }

        //Junta os arrays e eleimina duplicidades
        $lista_sessao = session('lista_cidades');
        $lista_merge = array_merge($lista_cidades_selecao, $lista_cidades_grupo, $lista_cidades_honorarios, $lista_sessao);

        foreach ($lista_merge as $cidade) {
            if(!in_array($cidade, $lista_cidades))
                $lista_cidades[] = $cidade;

        }

        //Após o mesge, limpa a sessão para atualizar mais tarde
        \Session::forget('lista_cidades');

        //Ordena a lista de cidades
        usort($lista_cidades,
            function($a, $b) {
                if( $a->nm_cidade_cde == $b->nm_cidade_cde ) return 0;
                return (($a->nm_cidade_cde < $b->nm_cidade_cde) ? -1 : 1);
            }
        );
 
        \Session::put('lista_cidades',$lista_cidades);

        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$conta)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $valores[$honorario->cd_cidade_cde][$honorario->cd_tipo_servico_tse] = $honorario->nu_taxa_the;
            }
            Flash::success('Dados inseridos com sucesso na visualização');
        }  
        
        //Envia dados e renderiza tela
        return view('cliente/honorarios',['cliente' => $cliente, 'grupos' => $grupos, 'servicos' => $servicos, 'lista_servicos' => $lista_servicos, 'organizar' => $organizar, 'cidades' => session('lista_cidades'), 'valores' => $valores]);
    }

    public function limparSelecao($id){
        \Session::forget('lista_cidades');
        return redirect('cliente/honorarios/'.$id);
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function novo()
    {
        return view('cliente/novo');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function listar()
    {
        $clientes = Cliente::take(10)->orderBy('created_at','DESC')->get();
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function buscar(Request $request)
    {
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');
        $tipo = $request->get('tipo_pessoa');
        $situacao = $request->get('situacao');

        $clientes = Cliente::join('entidade_ete', function($join) use($nome, $tipo, $situacao){

                                $join->on('cliente_cli.cd_entidade_ete','=','entidade_ete.cd_entidade_ete');
                                $join->where('entidade_ete.cd_tipo_entidade_tpe','=',8);
                                if(!empty($nome)) $join->where('nm_razao_social_cli','ilike',"%$nome%");
                                if(!empty($tipo)) $join->where('cliente_cli.cd_tipo_pessoa_tpp','=',$tipo);
                                if(!empty($situacao)) $join->where('fl_ativo_cli','=',$situacao);

                            })->leftJoin('identificacao_ide', function($join) use ($identificacao){
                                $join->on('cliente_cli.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                if(!empty($identificacao)) $join->where('nu_identificacao_ide','=',$identificacao);
                            })
                            ->where('cliente_cli.cd_conta_con',$this->conta)
                            ->orderBy('cliente_cli.created_at','DESC')
                            ->get();

        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function store(ClienteRequest $request)
    {

        $cd_conta_con = \Session::get('SESSION_CD_CONTA');

        $dt_inicial = ($request->cd_tipo_pessoa_tpp == 1) ? $request->data_nascimento_cli : $request->data_fundacao_cli;

        $taxa_imposto_cli = str_replace(",", ".", $request->taxa_imposto_cli);

        $request->merge(['nu_cep_ede' => str_replace("-", "", $request->nu_cep_ede)]);
        $request->merge(['cd_conta_con' => $cd_conta_con]);
        $request->merge(['dt_inicial_cli' => $dt_inicial]);
        $request->merge(['taxa_imposto_cli' => $taxa_imposto_cli]);

        $entidade = Entidade::create([
            'cd_conta_con'         => \Session::get('SESSION_CD_CONTA'),
            'cd_tipo_entidade_tpe' => \TipoEntidade::CLIENTE
        ]);

        $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

        $cliente = new Cliente();
        $cliente->fill($request->all());

        if($cliente->saveOrFail()){

            $nu_identificacao_ide = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;

            $identificacao = Identificacao::create([
                'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                'cd_conta_con'              => $cd_conta_con, 
                'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                'nu_identificacao_ide'      => (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : ''
            ]);

            if(!empty($request->dc_logradouro_ede)){

                $endereco = new Endereco();
                $endereco->fill($request->all());
                $endereco->saveOrFail();

            }

            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $cd_conta_con, 
                        'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                        'nu_fone_fon'               => $fones[$i]->numero
                    ]);

                }
            }

            if(!empty($request->despesas)){

                $despesas = $request->despesas;
                for($i = 0; $i < count($despesas); $i++) {

                    $reembolso = ReembolsoTipoDespesa::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $cd_conta_con, 
                        'cd_tipo_despesa_tds'       => $despesas[$i]
                    ]);
                }

            }

            Flash::success('Dados inseridos com sucesso');
            return redirect('cliente/detalhes/'.$cliente->cd_cliente_cli);
        }
        else{
            Flash::error('Erro ao atualizar dados');
            return redirect('clientes');
        }

    }

    public function editar($id){

        $cliente = Cliente::where('cd_cliente_cli',$id)->first();
        return view('cliente/editar',['cliente' => $cliente]);

    }

    public function update(ClienteRequest $request, $id)
    {
        $cliente = Cliente::where('cd_cliente_cli',$id)->first();
        $cliente->fill($request->all());
        
        if($cliente->saveOrFail()){

            $identificacao = (Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first()) ? Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first() : $identificacao = Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CNPJ)->first();

            $nu_identificacao_ide = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;

            if($identificacao){

                $identificacao->cd_tipo_identificacao_tpi = ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ;
                $identificacao->nu_identificacao_ide = (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : '';
                $identificacao->saveOrFail();

            }else{

                $identificacao = Identificacao::create([
                'cd_entidade_ete'           => $cliente->entidade->cd_entidade_ete,
                'cd_conta_con'              => $cliente->cd_conta_con, 
                'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                'nu_identificacao_ide'      => (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : ''
                ]);
            }            

            Flash::success('Dados atualizados com sucesso');
        }
        else
            Flash::error('Erro ao atualizar dados');

        return redirect('cliente/detalhes/'.$cliente->cd_cliente_cli);
    }

    public function destroy($id)
    {

        $cliente = Cliente::where('cd_cliente_cli',$id)->first();

        if($cliente->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        
    }

    public function search(Request $request)
    {
        $search = $request->get('term');
      
        $resultados = Cliente::where('nm_razao_social_cli', 'ilike', '%'. $search. '%')->orWhere('nm_fantasia_cli', 'ilike', '%'. $search. '%')->get();

        $results = array();
        foreach ($resultados as $ret)
        {

            if(!empty($ret->nm_fantasia_cli)){
                $nome =  $ret->nu_cliente_cli.' - '.$ret->nm_razao_social_cli.' ('.$ret->nm_fantasia_cli.')';
            }else{
                $nome = $ret->nu_cliente_cli.' - '.$ret->nm_razao_social_cli;
            }
            
           $results[] = [ 'id' => $ret->cd_cliente_cli, 'value' => $nome ];
        }
 
        return response()->json($results);
            
    } 

    public function salvarHonorarios(Request $request){

        $conta = \Session::get('SESSION_CD_CONTA');
        $entidade = $request->entidade;

        if(!empty($request->valores) && count(json_decode($request->valores)) > 0){

            $valores = json_decode($request->valores);
                
            for($i = 0; $i < count($valores); $i++) {

                $valor = TaxaHonorario::where('cd_conta_con',$conta)
                                      ->where('cd_entidade_ete',$entidade)
                                      ->where('cd_cidade_cde',$valores[$i]->cidade)
                                      ->where('cd_tipo_servico_tse',$valores[$i]->servico)->first();

                if(!empty($valor)){

                    $valor->nu_taxa_the = str_replace(",", ".", $valores[$i]->valor);
                    $valor->saveOrFail();

                }else{

                    $taxa = TaxaHonorario::create([
                        'cd_entidade_ete'           => $entidade,
                        'cd_conta_con'              => $conta, 
                        'cd_tipo_servico_tse'       => $valores[$i]->servico,
                        'cd_cidade_cde'             => $valores[$i]->cidade,
                        'nu_taxa_the'               => str_replace(",", ".", $valores[$i]->valor),
                        'dc_observacao_the'         => "--"
                    ]);
                }
            }
        }
    }

    public function buscaAdvogados($cliente){
        $conta = \Session::get('SESSION_CD_CONTA');
        $cliente = Cliente::where('cd_conta_con',$conta)->find($cliente);

        $contatos = Contato::where('cd_conta_con',$conta)
                           ->where('cd_tipo_contato_tct', \TipoContato::ADVOGADO)
                           ->where('cd_entidade_ete', $cliente->cd_entidade_ete)
                           ->get();

        echo json_encode($contatos);
    }
}