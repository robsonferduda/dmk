<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use URL;
use DB;
use Auth;
use Mail;
use Hash;
use App\Utils;
use App\Enums\Nivel;
use App\Enums\Roles;
use App\Enums\TipoEnderecoEletronico;
use App\Exports\Correspondente\RelacaoCorrespondentesEscritorioExport;
use App\Fone;
use App\User;
use App\Conta;
use App\Estado;
use App\Processo;
use App\Endereco;
use App\Entidade;
use App\Cidade;
use App\GrupoCidade;
use App\Enums\TipoMensagem;
use App\CidadeAtuacao;
use App\Correspondente;
use App\ConviteCorrespondente;
use App\TipoDespesa;
use App\TipoProcesso;
use App\TipoServico;
use App\ProcessoMensagem;
use App\TaxaHonorario;
use App\ContaCorrespondente;
use App\EnderecoEletronico;
use App\ReembolsoTipoDespesa;
use App\Identificacao;
use App\RegistroBancario;
use App\StatusProcesso;
use Kodeine\Acl\Models\Eloquent\Role;
use Illuminate\Http\Request;
use App\Http\Requests\ConviteRequest;
use App\Http\Requests\CorrespondenteRequest;
use App\Http\Requests\CadastroCorrespondenteRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CorrespondenteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['cadastro','aceitarFiliacao','aceitarConvite','cadastrarSenha','novaSenha']]);
        
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        return view('correspondente/correspondentes');
    }

    public function painel()
    {
        return view('correspondente/painel');
    }

    public function detalhes($id)
    {
        $id = \Crypt::decrypt($id);

        $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $id)->first();
        return view('correspondente/detalhes', ['correspondente' => $correspondente]);
    }

    public function dados($id)
    {
        $correspondente = Correspondente::where('cd_conta_con', $id)->first();
        $endereco = ($correspondente->entidade()) ? Endereco::where('cd_entidade_ete', $correspondente->entidade()->first()->cd_entidade_ete)->first() : null;

        $dados = array('dados' => $correspondente->toArray(), 'endereco' => $endereco);

        echo json_encode($dados);
    }

    public function buscar(Request $request)
    {
        $estado = $request->get('cd_estado_est');
        $cidade = $request->get('cd_cidade_cde');
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');
        $categoria = $request->get('cd_categoria_correspondente_cac');
        $condicao_cidade = null;

        $sql = "SELECT t1.cd_conta_correspondente_ccr, t1.cd_conta_con, t1.cd_correspondente_cor, t1.cd_entidade_ete, t3.nu_identificacao_ide, t1.nm_conta_correspondente_ccr, t4.dc_categoria_correspondente_cac, t5.cd_cidade_cde, t6.nm_cidade_cde, t10.email, t4.color_cac
                FROM conta_correspondente_ccr t1
                LEFT JOIN categoria_correspondente_cac t4 ON t1.cd_categoria_correspondente_cac = t4.cd_categoria_correspondente_cac
                JOIN conta_con t2 ON t1.cd_conta_con = t2.cd_conta_con AND t1.cd_conta_con = $this->conta
                LEFT JOIN identificacao_ide t3 ON t1.cd_entidade_ete = t3.cd_entidade_ete AND t3.cd_tipo_identificacao_tpi IN(1,7) AND t3.deleted_at is null
                LEFT JOIN cidade_atuacao_cat t5 ON t1.cd_entidade_ete = t5.cd_entidade_ete AND t5.fl_origem_cat = 'S' AND t5.deleted_at is null
                LEFT JOIN cidade_cde t6 ON t5.cd_cidade_cde = t6.cd_cidade_cde
                JOIN users t10 ON t1.cd_correspondente_cor = t10.cd_conta_con
                WHERE t1.deleted_at is null ";

        if (!empty($nome)) {
            $sql .= " AND nm_conta_correspondente_ccr ilike '%$nome%' ";
        }

        if (!empty($categoria)) {
            $sql .= " AND t4.cd_categoria_correspondente_cac = $categoria ";
        }

        if (!empty($identificacao)) {
            $sql .= " AND t3.nu_identificacao_ide = '$identificacao' ";
        }

        if (!empty($cidade)) {
            $condicao_cidade .= " AND t7.cd_cidade_cde = $cidade  ";
        }

        if (!empty($estado)) {
            $sql .= "AND t1.cd_entidade_ete IN (SELECT t8.cd_entidade_ete 
                                       FROM cidade_atuacao_cat t7, conta_correspondente_ccr t8, cidade_cde t9 
                                       WHERE t7.cd_entidade_ete = t8.cd_entidade_ete 
                                       AND t7.cd_cidade_cde = t9.cd_cidade_cde
                                       $condicao_cidade
                                       AND t9.cd_estado_est = $estado
                                       AND t8.cd_conta_con = $this->conta 
                                       AND t7.deleted_at is null)";
        }

        $sql .= " ORDER BY nm_conta_correspondente_ccr";

        $correspondentes = DB::select($sql);

        session()->flashInput($request->input());
        
        if (is_null($correspondentes)) {
            Flash::warning('Não existem correspondentes que correspondam aos valores pesquisados');
        }

        switch (Utils::get_post_action('pesquisar', 'exportar')) {
            case 'pesquisar':
                return view('correspondente/correspondentes', ['correspondentes' => $correspondentes]);
                break;

            case 'exportar':
                $dados = array('correspondentes' => $correspondentes);
                return \Excel::download(new RelacaoCorrespondentesEscritorioExport($dados), 'correspondentes.xls', \Maatwebsite\Excel\Excel::XLSX);
                break;
        }
    }

    public function novo()
    {
        return view('correspondente/novo');
    }

    public function buscarTodos(Request $request)
    {
        $correspondentes = Correspondente::with('entidade')->with('entidade.usuario')->where('fl_correspondente_con', 'S')->orderBy('cd_conta_con')->get();

        //$correspondentes = null;
        return view('correspondente/todos', ['correspondentes' => $correspondentes]);
    }

    //Cadastro de correspondentes pela interface do sistema, aberto, sem receber convite
    public function cadastro(CorrespondenteRequest $request)
    {
        exit;

        DB::transaction(function () use ($request) {
            $input = $request->all();
            $email = trim($input['email']);
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];
            $flag_convite = false;

            //Se existe o token, indica que o cadastro é via convite
            if ($request->token and $request->conta) {
                $token = $request->token;
                $conta_convite = $request->conta;
                $flag_convite = true;
            }

            $conta = new Correspondente();
            $conta->fill($request->all());
            $conta->fl_correspondente_con = "S";
            $conta->saveOrFail();

            if ($conta->cd_conta_con) {
                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CORRESPONDENTE;
                $entidade->saveOrFail();

                if ($entidade->cd_entidade_ete) {
                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::CORRESPONDENTE;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    if ($user->id) {
                        $role = Role::find(Roles::CORRESPONDENTE);
                        $user->assignRole($role);

                        $enderecoEletronico = new EnderecoEletronico();
                        $enderecoEletronico->cd_conta_con = $conta->cd_conta_con;
                        $enderecoEletronico->cd_entidade_ete = $entidade->cd_entidade_ete;
                        $enderecoEletronico->cd_tipo_endereco_eletronico_tee = TipoEnderecoEletronico::NOTIFICACAO;
                        $enderecoEletronico->dc_endereco_eletronico_ede = $email;
                        $enderecoEletronico->save();
                    }

                    //Se o login se originou de um convite, registra o correpondente
                    if ($flag_convite) {
                        //Entidade usada para criar identificador único para cada conta alterar seus dados de correspondente
                        $entidade_correspondente = new Entidade;
                        $entidade_correspondente->cd_conta_con = $conta_convite;
                        $entidade_correspondente->cd_tipo_entidade_tpe = \TipoEntidade::CONTA_CORRESPONDENTE;
                        $entidade_correspondente->saveOrFail();

                        $vinculo = ContaCorrespondente::create(['cd_conta_con' => $conta_convite,
                                                                'cd_correspondente_cor' => $conta->cd_conta_con,
                                                                'cd_entidade_ete' => $entidade_correspondente->cd_entidade_ete,
                                                                'nm_conta_correspondente_ccr' => $nome
                        ]);

                        //Se inseriu a conta correspondente, atualiza o convite
                        if ($vinculo) {
                            $convite = ConviteCorrespondente::where('token_coc', $token)->first();
                            $convite->fl_aceite_coc = 'S';
                            $convite->dt_aceite_coc = date("Y-m-d H:i:s");
                        
                            $convite->save();
                        }
                    }
                    $conta->email = $email;
                    $conta->notificacaoConfirmacao($conta);

                    Session::put('SESSION_CD_CONTA', $conta->cd_conta_con);
                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }

    public function despesas($id)
    {
        $selecionadas = array();
        $disponiveis = array();
        $correspondente = Correspondente::where('cd_conta_con', $id)->first();

        $despesas = ReembolsoTipoDespesa::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $correspondente->entidade->cd_entidade_ete)->get();
        $todas = TipoDespesa::where('cd_conta_con', $this->conta)->where('fl_reembolso_tds', 'S')->get();

        foreach ($despesas as $d) {
            $selecionadas[] = $d->TipoDespesa()->first();
        }

        foreach ($todas as $t) {
            $disponiveis[] = $t;
        }

        $despesas_disponiveis = array_udiff(
            $disponiveis,
            $selecionadas,
            function ($obj_a, $obj_b) {
                return $obj_a->cd_tipo_despesa_tds - $obj_b->cd_tipo_despesa_tds;
            }
        );

        return view('correspondente/despesas', ['correspondente' => $correspondente, 'despesas' => $despesas, 'despesas_disponiveis' => $despesas_disponiveis ]);
    }

    public function adicionarDespesas(Request $request)
    {
        $selecionadas = array();
        $removidas = $request->remover;
        $despesas = ReembolsoTipoDespesa::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->get();

        //Verifica se alguma das opções marcadas foi removida. Se sim, remove do banco.
        foreach ($despesas as $d) {
            $selecionadas[] = $d->TipoDespesa()->first()->cd_tipo_despesa_tds;
        }

        if ($removidas == null) { //Remover tudo
            for ($i=0; $i < count($selecionadas); $i++) {
                $despesa = ReembolsoTipoDespesa::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->where('cd_tipo_despesa_tds', $selecionadas[$i])->first();
                $despesa->delete();
            }
        } else {
            $diferenca = array_diff($selecionadas, $removidas);

            if (count($diferenca) > 0) {
                $valores = array_values($diferenca);
                for ($i=0; $i < count($valores); $i++) {
                    $despesa = ReembolsoTipoDespesa::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->where('cd_tipo_despesa_tds', $valores[$i])->first();
                    $despesa->delete();
                }
            }
        }

        //Adiciona as novas despesas que foram marcadas
        if (!empty($request->despesas)) {
            $despesas = $request->despesas;
            for ($i = 0; $i < count($despesas); $i++) {
                $despesa = ReembolsoTipoDespesa::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->where('cd_tipo_despesa_tds', $despesas[$i])->first();

                if (!$despesa) {
                    $reembolso = ReembolsoTipoDespesa::create([
                        'cd_entidade_ete'           => $request->entidade,
                        'cd_conta_con'              => $this->conta,
                        'cd_tipo_despesa_tds'       => $despesas[$i]
                    ]);
                }
            }
        }
        return redirect('correspondente/detalhes/'.\Crypt::encrypt($request->conta));
    }

    public function honorarios($id)
    {
        $id = \Crypt::decrypt($id);

        $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $id)->first();
        $servicos = TipoServico::where('cd_conta_con', $this->conta)->orderBy('nm_tipo_servico_tse')->get();

        return view('correspondente/honorarios', ['cliente' => $correspondente, 'servicos' => $servicos]);
    }

    public function convidar(ConviteRequest $request)
    {
        $to_name = 'Convidado';
        $to_email = $request->email;
        $conta = Conta::where('cd_conta_con', $this->conta)->first();
        
        $data = array('nome'=>Auth::user()->name);

        $convite = ConviteCorrespondente::create(['cd_conta_con' => $this->conta,
                                                  'token_coc'    => $token = str_random(40),
                                                  'email_coc'    => $to_email
        ]);

        $conta->email = $to_email;
        $unique = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('email', $request->email)->first();

        if (!empty($unique)) {
            $conta->enviarFiliacao($convite);
            Flash::success('O email informado já está cadastrado como correspondente. Foi encaminhado um convite para atuar como colaborador em parceria com seu escritório');
        } else {
            $conta->enviarConvite($convite);
            Flash::success('Convite enviado com sucesso. O destinatário poderá realizar seu cadastro para aparecer nas buscas por correspondentes.');
        }

        return redirect('correspondentes');
    }

    public function aceitarConvite($token)
    {
        //No caso de convite, o correspondente estará deslogado, pois ele ainda não possui cadastro
        $convite = ConviteCorrespondente::where('token_coc', $token)->first();
        \Session::put('token', $convite->token_coc);
        \Session::put('flag_convite', true);
        \Session::put('conta', $convite->cd_conta_con);
        return Redirect::route('correspondente');
    }

    public function aceitarFiliacao($token)
    {
        $convite = ConviteCorrespondente::where('token_coc', $token)->first();

        //Verificar para quem pertence o convite
        if (Auth::user() and (Auth::user()->email != $convite->email_coc)) {
            \Session::put('retorno', array('tipo' => 'erro','msg' => 'Esse convite não pertence ao seu usuário e foi desconsiderado pelo sistema.'));
            return Redirect::route('msg-filiacao');
        }

        //Verificar se o convite já foi aceito
        if ($convite->fl_aceite_coc == 'S') {
            \Session::put('retorno', array('tipo' => 'erro','msg' => 'O convite foi aceito e não está mais disponível'));
            return Redirect::route('msg-filiacao');
        }

        //Busca os dados do correspondente
        $user = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('email', $convite->email_coc)->first();

        //Verifica se o correspondente já possui cadastro e busca os dados
        $correspondente = ContaCorrespondente::where('cd_conta_con', $convite->cd_conta_con)->where('cd_correspondente_cor', $user->cd_conta_con)->first();

        if (is_null($correspondente)) {
            $correspondente = new ContaCorrespondente();
            $correspondente->cd_conta_con = $convite->cd_conta_con;
            $correspondente->cd_correspondente_cor = $user->cd_conta_con;
                
            if ($correspondente->save()) {
                $convite->fl_aceite_coc = 'S';
                $convite->dt_aceite_coc = date("Y-m-d H:i:s");
                $convite->save();

                $conta = Conta::where('cd_conta_con', $convite->cd_conta_con)->first();
                \Session::put('retorno', array('tipo' => 'sucesso','msg' => 'Você foi adicionado como correspondente de '.$conta->nm_razao_social_con.' com sucesso'));
                return Redirect::route('msg-filiacao');
            } else {
                \Session::put('retorno', array('tipo' => 'erro','msg' => 'Erro ao aceitar convite'));
                return Redirect::route('msg-filiacao');
            }
        } else {
            \Session::put('retorno', array('tipo' => 'erro','msg' => 'Você já faz parte dessa rede de correspondentes'));
            return Redirect::route('msg-filiacao');
        }
    }

    //Cadastro de correspondente realizado pela conta
    public function novoCorrespondenteConta(CadastroCorrespondenteRequest $request)
    {
        $id = DB::transaction(function () use ($request) {
            $input = $request->all();
            $email = trim($input['email']);
            $nome  = $input['nm_razao_social_con'];
            $senha_aleatoria = Utils::gerar_senha(8, true, true, true, false);

            //Primeiro passo é verificar se existe um usuário do tipo correspondente com o email informado
            $unique = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('email', 'ilike', $request->email)->first();
            $conta_logada = Conta::where('cd_conta_con', $this->conta)->first();

            $correspondente_cadastro = new Correspondente();
            $correspondente_cadastro->email = $email;
            $correspondente_cadastro->senha = $senha_aleatoria;

            //Se ainda não existe correspondente, cria e vincula ele à conta
            if (empty($unique)) {
                $conta = new Correspondente();
                $conta->fill($request->all());
                $conta->fl_correspondente_con = "S";
                $conta->saveOrFail();

                if ($conta->cd_conta_con) {
                    //Entidade criado do tipo correspondente. Usada para vincular um usuário ao correspondente
                    $entidade = new Entidade;
                    $entidade->cd_conta_con = $conta->cd_conta_con;
                    $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CORRESPONDENTE;
                    $entidade->saveOrFail();

                    //Entidade usada para criar identificador único para cada conta alterar seus dados de correspondente
                    $entidade_correspondente = new Entidade;
                    $entidade_correspondente->cd_conta_con = $this->conta;
                    $entidade_correspondente->cd_tipo_entidade_tpe = \TipoEntidade::CONTA_CORRESPONDENTE;
                    $entidade_correspondente->saveOrFail();

                    if ($entidade->cd_entidade_ete and $entidade_correspondente->cd_entidade_ete) {
                        $user = new User();
                        $user->cd_conta_con = $conta->cd_conta_con;
                        $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                        $user->cd_nivel_niv = Nivel::CORRESPONDENTE;
                        $user->name = $nome;
                        $user->email = $email;

                        $user->password = Hash::make($senha_aleatoria);
                        $user->save();

                        if ($user->id) {
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
                        $correspondente->cd_entidade_ete = $entidade_correspondente->cd_entidade_ete;
                        $correspondente->nm_conta_correspondente_ccr = $nome;
                        
                        if ($correspondente->save()) {
                            $conta->email = $email;
                            
                            if ($conta->notificarCadastroConta($conta_logada)) {
                                Flash::success('Correspondente adicionado com sucesso. O correspondente foi notificado no email '.$correspondente_cadastro->email);
                            } else {
                                Flash::warning('Correspondente adicionado com sucesso, porém não foi enviada notificação de cadastro. Habilite essa opção para enviar notificações.');
                            }

                            return $conta->cd_conta_con;
                        } else {
                            Flash::error('Erro ao adicionar correspondente');
                            return false;
                        }
                    }
                }
            } else {
                //Verifica se o correspondente já está presente na conta, mesmo que tenha sido excluído
                $conta_correspondente = ContaCorrespondente::where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $unique->cd_conta_con)->withTrashed()->get();

                if ($conta_correspondente->isEmpty()) {
                    //Se já possui cadastro, cria somente a entidade para associar os dados e insere o vínculo com a conta
                    $entidade_correspondente = new Entidade;
                    $entidade_correspondente->cd_conta_con = $this->conta;
                    $entidade_correspondente->cd_tipo_entidade_tpe = \TipoEntidade::CONTA_CORRESPONDENTE;
                    
                    if ($entidade_correspondente->saveOrFail()) {
                        $enderecoEletronico = new EnderecoEletronico();
                        $enderecoEletronico->cd_conta_con = $this->conta;
                        $enderecoEletronico->cd_entidade_ete = $entidade_correspondente->cd_entidade_ete;
                        $enderecoEletronico->cd_tipo_endereco_eletronico_tee = TipoEnderecoEletronico::NOTIFICACAO;
                        $enderecoEletronico->dc_endereco_eletronico_ede = $email;
                        $enderecoEletronico->save();

                        //Após cadastrar, vincula a conta que realizou o cadastro
                        $correspondente = new ContaCorrespondente();
                        $correspondente->cd_conta_con = $this->conta;
                        $correspondente->cd_correspondente_cor = $unique->cd_conta_con;
                        $correspondente->cd_entidade_ete = $entidade_correspondente->cd_entidade_ete;
                        $correspondente->nm_conta_correspondente_ccr = $nome; //Insere mesmo nome do usuário já utilizado
                            
                        if ($correspondente->save()) {
                            $correspondente_cadastro->email = $email;

                            if ($correspondente_cadastro->notificarFiliacaoConta($conta_logada)) {
                                Flash::success('Correspondente adicionado com sucesso. O correspondente foi notificado no email '.$correspondente_cadastro->email);
                            } else {
                                Flash::warning('Correspondente adicionado com sucesso, porém não foi enviada notificação de cadastro. Habilite essa opção para enviar notificações.');
                            }
                            
                            return $unique->cd_conta_con;
                        } else {
                            Flash::error('Erro ao adicionar correspondente');
                            return false;
                        }
                    } else {
                        Flash::error('Erro ao adicionar correspondente');
                        return false;
                    }
                } else {
                    //Quando existir correspondente que foi excluído, restaura o código antigo, fazendo deleted_at = null
                    $correspondente = $conta_correspondente->get(0);

                    if (is_null($correspondente->deleted_at)) {
                        Flash::warning('Correspondente já faz parte da sua lista. <a href="'.URL::to('correspondente/detalhes/'.\Crypt::encrypt($correspondente->cd_correspondente_cor)).'">Clique aqui</a> para acessar seu cadastro.');
                        return false;
                    } else {
                        $correspondente->nm_conta_correspondente_ccr = $nome;
                        $correspondente->deleted_at = null;

                        if ($correspondente->save()) {
                            $correspondente_cadastro->email = $email;

                            if ($correspondente_cadastro->notificarFiliacaoConta($conta_logada)) {
                                Flash::success('Correspondente adicionado com sucesso. O correspondente foi notificado no email '.$correspondente_cadastro->email);
                            } else {
                                Flash::warning('Correspondente adicionado com sucesso, porém não foi enviada notificação de cadastro. Habilite essa opção para enviar notificações.');
                            }
                        }

                        return $correspondente->cd_correspondente_cor;
                    }
                }
            }
        });

        if ($id) {
            return redirect('correspondente/detalhes/'.\Crypt::encrypt($id));
        } else {
            return redirect('correspondentes');
        }
    }

    public function adicionar(Request $request)
    {
        $correspondente = ContaCorrespondente::where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $request->id)->first();

        if (is_null($correspondente)) {
            $correspondente = new ContaCorrespondente();
            $correspondente->cd_conta_con = $this->conta;
            $correspondente->cd_correspondente_cor = $request->id;
            
            if ($correspondente->save()) {
                Flash::success('Correspondente adicionado com sucesso');
            } else {
                Flash::error('Erro ao adicionar correspondente');
                return redirect()->back();
            }
        } else {
            Flash::warning('Correspondente já existe na sua lista');
            return redirect()->back();
        }

        return redirect('correspondentes');
    }

    public function remover(Request $request)
    {
        $correspondente = ContaCorrespondente::where('cd_conta_correspondente_ccr', $request->id)->first();
        
        if (!empty($correspondente) && $correspondente->delete()) {
            Flash::success('Correspondente removido com sucesso');
        } else {
            Flash::error('Erro ao remover correspondente');
        }
        return redirect('correspondentes');
    }

    //Métodos para a ROLE CORRESPONDENTE

    public function adicionarAtuacao(Request $request)
    {
        if ($request->atuacao == 'S') {
            $origem = CidadeAtuacao::where('cd_entidade_ete', $request->entidade)->where('fl_origem_cat', $request->atuacao)->first();

            if ($origem) {
                return Response::json(['msg' => 'Comarca de origem já foi informada'], 500);
            } else {
                $cidade = CidadeAtuacao::where('cd_entidade_ete', $request->entidade)->where('cd_cidade_cde', $request->cidade)->first();

                if ($cidade) {
                    $cidade->fl_origem_cat = 'S';
                    $cidade->save();
                } else {
                    $atuacao = new CidadeAtuacao();
                    $atuacao->cd_entidade_ete = $request->entidade;
                    $atuacao->cd_cidade_cde = $request->cidade;
                    $atuacao->fl_origem_cat = $request->atuacao;
                    $atuacao->save();
                }
            }
        } else {
            if ($request->cidade == 0) {
                $cidades = Cidade::where('cd_estado_est', $request->estado)->get();

                foreach ($cidades as $cidade) {
                    $atuacao = CidadeAtuacao::where('cd_entidade_ete', $request->entidade)->where('cd_cidade_cde', $cidade->cd_cidade_cde)->first();

                    if (!$atuacao) {
                        $atuacao = new CidadeAtuacao();
                        $atuacao->cd_entidade_ete = $request->entidade;
                        $atuacao->cd_cidade_cde = $cidade->cd_cidade_cde;
                        $atuacao->fl_origem_cat = 'N';

                        $atuacao->save();
                    }
                }
                return Response::json(array('message' => 'Cidades adicionadas com sucesso'), 200);
            } else {
                $atuacao = CidadeAtuacao::where('cd_entidade_ete', $request->entidade)->where('cd_cidade_cde', $request->cidade)->first();

                if ($atuacao) {
                    return Response::json(['msg' => 'Comarca de atuação já informada'], 500);
                } else {
                    $atuacao = new CidadeAtuacao();
                    $atuacao->cd_entidade_ete = $request->entidade;
                    $atuacao->cd_cidade_cde = $request->cidade;
                    $atuacao->fl_origem_cat = $request->atuacao;

                    if ($atuacao->save()) {
                        return Response::json(array('message' => 'Registro adicionado com sucesso'), 200);
                    } else {
                        return Response::json(array('message' => 'Erro ao adicionar registro'), 500);
                    }
                }
            }
        }
    }

    public function excluirAtuacao($id)
    {
        $atuacao = CidadeAtuacao::where('cd_cidade_atuacao_cat', $id)->first();

        $honorarios = TaxaHonorario::where('cd_entidade_ete',$atuacao->cd_entidade_ete)
                                    ->where('cd_cidade_cde',$atuacao->cd_cidade_cde)
                                    ->get(); 

        if(count($honorarios)){

            return Response::json(array('message' => 'Existem valores de honorários cadastrados para a comarca selecionada. O registro não pode ser excluído'), 500);

        }else{

            if ($atuacao->delete()) {
                return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
            } else {
                return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
            }

        }
    }

    public function listarAtuacao($entidade)
    {
        return response()->json(CidadeAtuacao::with('cidade')->where('cd_entidade_ete', $entidade)->get());
    }

    public function listarOrigem($entidade)
    {
        return response()->json(CidadeAtuacao::with('cidade')->where('cd_entidade_ete', $entidade)->where('fl_origem_cat', 'S')->get());
    }

    //O mesmo método de edição é utilizado pelo escritório e pelo correspondente, por isso existe a verificação de nível
    public function editar(Request $request)
    {
        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);

        $correspondente = Correspondente::where('cd_conta_con', $request->conta)->first();
        $vinculo = null;
        
        if (Auth::user()->cd_nivel_niv == 3) {
            $correspondente->nm_razao_social_con = $request->nm_conta_correspondente_ccr;
            $correspondente->cd_tipo_pessoa_tpp = $request->cd_tipo_pessoa_tpp;
            $correspondente->save();
        } else {
            $conta_correspondente = ContaCorrespondente::where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $request->conta)->first();

            $conta_correspondente->nm_conta_correspondente_ccr = $request->nm_conta_correspondente_ccr;
            $conta_correspondente->cd_tipo_pessoa_tpp = $request->cd_tipo_pessoa_tpp;
            $conta_correspondente->cd_categoria_correspondente_cac = $request->cd_categoria_correspondente_cac;
            $conta_correspondente->obs_ccr = $request->obs_ccr;

            $vinculo = $conta_correspondente->saveOrFail();
        }

        if ($vinculo or $correspondente) {
            //Inserção de telefones
            if (!empty($request->telefones) && count(json_decode($request->telefones)) > 0) {
                $fones = json_decode($request->telefones);
                for ($i = 0; $i < count($fones); $i++) {
                    $fone = Fone::create([
                        'cd_entidade_ete'           => $request->entidade,
                        'cd_conta_con'              => $correspondente->cd_conta_con,
                        'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                        'nu_fone_fon'               => $fones[$i]->numero
                    ]);
                }
            }

            //Inserção de emails
            if (!empty($request->emails) && count(json_decode($request->emails)) > 0) {
                $emails = json_decode($request->emails);
                for ($i = 0; $i < count($emails); $i++) {
                    $email = EnderecoEletronico::create([
                        'cd_entidade_ete'                 => $request->entidade,
                        'cd_conta_con'                    => $correspondente->cd_conta_con,
                        'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                        'dc_endereco_eletronico_ede'      => trim($emails[$i]->email)
                    ]);
                }
            }

            //Atualização dos dados bancários
            if (!empty($request->registrosBancarios) && count(json_decode($request->registrosBancarios)) > 0) {
                $registrosBancarios = json_decode($request->registrosBancarios);
                for ($i = 0; $i < count($registrosBancarios); $i++) {
                    $registro = RegistroBancario::create([
                            'cd_entidade_ete' => $request->entidade,
                            'cd_conta_con'    => $correspondente->cd_conta_con,
                            'nm_titular_dba'  => $registrosBancarios[$i]->titular,
                            'nu_cpf_cnpj_dba' => str_replace(array('.','-'), '', $registrosBancarios[$i]->cpf),
                            'nu_agencia_dba'  => $registrosBancarios[$i]->agencia,
                            'nu_conta_dba'    => $registrosBancarios[$i]->conta,
                            'cd_banco_ban'    => $registrosBancarios[$i]->banco,
                            'cd_tipo_conta_tcb' => $registrosBancarios[$i]->tipo
                        ]);
                }
            }

            //Identificação para tipo de pessoa
            $identificacao = (Identificacao::where('cd_entidade_ete', $request->entidade)->where('cd_tipo_identificacao_tpi', \TipoIdentificacao::CPF)->first()) ? Identificacao::where('cd_entidade_ete', $request->entidade)->where('cd_tipo_identificacao_tpi', \TipoIdentificacao::CPF)->first() : $identificacao = Identificacao::where('cd_entidade_ete', $request->entidade)->where('cd_tipo_identificacao_tpi', \TipoIdentificacao::CNPJ)->first();

            $nu_cpf_cnpj = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;
            
            if ($identificacao) {
                $identificacao->cd_tipo_identificacao_tpi = ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ;
                $identificacao->nu_identificacao_ide = (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : '';
                $identificacao->saveOrFail();
            } else {
                $identificacao = Identificacao::create([
                    'cd_entidade_ete'           => $request->entidade,
                    'cd_conta_con'              => $correspondente->cd_conta_con,
                    'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                    'nu_identificacao_ide'      => (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : ''
                ]);
            }

            //Identificação para OAB
            if (!empty($request->oab)) {
                $identificacao = Identificacao::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->where('cd_tipo_identificacao_tpi', \TipoIdentificacao::OAB)->first();

                if ($identificacao) {
                    $request->merge(['nu_identificacao_ide' => $request->oab]);
                    $identificacao->fill($request->all());
                    $identificacao->saveOrFail();
                } else {
                    $identificacao = Identificacao::create([
                    'cd_entidade_ete'           => $request->entidade,
                    'cd_conta_con'              => $correspondente->cd_conta_con,
                    'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                    'nu_identificacao_ide'      => $request->oab
                    ]);
                }
            }

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if (!empty($request->dc_logradouro_ede)) {
                $endereco = Endereco::where('cd_conta_con', $this->conta)->where('cd_entidade_ete', $request->entidade)->first();

                if ($endereco) {
                    $endereco->fill($request->all());
                    $endereco->saveOrFail();
                } else {
                    $endereco = new Endereco();
                    $endereco->cd_conta_con = $correspondente->cd_conta_con;
                    $endereco->cd_entidade_ete = $request->entidade;
                    $endereco->fill($request->all());
                    $endereco->saveOrFail();
                }
            }
        }

        if (Auth::user()->cd_nivel_niv == 3) {
            return redirect('correspondente/perfil/'.\Crypt::encrypt($request->entidade));
        } else {
            return redirect('correspondente/detalhes/'.\Crypt::encrypt($conta_correspondente->correspondente->cd_conta_con));
        }
    }

    public function clientes()
    {
        $clientes = ContaCorrespondente::where('cd_correspondente_cor', $this->conta)->with('conta')->get();

        return view('correspondente/clientes', ['clientes' => $clientes]);
    }

    public function comarcas($id)
    {
        $id = \Crypt::decrypt($id);

        if (Auth::user()->cd_nivel_niv == 3) {
            $correspondente = Conta::with('entidade')->where('cd_conta_con', $id)->first();
            return view('correspondente/ficha-correspondente', ['correspondente' => $correspondente]);
        } else {
            $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $id)->first();
            return view('correspondente/comarcas', ['correspondente' => $correspondente]);
        }
    }

    public function ficha($id)
    {
        $id = \Crypt::decrypt($id);

        if (Auth::user()->cd_nivel_niv == 3) {
            $correspondente = Conta::with('entidade')->where('cd_conta_con', $id)->first();
            return view('correspondente/ficha-correspondente', ['correspondente' => $correspondente]);
        } else {
            $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $this->conta)->where('cd_correspondente_cor', $id)->first();
            return view('correspondente/ficha', ['correspondente' => $correspondente]);
        }
    }

    public function processos()
    {
        $clientes = ContaCorrespondente::where('cd_correspondente_cor', $this->conta)->with('conta')->get();

        $processos = Processo::where('cd_correspondente_cor', $this->conta)
                               ->whereHas('status', function ($query) {
                                   $query->where('fl_visivel_correspondente_stp', 'S');
                               })
                                ->where('cd_status_processo_stp', '!=', \StatusProcesso::FINALIZADO)
                                ->where('cd_status_processo_stp', '!=', \StatusProcesso::CANCELADO)
                                  ->orderBy('dt_prazo_fatal_pro', 'asc')
                                  ->orderBy('hr_audiencia_pro')->get();

        $status = StatusProcesso::whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO, \StatusProcesso::CANCELADO])
                  ->orderBy('nm_status_processo_conta_stp')
                  ->get();

        return view('correspondente/processos', ['processos' => $processos, 'clientes' => $clientes, 'status' => $status]);
    }

    public function buscarProcesso(Request $request)
    {
        $numero   = trim($request->get('nu_processo_pro'));
        $tipo = $request->get('cd_tipo_processo_tpo');
        $tipoServico = $request->get('cd_tipo_servico_tse');
        $autor = $request->get('nm_autor_pro');
        $reu = $request->get('nm_reu_pro');
        $acompanhamento = $request->get('nu_acompanhamento_pro');
        $finalizado = $request->get('finalizado');

        $tiposServico = TipoServico::where('cd_conta_con', $this->conta)->get();

        $processos = Processo::where('cd_correspondente_cor', $this->conta)
                               ->whereHas('status', function ($query) {
                                   $query->where('fl_visivel_correspondente_stp', 'S');
                               });

        if (!empty($tipoServico)) {
            $processos->whereHas('honorario', function ($query) use ($tipoServico) {
                $query->where('cd_tipo_servico_tse', $tipoServico);
            });
        }

        if (!empty($finalizado)) {
            $processos->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
        } else {
            $processos->where('cd_status_processo_stp', '!=', \StatusProcesso::FINALIZADO);
        }
        if (!empty($numero)) {
            $processos->where('nu_processo_pro', 'like', "%$numero%");
        }
        if (!empty($tipo)) {
            $processos->where('cd_tipo_processo_tpo', $tipo);
        }
        if (!empty($autor)) {
            $processos->where('nm_autor_pro', 'ilike', '%'. $autor. '%');
        }
        if (!empty($reu)) {
            $processos->where('nm_reu_pro', 'ilike', '%'. $reu. '%');
        }
        if (!empty($acompanhamento)) {
            $processos->where('nu_acompanhamento_pro', 'ilike', '%'. $acompanhamento. '%');
        }

        $processos = $processos->orderBy('dt_prazo_fatal_pro')->orderBy('hr_audiencia_pro')->get();

        if ($request->tipo == 'acompanhamento') {
            return view('correspondente/processos', ['processos' => $processos,'numero' => $numero,'tiposProcesso' => array(),'tipoServico' => $tipoServico, 'tiposServico' => $tiposServico, 'autor' => $autor, 'reu' => $reu, 'acompanhamento' => $acompanhamento, 'finalizado' => $finalizado]);
        } else {
            return view('processo/processos', ['processos' => $processos,'tiposProcesso' => array(),'tiposServico' => $tiposServico]);
        }
    }

    /* Método usado na área do correspondente para listar o acompanhamento de processo */
    public function acompanhamento($id)
    {
        $id = \Crypt::decrypt($id);

        //Verifica se a conta logada tem nível diferente de correspondente. Se tiver, busca o usuário do correspondente, senão, desloga.
        if (Auth::user() and Auth::user()->cd_nivel_niv != Nivel::CORRESPONDENTE) {
            $user_correspondente = User::where('email', Auth::user()->email)->where('cd_nivel_niv', Nivel::CORRESPONDENTE)->first();

            if ($user_correspondente) {
                //Loga o usuário a atualiza as variáveis de sessão
                Auth::login($user_correspondente);

                Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem
                Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem
                Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);
                $this->conta = Auth::user()->cd_conta_con;

                Flash::success('Seu acesso foi alterado para o perfil de correspondente');
            } else {
                Auth::logout();
                return redirect('/login');
            }
        }

        $processo = Processo::with('anexos')->with('anexos.entidade.usuario')->where('cd_processo_pro', $id)->where('cd_correspondente_cor', $this->conta)->first();
        
        $mensagens = ProcessoMensagem::where('cd_processo_pro', $id)
                                     ->where('cd_tipo_mensagem_tim', TipoMensagem::EXTERNA)
                                     ->with('entidadeRemetente')
                                     ->with('entidadeDestinatario')
                                     ->withTrashed()
                                     ->orderBy('created_at', 'ASC')->get();

    
        return view('processo/acompanhar', ['processo' => $processo, 'mensagens_internas' => array(), 'mensagens_externas' => $mensagens]);
    }

    public function perfil($id)
    {
        $id = \Crypt::decrypt($id);

        $correspondente = Correspondente::where('cd_conta_con', Entidade::where('cd_entidade_ete', $id)->first()->cd_conta_con)->first();
        return view('correspondente/perfil', ['correspondente' => $correspondente]);
    }

    public function dadosCliente($cliente)
    {
        $cliente = \Crypt::decrypt($cliente);

        $correspondente = ContaCorrespondente::with('entidade')->with('correspondente')->where('cd_conta_con', $cliente)->where('cd_correspondente_cor', $this->conta)->first();
        return view('correspondente/dados', ['correspondente' => $correspondente]);
    }

    public function processosCliente($cliente)
    {
        $cliente = \Crypt::decrypt($cliente);

        if (!empty(\Cache::tags($this->conta, 'listaTiposProcesso')->get('tiposProcesso'))) {
            $tiposProcesso = \Cache::tags($this->conta, 'listaTiposProcesso')->get('tiposProcesso');
        } else {
            $tiposProcesso = TipoProcesso::All();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags($this->conta, 'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);
        }

        $tiposServico = TipoServico::where('cd_conta_con', $this->conta)->get();

        $processos = Processo::where('cd_correspondente_cor', $this->conta)->get();

        return view('correspondente/processos', ['processos' => $processos, 'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico]);
    }

    public function dashboard($id)
    {
        $id = \Crypt::decrypt($id);

        $correspondente = Correspondente::where('cd_conta_con', Entidade::where('cd_entidade_ete', $id)->first()->cd_conta_con)->first();
        $convites = ConviteCorrespondente::where('cd_convite_correspondente_coc', $correspondente->cd_conta_con)->get();
        $total_processos = Processo::count('cd_correspondente_cor', $correspondente->cd_conta_con);
        
        return view('correspondente/dashboard', ['correspondente' => $correspondente, 'convites' => $convites, 'total_processos' => $total_processos]);
    }

    public function searchConta(Request $request)
    {
        $search = $request->get('term');
      
        $resultados = ContaCorrespondente::whereHas('conta', function ($query) use ($search) {
            $query->where('nm_razao_social_con', 'ilike', '%'. $search. '%');
        })->where('cd_correspondente_cor', $this->conta)->get();

        $results = array();
        foreach ($resultados as $ret) {
            $results[] = [ 'id' => $ret->cd_conta_con, 'value' => $ret->conta->nm_razao_social_con ];
        }
 
        return response()->json($results);
    }

    public function searchDeletedToo(Request $request)
    {
        $search = $request->get('term');

        $cidade  = $request->get('cidade');
        $estado  = $request->get('estado');
  
        $resultados = ContaCorrespondente::where('nm_conta_correspondente_ccr', 'ilike', '%'. $search. '%')
                                            ->where('cd_conta_con', $this->conta)
                                            ->when(!empty($cidade) && !empty($estado), function ($query) use ($cidade) {
                                                return $query->whereHas('cidadeAtuacao', function ($query) use ($cidade) {
                                                    $query->where('cd_cidade_cde', $cidade);
                                                });
                                            })
                                            ->withTrashed()->get();

        $results = array();
        foreach ($resultados as $ret) {
            $results[] = [ 'id' => $ret->correspondente->cd_conta_con, 'value' => $ret->nm_conta_correspondente_ccr, 'flag' => $ret->fl_correspondente_escritorio_ccr ];
        }
 
        return response()->json($results);
    }

    public function search(Request $request)
    {
        $search = $request->get('term');

        $cidade  = $request->get('cidade');
        $estado  = $request->get('estado');
  
        $resultados = ContaCorrespondente::when(!empty($search), function ($query) use ($search) {
                                                $query->where('nm_conta_correspondente_ccr', 'ilike', '%'. $search. '%');
                                            })
                                            ->where('cd_conta_con', $this->conta)
                                            ->when(!empty($cidade) && !empty($estado), function ($query) use ($cidade) {
                                                return $query->whereHas('cidadeAtuacao', function ($query) use ($cidade) {
                                                    $query->where('cd_cidade_cde', $cidade);
                                                });
                                            })
                                            ->orderBy('nm_conta_correspondente_ccr')
                                            ->get();

        $results = array();
        foreach ($resultados as $ret) {
            $results[] = [ 'id' => $ret->correspondente->cd_conta_con, 'value' => $ret->nm_conta_correspondente_ccr, 'flag' => $ret->fl_correspondente_escritorio_ccr ];
        }
 
        return response()->json($results);
    }

    public function notificacao($id)
    {
        //Notifica o correspondente sobre o cadastro realizado, informando o acesso do site
        $user = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('cd_conta_con', $id)->first();

        $conta_logada = Conta::where('cd_conta_con', $this->conta)->first();
        $correspondente = Correspondente::where('cd_conta_con', $id)->first();
        $correspondente->email = $user->email;

        if ($correspondente->notificarFiliacaoConta($conta_logada)) {
            Flash::success('Correspondente notificado com sucesso. O correspondente foi notificado no email '.$correspondente->email);
        } else {
            Flash::error('Erro ao notificar correspondente. Verifique as configurações de notificação e tente novamente.');
        }

        return redirect('correspondentes');
    }

    public function redefinirSenha($id, $senha)
    {
        $id = \Crypt::decrypt($id);
        //Notifica o correspondente sobre o cadastro realizado, informando o acesso do site
        $user = User::where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('cd_conta_con', $id)->first();

        $conta_logada = Conta::where('cd_conta_con', $this->conta)->first();
        $conta_logada->senha = $senha;
        $correspondente = Correspondente::where('cd_conta_con', $id)->first();
        $correspondente->email = $user->email;

        if ($correspondente->notificarAlteracaoSenha($conta_logada)) {
            Flash::success('Correspondente notificado com sucesso. O correspondente foi notificado no email '.$correspondente->email);
        } else {
            Flash::error('Erro ao notificar correspondente. Verifique as configurações de notificação e tente novamente.');
        }

        return redirect('correspondentes');
    }

    public function cadastrarSenha($id)
    {
        $id = \Crypt::decrypt($id);
        
        $user = User::where('cd_conta_con', $id)->where('cd_nivel_niv', 3)->first();
        $user->setRememberToken(Str::random(60));
        $user->save();

        return view('auth.passwords.create')->with(
            ['token' => $user->remember_token, 'email' => $user->email]
        );
    }

    public function novaSenha(Request $request)
    {
        $nivel_url = \Crypt::encrypt(3);

        $user = User::where('remember_token', $request->token)->where('cd_nivel_niv', 3)->first();
        $user->password = Hash::make($request->password_confirmation);
        $user->save();

        return redirect(route('seleciona.perfil', ['nivel_url' => $nivel_url]));
    }

    public function buscaTipoProcesso($cliente)
    {
        $vinculo = ContaCorrespondente::where('cd_correspondente_cor', $this->conta)
                   ->where('cd_conta_con', $cliente)
                   ->first();
        if ($vinculo) {
            $tiposProcesso = TipoProcesso::where('cd_conta_con', $cliente)->get();
            echo json_encode($tiposProcesso);
        } else {
            echo json_encode([]);
        }
    }

    public function buscaTipoServico($cliente)
    {
        $vinculo = ContaCorrespondente::where('cd_correspondente_cor', $this->conta)
                   ->where('cd_conta_con', $cliente)
                   ->first();
        if ($vinculo) {
            $tiposServico = TipoServico::where('cd_conta_con', $cliente)->get();
            echo json_encode($tiposServico);
        } else {
            echo json_encode([]);
        }
    }
}
