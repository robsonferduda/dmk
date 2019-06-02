<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use Hash;
use App\Enums\Nivel;
use App\Enums\Roles;
use App\Enums\TipoEnderecoEletronico;
use App\Fone;
use App\User;
use App\Conta;
use App\Endereco;
use App\Entidade;
use App\Cidade;
use App\GrupoCidade;
use App\CidadeAtuacao;
use App\Correspondente;
use App\TipoDespesa;
use App\TipoServico;
use App\TaxaHonorario;
use App\ContaCorrespondente;
use App\EnderecoEletronico;
use App\ReembolsoTipoDespesa;
use App\Identificacao;
use App\RegistroBancario;
use Kodeine\Acl\Models\Eloquent\Role;
use Illuminate\Http\Request;
use App\Http\Requests\ConviteRequest;
use App\Http\Requests\CorrespondenteRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;

class CorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth',['except' => ['cadastro']]);
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $correspondentes = Correspondente::whereHas('contaCorrespondente', function($sql){

                                $sql->where('cd_conta_con',$this->conta);

                            })
                            ->with('contaCorrespondente')
                            ->with('entidade')
                            ->with('entidade.identificacao')
                            ->with('entidade.usuario')
                            ->where('fl_correspondente_con','S')
                            ->orderBy('conta_con.nm_razao_social_con','DESC')
                            ->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);
    }

    public function painel()
    {
        return view('correspondente/painel');
    }

    public function detalhes($id)
    {
        $correspondente = Correspondente::where('cd_conta_con',$id)->first();        
        return view('correspondente/detalhes',['correspondente' => $correspondente]);
    }

    public function dados($id)
    {
        $correspondente = Correspondente::where('cd_conta_con',$id)->first();
        $endereco = ($correspondente->entidade()) ? Endereco::where('cd_entidade_ete',$correspondente->entidade()->first()->cd_entidade_ete)->first() : null;

        $dados = array('dados' => $correspondente->toArray(), 'endereco' => $endereco);

        echo json_encode($dados);
    }

    public function despesas($id)
    {
        $selecionadas = array();
        $disponiveis = array();
        $correspondente = Correspondente::where('cd_conta_con',$id)->first();

        $despesas = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->get();
        $todas = TipoDespesa::where('cd_conta_con',$this->conta)->where('fl_reembolso_tds','S')->get();

        foreach ($despesas as $d) {
            $selecionadas[] = $d->TipoDespesa()->first();
        }

        foreach ($todas as $t) {
            $disponiveis[] = $t;
        }

        $despesas_disponiveis = array_udiff($disponiveis, $selecionadas,
                                  function ($obj_a, $obj_b) {
                                    return $obj_a->cd_tipo_despesa_tds - $obj_b->cd_tipo_despesa_tds;
                                  }
                                );

        return view('correspondente/despesas',['correspondente' => $correspondente, 'despesas' => $despesas, 'despesas_disponiveis' => $despesas_disponiveis ]);

    }

    public function adicionarDespesas(Request $request)
    {

        $selecionadas = array();
        $removidas = $request->remover;
        $despesas = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$request->entidade)->get();

        //Verifica se alguma das opções marcadas foi removida. Se sim, remove do banco.
        foreach ($despesas as $d) {
            $selecionadas[] = $d->TipoDespesa()->first()->cd_tipo_despesa_tds;
        }

        if($removidas == null){ //Remover tudo

            for ($i=0; $i < count($selecionadas); $i++){ 
                
                $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$request->entidade)->where('cd_tipo_despesa_tds',$selecionadas[$i])->first();
                $despesa->delete();
            }

        }else{

            $diferenca = array_diff($selecionadas, $removidas);

            if(count($diferenca) > 0){

                $valores = array_values($diferenca);
                for ($i=0; $i < count($valores); $i++) { 
                    
                    $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$request->entidade)->where('cd_tipo_despesa_tds',$valores[$i])->first();
                    $despesa->delete();

                }

            }

        }

        //Adiciona as novas despesas que foram marcadas
        if(!empty($request->despesas)){

            $despesas = $request->despesas;
            for($i = 0; $i < count($despesas); $i++) {

                $despesa = ReembolsoTipoDespesa::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$request->entidade)->where('cd_tipo_despesa_tds',$despesas[$i])->first();

                if(!$despesa){

                    $reembolso = ReembolsoTipoDespesa::create([
                        'cd_entidade_ete'           => $request->entidade,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_despesa_tds'       => $despesas[$i]
                    ]);
                }
            }
        }
        return redirect('correspondente/despesas/'.$request->conta);
    }

    public function honorarios($id)
    {

        //Inicialização de variáveis
        $lista_servicos = array();
        $cidades = array();
        $valores = array();
        $organizar = 1;  
        $correspondente = Correspondente::with('entidade')->where('cd_conta_con',$id)->first();
        
        //Dados utilizados pelo combo
        $servicos = TipoServico::where('cd_conta_con',$this->conta)->get();

        //Limpa dados da sessão
        \Session::forget('lista_cidades');

        //Carrega os valores de honorarios para determinado grupo
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $valores[$honorario->cd_cidade_cde][$honorario->cd_tipo_servico_tse] = $honorario->nu_taxa_the;
            }
        } 

        //Carrega as cidades
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $cidades[] = $honorario->cidade;
            }
        } 

        //Carrega os serviços
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $lista_servicos[] = $honorario->tipoServico;
            }
        } 

        return view('correspondente/honorarios',['cliente' => $correspondente, 'servicos' => $servicos, 'cidades' => $cidades, 'valores' => $valores, 'organizar' => $organizar, 'lista_servicos' => $lista_servicos]);

    }

    public function buscarHonorarios(Request $request)
    {
        $cd_correspondente = $request->cd_correspondente;
        $correspondente = Correspondente::with('entidade')->where('cd_conta_con',$cd_correspondente)->first();
        
        $cidade = $request->cd_cidade_cde;
        $servico = $request->servico;
        $organizar = $request->organizar;
        $valores = null;

        $lista_cidades = array();
        $lista_cidades_selecao = array();
        $lista_cidades_honorarios = array();
        $lista_merge = array();

        $lista_servicos = array();        

        //Carrega dados do combo        
        $servicos = TipoServico::where('cd_conta_con',$this->conta)->get();

        if(empty(session('lista_cidades'))){
            \Session::put('lista_cidades', array());
        }

        if(empty(session('lista_servicos'))){
            \Session::put('lista_servicos', array());
        }

        //Carrega serviços já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)
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
            $lista_servicos = $servicos;
        }else{
            $lista_servicos[] = TipoServico::where('cd_tipo_servico_tse',$servico)->first();
        }

        $lista_temp = array();
        foreach ($lista_servicos as $servico) {
            if(!in_array($servico, $lista_temp))
                $lista_temp[] = $servico;
        }
        $lista_servicos = $lista_temp;

        //Carrega cidade selecionada        
        if($cidade > 0){
            $lista_cidades_selecao[] = Cidade::where('cd_cidade_cde',$cidade)->first(); 
        }

        //Carrega cidades já cadastradas
        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)
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
        $lista_merge = array_merge($lista_cidades_selecao, $lista_cidades_honorarios, $lista_sessao);

        foreach ($lista_merge as $cidade) {
            if(!in_array($cidade, $lista_cidades))
                $lista_cidades[] = $cidade;

        }

        //Após o merge, limpa a sessão para atualizar mais tarde
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
                                    ->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $valores[$honorario->cd_cidade_cde][$honorario->cd_tipo_servico_tse] = $honorario->nu_taxa_the;
            }
            Flash::success('Dados inseridos com sucesso na visualização');
        }  
        
        //Envia dados e renderiza tela
        return view('correspondente/honorarios',['cliente' => $correspondente, 'servicos' => $servicos, 'lista_servicos' => $lista_servicos, 'organizar' => $organizar, 'cidades' => session('lista_cidades'), 'valores' => $valores]);
    }

    public function limparSelecao($id){
        \Session::forget('lista_cidades');
        return redirect('correspondente/honorarios/'.$id);
    }

    public function salvarHonorarios(Request $request){

        $entidade = $request->entidade;

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

    public function buscar(Request $request){

        $estado = $request->get('cd_estado_est');
        $cidade = $request->get('cd_cidade_cde');
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');

        $correspondentes = Correspondente::whereHas('contaCorrespondente', function($sql) use($nome){

                                if(!empty($nome)) $sql->where('nm_razao_social_con','ilike',"%$nome%");
                                $sql->where('cd_conta_con',$this->conta);

                                if(!empty($identificacao)){

                                    $join('entidade_ete', function($join) use ($identificacao){
                                    
                                        $join->on('conta_con.cd_conta_con','=','entidade_ete.cd_conta_con');
                                        $join->join('identificacao_ide', function($join) use ($identificacao){
                                            $join->on('entidade_ete.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                            $join->where('nu_identificacao_ide','=',$identificacao);
                                        });
                                    }); 
                                }
                            })
                            ->with('entidade')
                            ->with('entidade.usuario')
                            ->with('contaCorrespondente')
                            ->with('entidade.identificacao')
                            ->where('fl_correspondente_con','S')
                            ->orderBy('conta_con.nm_razao_social_con','DESC')
                            ->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);

    }

    public function buscarTodos(Request $request){

        $nome = $request->get('nome');
        $email = $request->get('email');
        $identificacao = $request->get('identificacao');
        $correspondentes = array();

        if(empty($nome) and empty($email) and empty($identificacao)){
            Flash::warning('Preencha pelo menos um termo para a busca');
            return view('correspondente/novo',['correspondetes' => $correspondentes]);
        }

        $correspondentes = Correspondente::join('entidade_ete', function($join) use ($nome, $email, $identificacao){
                                    
                                    $join->on('conta_con.cd_conta_con','=','entidade_ete.cd_conta_con');
                                    $join->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE);

                                    if(!empty($nome)) $join->where('nm_razao_social_con','ilike','%'.$nome.'%');
                                    
                                    if(!empty($identificacao)){
                                        $join->join('identificacao_ide', function($join) use ($identificacao){
                                            $join->on('entidade_ete.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                            $join->where('nu_identificacao_ide','=',$identificacao);
                                        });
                                    } 

                                    if(!empty($email)){
                                        $join->join('users', function($join) use ($email){
                                            $join->on('entidade_ete.cd_entidade_ete','=','users.cd_entidade_ete');
                                            $join->where('email','=',$email);
                                        });
                                    }

                            })
                            ->orderBy('conta_con.nm_fantasia_con','DESC')
                            ->get();

        if(count($correspondentes) == 0)
            \Session::put('busca_vazia', true);

        return view('correspondente/novo',['correspondetes' => $correspondentes]);

    }

    public function novo(){
        return view('correspondente/novo');
    }

    public function convidar(ConviteRequest $request){

        $to_name = 'Convidado';
        $to_email = $request->email;
        
        $data = array('nome'=>Auth::user()->name);

        /*
        $unique = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('email',$request->email)->first(); 
        if(!empty($unique)){
            Flash::warning('Email já cadastrado no sistema. Utilize a busca para encontrar o registro.');
            return redirect('correspondente/novo');
        }
        */
            
        Mail::send('correspondente/email_convite', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('Cadastro Sistema DMK');
            $message->from('financeiro@dmkadvogados.com.br','Atendimento DMK');
            Flash::success('Convite enviado com sucesso. O destinatário poderá realizar seu cadastro para aparecer nas buscas por correspondentes.');
        });

        if(Mail::failures()){


        }

        return redirect('correspondente/novo');

    }

    public function novoCorrespondenteConta(Request $request)
    {
        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];

            $conta = new Correspondente();        
            $conta->fill($request->all());
            $conta->fl_correspondente_con = "S";
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CORRESPONDENTE;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::CORRESPONDENTE;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make("correspondente");
                    $user->save();

                    if($user->id){

                        $role = Role::find(Roles::CORRESPONDENTE);
                        $user->assignRole($role);

                        $enderecoEletronico = new EnderecoEletronico();
                        $enderecoEletronico->cd_conta_con = $conta->cd_conta_con;
                        $enderecoEletronico->cd_entidade_ete = $entidade->cd_entidade_ete;
                        $enderecoEletronico->cd_tipo_endereco_eletronico_tee = TipoEnderecoEletronico::NOTIFICACAO;
                        $enderecoEletronico->dc_endereco_eletronico_ede = $email;
                        $enderecoEletronico->save();
                    }

                    //Após cadastrar, vincula a conta que realizou o cadastro
                    $correspondente = new ContaCorrespondente();
                    $correspondente->cd_conta_con = $this->conta;
                    $correspondente->cd_correspondente_cor = $conta->cd_conta_con;        
                    
                    if($correspondente->save())
                        Flash::success('Correspondente adicionado com sucesso');
                    else{
                        Flash::error('Erro ao adicionar correspondente');
                        return redirect()->back();
                    }
                }
            }
        });

        return redirect('correspondentes');
    }

    public function cadastro(CorrespondenteRequest $request){

        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];

            $conta = new Correspondente();        
            $conta->fill($request->all());
            $conta->fl_correspondente_con = "S";
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CORRESPONDENTE;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::CORRESPONDENTE;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    if($user->id){

                        $role = Role::find(Roles::CORRESPONDENTE);
                        $user->assignRole($role);

                        $enderecoEletronico = new EnderecoEletronico();
                        $enderecoEletronico->cd_conta_con = $conta->cd_conta_con;
                        $enderecoEletronico->cd_entidade_ete = $entidade->cd_entidade_ete;
                        $enderecoEletronico->cd_tipo_endereco_eletronico_tee = TipoEnderecoEletronico::NOTIFICACAO;
                        $enderecoEletronico->dc_endereco_eletronico_ede = $email;
                        $enderecoEletronico->save();
                    }

                    Session::put('SESSION_CD_CONTA', $conta->cd_conta_con);
                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }

    public function adicionar(Request $request){

        $correspondente = ContaCorrespondente::where('cd_conta_con', $this->conta)->where('cd_correspondente_cor',$request->id)->first();

        if(is_null($correspondente)){

            $correspondente = new ContaCorrespondente();
            $correspondente->cd_conta_con = $this->conta;
            $correspondente->cd_correspondente_cor = $request->id;        
            
            if($correspondente->save())
                Flash::success('Correspondente adicionado com sucesso');
            else{
                Flash::error('Erro ao adicionar correspondente');
                return redirect()->back();
            }

        }else{
            Flash::warning('Correspondente já existe na sua lista');
            return redirect()->back();
        }

        return redirect('correspondentes');
    }

    public function remover(Request $request){

        $correspondente = ContaCorrespondente::where('cd_conta_correspondente_ccr',$request->id)->first();

        if($correspondente->delete())
            Flash::success('Correspondente removido com sucesso');
        else{
            Flash::error('Erro ao remover correspondente');
        }    
        return redirect('correspondentes');    
    }

    //Métodos para a ROLE CORRESPONDENTE

    public function adicionarAtuacao(Request $request){

        $atuacao = new CidadeAtuacao();
        $atuacao->cd_entidade_ete = $request->entidade;
        $atuacao->cd_cidade_cde = $request->cidade;
        $atuacao->fl_origem_cat = $request->atuacao;

        if($atuacao->save())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500); 

    }

    public function excluirAtuacao($id){

        $atuacao = CidadeAtuacao::where('cd_cidade_atuacao_cat',$id)->first();

        if($atuacao->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);   
        
    }

    public function listarAtuacao($entidade){

        return response()->json(CidadeAtuacao::with('cidade')->where('cd_entidade_ete',$entidade)->get()); 

    }

    public function editar(Request $request){

        $correspondente = Correspondente::where('cd_conta_con',$request->conta)->first();

        $request->merge(['cd_conta_con' => $correspondente->cd_conta_con]);
        $request->merge(['cd_entidade_ete' => $correspondente->entidade->cd_entidade_ete]);

        $correspondente->fill($request->all());

        if($correspondente->saveOrFail()){

            //Inserção de telefones
            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $correspondente->entidade->cd_entidade_ete,
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
                        'cd_entidade_ete'                 => $correspondente->entidade->cd_entidade_ete,
                        'cd_conta_con'                    => $this->conta, 
                        'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                        'dc_endereco_eletronico_ede'      => $emails[$i]->email
                    ]);

                }
            }

            //Identificação para tipo de pessoa
            $identificacao = (Identificacao::where('cd_conta_con',$correspondente->entidade->cd_conta_con)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first()) ? Identificacao::where('cd_conta_con',$correspondente->entidade->cd_conta_con)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first() : $identificacao = Identificacao::where('cd_conta_con',$correspondente->entidade->cd_conta_con)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CNPJ)->first();

            $nu_cpf_cnpj = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;
            
            if($identificacao){

                $identificacao->cd_tipo_identificacao_tpi = ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ;
                $identificacao->nu_identificacao_ide = (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : '';
                $identificacao->saveOrFail();

            }else{

                $identificacao = Identificacao::create([
                    'cd_entidade_ete'           => $correspondente->entidade->cd_entidade_ete,
                    'cd_conta_con'              => $correspondente->cd_conta_con, 
                    'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                    'nu_identificacao_ide'      => (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : ''
                ]);
            }

            //Identificação para OAB
            if(!empty($request->oab)){

                $identificacao = Identificacao::where('cd_conta_con',$correspondente->entidade->cd_conta_con)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::OAB)->first();

                if($identificacao){
                    
                    $request->merge(['nu_identificacao_ide' => $request->oab]);
                    $identificacao->fill($request->all());
                    $identificacao->saveOrFail();
                             
                }else{

                    $identificacao = Identificacao::create([
                    'cd_entidade_ete'           => $correspondente->entidade->cd_entidade_ete,
                    'cd_conta_con'              => $this->conta, 
                    'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                    'nu_identificacao_ide'      => $request->oab
                    ]);  

                }              
            }

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if(!empty($request->dc_logradouro_ede)){

                $endereco = Endereco::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->first();

                if($endereco){
                        
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();

                }else{

                        $endereco = new Endereco();
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();
                }
                    
            }

            //Atualização dos dados bancários
            if(!empty($request->cd_banco_ban) && !empty($request->nu_agencia_dba) && !empty($request->cd_tipo_conta_tcb) && !empty($request->nu_conta_dba)){

                $request->merge(['nm_titular_dba'  => $request->nm_razao_social_con]);
                $request->merge(['nu_cpf_cnpj_dba' => $nu_cpf_cnpj]);

                $registro = RegistroBancario::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$correspondente->entidade->cd_entidade_ete)->first();

                if($registro){
                        
                        $registro->fill($request->all());
                        $registro->saveOrFail();

                }else{

                        $registro = new RegistroBancario();
                        $registro->fill($request->all());
                        $registro->saveOrFail();
                }
            }

        }       

        return redirect('correspondente/perfil/'.$correspondente->entidade->cd_entidade_ete);
    }

    public function clientes(){

        return view('correspondente/clientes');

    }

    public function ficha($id){

        $correspondente = Correspondente::with('entidade')->where('cd_conta_con', $id)->first();
        $flag = (count($correspondente->entidade->atuacao()->get()) == 0 or count($correspondente->entidade->fone()->get()) == 0) ? true : false;

        return view('correspondente/ficha',['correspondente' => $correspondente, 'flag' => $flag]);

    }

    public function processos(){

        return view('correspondente/processos');

    }

    public function perfil($id){

        $correspondente = Correspondente::where('cd_conta_con',Entidade::where('cd_entidade_ete', $id)->first()->cd_conta_con)->first();
        return view('correspondente/perfil',['correspondente' => $correspondente]);

    }

     public function dashboard($id){

        $correspondente = Correspondente::where('cd_conta_con',Entidade::where('cd_entidade_ete', $id)->first()->cd_conta_con)->first();

        if(count($correspondente->entidade->atuacao()->get()) == 0 or count($correspondente->entidade->fone()->get()) == 0){

            return redirect('correspondente/ficha/'.$correspondente->cd_conta_con)->with(['flag' => true]); 
        }
        
        return view('correspondente/dashboard',['correspondente' => $correspondente]);

    }

    public function search(Request $request)
    {
        $search = $request->get('term');
      
        $resultados = ContaCorrespondente::whereHas('correspondente', function ($query) use ($search) {
            $query->where('nm_razao_social_con', 'ilike', '%'. $search. '%')->orWhere('nm_fantasia_con', 'ilike', '%'. $search. '%');
        })->get();

        $results = array();
        foreach ($resultados as $ret)
        {

            if(!empty($ret->correspondente->nm_fantasia_con)){
                $nome =  $ret->correspondente->nm_razao_social_con.' ('.$ret->correspondente->nm_fantasia_con.')';
            }else{
                $nome = $ret->correspondente->nm_razao_social_con;
            }
            
           $results[] = [ 'id' => $ret->correspondente->cd_conta_con, 'value' => $nome ];
        }
 
        return response()->json($results);
            
    } 

}