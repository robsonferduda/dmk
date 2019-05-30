<?php

namespace App\Http\Controllers;

use DB;
use App\Fone;
use App\Cidade;
use App\Cliente;
use App\Contato;
use App\Endereco;
use App\Entidade;
use App\EnderecoEletronico;
use App\Identificacao;
use App\TipoContato;
use App\TipoServico;
use App\TipoDespesa;
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
        $clientes = Cliente::with('entidade')->with('tipoPessoa')->where('cd_conta_con', $this->conta)->take(10)->orderBy('created_at','DESC')->get();
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function detalhes($id)
    {
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        return view('cliente/detalhes',['cliente' => $cliente]);
    }

    public function contatos($id)
    {
        Session::put('inicial',NULL);

        $dados = array();

        $nomeCliente = '';
        $codCliente = '';

        $tiposContato = TipoContato::where('cd_conta_con', $this->conta)->orderBy('nm_tipo_contato_tct')->get();


        $dados = DB::table('contato_cot')
                        ->leftJoin('tipo_contato_tct','tipo_contato_tct.cd_tipo_contato_tct','=','contato_cot.cd_tipo_contato_tct')
                        ->leftJoin('endereco_ede','endereco_ede.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete')
                        ->leftJoin('vi_fone_max_create_entidate_fon', function($join){
                            $join->on('vi_fone_max_create_entidate_fon.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_fone_max_create_entidate_fon.deleted_at');
                        })
                        ->leftJoin('vi_endereco_eletronico_max_create_entidate_ele', function($join){
                            $join->on('vi_endereco_eletronico_max_create_entidate_ele.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_endereco_eletronico_max_create_entidate_ele.deleted_at');
                        })
                        ->leftJoin('cidade_cde','cidade_cde.cd_cidade_cde','=','endereco_ede.cd_cidade_cde');
                        if(!empty($id)){
                            $dados->leftJoin('cliente_cli','cliente_cli.cd_entidade_ete','=','contato_cot.cd_entidade_ete')
                                 ->where('cliente_cli.cd_entidade_ete', $id);
                            
                            $cliente = Cliente::where('cd_conta_con',$this->conta)->where('cd_entidade_ete', $id)->first();

                            $codCliente = $cliente->cd_cliente_cli;

                            if(!empty($cliente->nm_fantasia_cli)){
                                $nomeCliente =  $cliente->nu_cliente_cli.' - '.$cliente->nm_razao_social_cli.' ('.$cliente->nm_fantasia_cli.')';
                            }else{
                                $nomeCliente = $cliente->nu_cliente_cli.' - '.$cliente->nm_razao_social_cli;
                            }
                            

                        }
            $dados   =  $dados->where('contato_cot.cd_conta_con',$this->conta)
                        ->whereNull('contato_cot.deleted_at')
                        ->select('contato_cot.cd_contato_cot','contato_cot.nm_contato_cot','nm_tipo_contato_tct','nm_cidade_cde','nu_fone_fon','dc_endereco_eletronico_ede')
                        ->get();

        return view('contato/index',['dados' => $dados, 'codCliente' => $codCliente, 'nomeCliente' => $nomeCliente, 'entidade' => $id, 'tiposContato' => $tiposContato]);
    }

    public function buscarContato($cliente,$inicial)
    {
        Session::put('inicial',$inicial); 
        return redirect('cliente/contatos/'.$cliente);
    }

    public function novoContato($id){

        $tipos = TipoContato::where('cd_conta_con',$this->conta)->get();
        $cliente =  Cliente::whereHas('entidade' , function($query) use ($id){
            $query->where('cd_entidade_ete',$id);

        })->first();

        return view('cliente/novo-contato',['tipos' => $tipos, 'cliente' => $cliente]);
    }

    public function createContato(Request $request,$id)
    {
        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);
        $request->merge(['cd_conta_con' => $this->conta]);

        DB::transaction(function() use ($request, $id){
        
            $entidade = new Entidade;
            $entidade->cd_conta_con = $this->conta;
            $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTATO;
            $entidade->saveOrFail();

            if($entidade->cd_entidade_ete){

                $c = Contato::create([
                    'cd_conta_con'              => $this->conta, 
                    'cd_entidade_ete'           => $id,
                    'cd_entidade_contato_ete'   => $entidade->cd_entidade_ete,
                    'cd_tipo_contato_tct'       => $request->cd_tipo_contato_tct,
                    'nm_contato_cot'            => $request->nm_contato_cot,
                    'dc_observacao_cot'         => $request->dc_observacao_cot
                ]);

                if(!empty($request->dc_logradouro_ede)){

                    $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

                    $endereco = new Endereco();
                    $endereco->fill($request->all());
                    $endereco->saveOrFail(); 

                    if(!$endereco){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('contato/novo');
                    }  
                }

                if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                    $fones = json_decode($request->telefones);
                    for($i = 0; $i < count($fones); $i++) {

                        $fone = Fone::create([
                            'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                            'cd_conta_con'              => $this->conta, 
                            'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                            'nu_fone_fon'               => $fones[$i]->numero
                        ]);

                        if(!$fone){
                            DB::rollBack();
                            Flash::error('Erro ao cadastrar telefone');
                            return redirect('contato/novo');
                        } 

                    }
                }

                //Inserção de emails
                if(!empty($request->emails) && count(json_decode($request->emails)) > 0){

                    $emails = json_decode($request->emails);
                    for($i = 0; $i < count($emails); $i++) {

                        $email = EnderecoEletronico::create([
                            'cd_entidade_ete'                 => $entidade->cd_entidade_ete,
                            'cd_conta_con'                    => $this->conta, 
                            'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                            'dc_endereco_eletronico_ede'      => $emails[$i]->email
                        ]);

                        if(!$email){
                            DB::rollBack();
                            Flash::error('Erro ao cadastrar email');
                            return redirect('contato/novo');
                        } 

                    }
                }

            }     
        });

        Flash::success('Contato inserido com sucesso');
        return redirect('cliente/contatos/'.$id);
    }

    public function honorarios($id)
    {
        //Inicialização de variáveis
        $cidades = array();
        $valores = array();    
        $lista_servicos = array();

        if(empty(\Session::get('organizar')))
            \Session::put('organizar',1);

        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        
        //Dados para combos
        $grupos = GrupoCidade::where('cd_conta_con',$this->conta)->get();
        $servicos = TipoServico::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_servico_tse')->get();

        //Limpa dados da sessão
        \Session::forget('lista_cidades');

        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
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

        return view('cliente/honorarios',['cidades' => $cidades,
                                          'cliente' => $cliente, 
                                          'grupos' => $grupos, 
                                          'servicos' => $servicos,                                            
                                          'valores' => $valores, 
                                          'organizar' => \Session::get('organizar'), 
                                          'lista_servicos' => $lista_servicos]);
    }

    public function organizar(Request $request)
    {
        $cliente = $request->cd_cliente;
        \Session::put('organizar',$request->organizar);
        return redirect('cliente/honorarios/'.$cliente);
    }

    public function buscarHonorarios(Request $request)
    {
        $id = $request->cd_cliente;
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        $grupo = $request->grupo_cidade;
        $cidade = $request->cd_cidade_cde;
        $servico = $request->servico;
        $organizar = \Session::get('organizar');
        $valores = null;

        $lista_cidades = array();
        $lista_cidades_selecao = array();
        $lista_cidades_grupo = array();
        $lista_cidades_honorarios = array();
        $lista_merge = array();

        $lista_servicos = array();        

        //Carrega dados do combo        
        $grupos = GrupoCidade::where('cd_conta_con',$this->conta)->get();
        $servicos = TipoServico::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_servico_tse')->get();

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
            $lista_servicos = TipoServico::where('cd_conta_con',$this->conta)->get();
        }else{
            $lista_servicos[] = TipoServico::where('cd_tipo_servico_tse',$servico)->first();
        }

        $lista_temp = array();
        foreach ($lista_servicos as $servico) {
            if(!in_array($servico, $lista_temp))
                $lista_temp[] = $servico;
        }
        $lista_servicos = $lista_temp;

        usort($lista_servicos,
            function($a, $b) {
                if( $a->nm_tipo_servico_tse == $b->nm_tipo_servico_tse ) return 0;
                return (($a->nm_tipo_servico_tse < $b->nm_tipo_servico_tse) ? -1 : 1);
            }
        );

        //Carrega cidades do grupo
        if($grupo > 0 and $cidade == 0) {

            $grupo = GrupoCidadeRelacionamento::with('cidade')->where('cd_grupo_cidade_grc',$grupo)->get();
         
            foreach($grupo as $g){
                $lista_cidades_grupo[] = $g->cidade;
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

                $a = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $a->nm_cidade_cde ) );
                $b = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $b->nm_cidade_cde ) );

                if( $a == $b ) return 0;
                return (($a < $b) ? -1 : 1);
            }
        );
 
        \Session::put('lista_cidades',$lista_cidades);

        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $valores[$honorario->cd_cidade_cde][$honorario->cd_tipo_servico_tse] = $honorario->nu_taxa_the;
            }
            Flash::success('Dados inseridos com sucesso na visualização');
        }  
        
        //Envia dados e renderiza tela
        return view('cliente/honorarios',['cidades' => session('lista_cidades'),
                                          'cliente' => $cliente, 
                                          'grupos' => $grupos, 
                                          'servicos' => $servicos, 
                                          'valores' => $valores,
                                          'organizar' => $organizar,
                                          'lista_servicos' => $lista_servicos                                           
                                          ]);
    }

    public function excluirHonorarios($entidade,$tipo,$id)
    {
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
                                    ->delete();

        if($taxa)
            return Response::json(array('message' => 'Registros excluídos com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500); 

    }

    public function limparSelecao($id){
        \Session::forget('lista_cidades');
        return redirect('cliente/honorarios/'.$id);
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function novo()
    {
        $despesas = TipoDespesa::where('cd_conta_con', $this->conta)->where('fl_reembolso_tds','S')->get();
        return view('cliente/novo',['despesas' => $despesas]);
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function listar()
    {
        $clientes = Cliente::with('entidade')->where('cd_conta_con',$this->conta)->take(10)->orderBy('created_at','DESC')->get();
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
                                if(!empty($nome)) $join->where('nm_razao_social_cli','ILIKE','%'.$nome.'%');
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
        $taxa_imposto_cli = ($request->taxa_imposto_cli) ? str_replace(",", ".", $request->taxa_imposto_cli) : null;
        $cep = ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null;

        $request->merge(['nu_cep_ede' => $cep]);
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
        $despesas = TipoDespesa::where('cd_conta_con', $this->conta)->where('fl_reembolso_tds','S')->get();
        $despesas_selecionadas = array();
        $disponiveis = array();

        $despesas_cliente = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->get();

        foreach ($despesas_cliente as $d) {
            $despesas_selecionadas[] = $d->TipoDespesa()->first();
        }

        foreach ($despesas as $t) {
            $disponiveis[] = $t;
        }

        $despesas = array_udiff($disponiveis, $despesas_selecionadas,
                                  function ($obj_a, $obj_b) {
                                    return $obj_a->cd_tipo_despesa_tds - $obj_b->cd_tipo_despesa_tds;
                                  }
                                );

        return view('cliente/editar',['cliente' => $cliente, 'despesas' => $despesas, 'despesas_selecionadas' => $despesas_selecionadas]);

    }

    public function update(ClienteRequest $request, $id)
    {
        $cliente = Cliente::where('cd_cliente_cli',$id)->first();

        $dt_inicial = ($request->cd_tipo_pessoa_tpp == 1) ? date('Y-m-d',strtotime(str_replace('/','-',$request->data_nascimento_cli))) : date('Y-m-d',strtotime(str_replace('/','-',$request->data_fundacao_cli)));
        $taxa_imposto_cli = ($request->taxa_imposto_cli) ? str_replace(",", ".", $request->taxa_imposto_cli) : null;
        $cep = ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null;

        $request->merge(['nu_cep_ede' => $cep]);
        $request->merge(['cd_conta_con' => $this->conta]);
        $request->merge(['dt_inicial_cli' => $dt_inicial]);
        $request->merge(['taxa_imposto_cli' => $taxa_imposto_cli]);
        $request->merge(['cd_entidade_ete' => $cliente->entidade->cd_entidade_ete]);

        $cliente->fill($request->all());
        
        if($cliente->saveOrFail()){

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if(!empty($request->dc_logradouro_ede)){

                $endereco = Endereco::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->first();

                if($endereco){
                        
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();

                }else{

                        $endereco = new Endereco();
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();
                }
                    
            }

            //Inserção de telefones
            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $cliente->entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                        'nu_fone_fon'               => $fones[$i]->numero
                    ]);

                }
            }

            //Inserção de emails
            if(!empty($request->emails) && count(json_decode($request->emails)) > 0){

                $emails = json_decode($request->emails);
                for($i = 0; $i < count($emails); $i++) {

                    $email = EnderecoEletronico::create([
                        'cd_entidade_ete'                 => $cliente->entidade->cd_entidade_ete,
                        'cd_conta_con'                    => $this->conta, 
                        'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                        'dc_endereco_eletronico_ede'      => $emails[$i]->email
                    ]);

                }
            }

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

            //Gerenciamento das despesas do cliente
            $selecionadas = array();
            $despesas_cliente = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->get();
            $despesas_remover = $request->remover;
            $despesas_adicionar = $request->despesas;

            foreach ($despesas_cliente as $d) {
                $selecionadas[] = $d->TipoDespesa()->first()->cd_tipo_despesa_tds;
            }

            if($despesas_remover == null){ //Remover tudo

                for ($i=0; $i < count($selecionadas); $i++){ 
                    
                    $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->where('cd_tipo_despesa_tds',$selecionadas[$i])->first();
                    $despesa->delete();
                }

            }else{

                $diferenca = array_diff($selecionadas, $despesas_remover);

                if(count($diferenca) > 0){

                    $valores = array_values($diferenca);
                    for ($i=0; $i < count($valores); $i++) { 
                        
                        $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->where('cd_tipo_despesa_tds',$valores[$i])->first();
                        $despesa->delete();

                    }

                }

            }

            //Adiciona as novas despesas que foram marcadas
            if(!empty($despesas_adicionar)){

                for($i = 0; $i < count($despesas_adicionar); $i++) {

                    $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$cliente->entidade->cd_entidade_ete)->where('cd_tipo_despesa_tds',$despesas_adicionar[$i])->first();

                    if(!$despesa){

                        $reembolso = ReembolsoTipoDespesa::create([
                            'cd_entidade_ete'           => $cliente->entidade->cd_entidade_ete,
                            'cd_conta_con'              => $this->conta, 
                            'cd_tipo_despesa_tds'       => $despesas_adicionar[$i]
                        ]);
                    }
                }
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
      
        $resultados = Cliente::where('nm_razao_social_cli', 'ilike', '%'. $search. '%')->orWhere('nm_fantasia_cli', 'ilike', '%'. $search. '%')->orWhere('nu_cliente_cli', 'ilike', '%'. $search. '%')->select('cd_entidade_ete','cd_cliente_cli','nm_razao_social_cli','nm_fantasia_cli','taxa_imposto_cli','nu_cliente_cli')->get();

        $results = array();
        foreach ($resultados as $ret)
        {

            if(!empty($ret->nm_fantasia_cli)){
                $nome =  $ret->nu_cliente_cli.' - '.$ret->nm_razao_social_cli.' ('.$ret->nm_fantasia_cli.')';
            }else{
                $nome = $ret->nu_cliente_cli.' - '.$ret->nm_razao_social_cli;
            }
            
           $results[] = [ 'id' => $ret->cd_cliente_cli, 'value' => $nome, 'nota' => $ret->taxa_imposto_cli, 'entidade' => $ret->cd_entidade_ete ];
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

                    $valor->nu_taxa_the = ($valores[$i]->valor) ? str_replace(",", ".", $valores[$i]->valor) : 0;
                    $valor->saveOrFail();

                }else{

                    $taxa = TaxaHonorario::create([
                        'cd_entidade_ete'           => $entidade,
                        'cd_conta_con'              => $conta, 
                        'cd_tipo_servico_tse'       => $valores[$i]->servico,
                        'cd_cidade_cde'             => $valores[$i]->cidade,
                        'nu_taxa_the'               => ($valores[$i]->valor) ? str_replace(",", ".", $valores[$i]->valor) : 0,
                        'dc_observacao_the'         => "--"
                    ]);
                }
            }
        }
    }

    public function buscaAdvogados($cliente){
        $conta = \Session::get('SESSION_CD_CONTA');
        $cliente = Cliente::where('cd_conta_con',$conta)->find($cliente);
        $contatos = Contato::whereHas('tipoContato' , function($query){
            $query->where('fl_tipo_padrao_tct','S');

        })->where('cd_conta_con',$conta)
          ->where('cd_entidade_ete', $cliente->cd_entidade_ete)
          ->select('cd_contato_cot', 'nm_contato_cot')->get()->toJson();

        echo $contatos;
    }
}