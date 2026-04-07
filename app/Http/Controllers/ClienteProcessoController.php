<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use App\User;
use App\Conta;
use App\Contato;
use App\Cliente;
use App\Estado;
use App\Entidade;
use App\Processo;
use App\TipoServico;
use App\AreaDireito;
use App\TipoProcesso;
use App\StatusProcesso;
use App\GrupoNotificacao;
use App\LogNotificacao;
use App\ProcessoMensagem;
use App\EnderecoEletronico;
use App\Enums\TipoMensagem;
use App\Exports\Layout\LayoutProcesso;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\ProcessoTaxaHonorario;
use App\TaxaHonorario;
use App\ContaCorrespondente;
use App\Imports\ProcessoImport;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class ClienteProcessoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        Session::put('menu_pai','processos');
        Session::forget('item_pai');
    }

    public function processos()
    {
        Session::put('item_pai','processo.listar');

        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        if (!empty(\Cache::tags($id_escritorio, 'listaTiposProcesso')->get('tiposProcesso'))) {
            $tiposProcesso = \Cache::tags($id_escritorio, 'listaTiposProcesso')->get('tiposProcesso');
        } else {
            $tiposProcesso = TipoProcesso::where('cd_conta_con', $id_escritorio)->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags($id_escritorio, 'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);
        }

        $tiposServico = TipoServico::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_servico_tse')->get();

        $processos = Processo::with(array('correspondente' => function ($query) use ($id_escritorio) {
            $query->select('cd_conta_con', 'nm_razao_social_con', 'nm_fantasia_con');
            $query->with(array('contaCorrespondente' => function ($query) use ($id_escritorio) {
                $query->where('cd_conta_con', $id_escritorio);
            }));
        }))->with(array('cidade' => function ($query) {
            $query->select('cd_cidade_cde', 'nm_cidade_cde', 'cd_estado_est');
            $query->with(array('estado' => function ($query) {
                $query->select('sg_estado_est', 'cd_estado_est');
            }));
        }))->with(array('honorario' => function ($query) {
            $query->select('cd_processo_pro', 'cd_tipo_servico_tse');
            $query->with(array('tipoServico' => function ($query) {
                $query->select('cd_tipo_servico_tse', 'nm_tipo_servico_tse');
            }));
        }))->with('status')
        ->with(array('cliente' => function ($query) {
            $query->select('cd_cliente_cli', 'nm_fantasia_cli', 'nm_razao_social_cli');
        }))->where('cd_conta_con', $id_escritorio)
        ->when(Auth::user()->role()->first()->slug == 'cliente', function ($query) {
            return $query->whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO, 
                                                                \StatusProcesso::CANCELADO]);
        })
        ->where('cd_cliente_cli', $cd_cliente_cli)
            ->take(50)
            //->orderBy('dt_prazo_fatal_pro','DESC')
            ->orderBy('created_at', 'desc')
            ->select('cd_processo_pro', 'nu_processo_pro', 'cd_cliente_cli', 'cd_cidade_cde', 'cd_correspondente_cor', 'hr_audiencia_pro', 'dt_solicitacao_pro', 'dt_prazo_fatal_pro', 'nm_autor_pro', 'cd_status_processo_stp')
            ->get();

        return view('cliente/processo/listar', ['processos' => $processos,'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico]);
    }  

    public function novo()
    {
        Session::put('item_pai','processo.novo');

        if (!\Cache::has('estados')) {
            $estados = Estado::orderBy('nm_estado_est')->get();
            \Cache::put('estados', $estados, now()->addMinutes(1440));
        } else {
            $estados =  \Cache::get('estados');
        }

        $id_escritorio = 64;
        $id_correspondente = 83;
        $escritorio = Conta::where('cd_conta_con', $id_escritorio)->first();
        $escritorio_entidade = $escritorio->entidade()->first();

        $cliente = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first();
        $correspondente = Conta::where('cd_conta_con', $id_correspondente)->first();
        $advogados = Contato::where('cd_entidade_ete', $escritorio_entidade->cd_entidade_ete)->get();

        $sub = \DB::table('vara_var')
                ->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")
                ->whereNull('deleted_at')
                ->whereRaw("cd_conta_con = $id_escritorio")
                ->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
            ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
            ->orderByRaw("nullif(number,'')::int,caracter")
            ->get();

        $tiposProcesso  = TipoProcesso::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_processo_tpo')->get();
        $tiposDeServico = TipoServico::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_servico_tse')->get();
        $areas = AreaDireito::where('cd_conta_con', $id_escritorio)->orderBy('dc_area_direito_ado')->get();

        return view('cliente/processo/novo', ['cliente' => $cliente,
                                            'correspondente' => $correspondente,
                                            'estados' => $estados,
                                            'escritorio' => $escritorio,
                                            'advogados' => $advogados,
                                            'varas' => $varas, 
                                            'tiposProcesso' => $tiposProcesso, 
                                            'tiposDeServico' => $tiposDeServico,
                                            'areas' => $areas]);
    }  

    public function getProcessosAndamento()
    {
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;
        $prazo_fatal = date("Y-m-d");

        $processos = (new Processo())->getProcessosAndamento($id_escritorio, null, null, null, null, null, null, null, null, $prazo_fatal, null, false, $cd_cliente_cli, null, null, null, null);
        return response()->json($processos);
    }

    public function acompanhamento()
    {
        Session::put('item_pai','processo.acompanhamento');

        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        if (!empty(\Cache::tags($id_escritorio, 'listaTiposProcesso')->get('tiposProcesso'))) {
            $tiposProcesso = \Cache::tags($id_escritorio, 'listaTiposProcesso')->get('tiposProcesso');
        } else {
            $tiposProcesso = TipoProcesso::where('cd_conta_con', $id_escritorio)->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags($id_escritorio, 'listaTiposProcesso')->put('tiposProcesso', $tiposProcesso, $expiresAt);
        }

        $tiposServico = TipoServico::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_servico_tse')->get();

        $processos = Processo::with(array('correspondente' => function ($query) use ($id_escritorio) {
            $query->select('cd_conta_con', 'nm_razao_social_con', 'nm_fantasia_con');
            $query->with(array('contaCorrespondente' => function ($query) use ($id_escritorio) {
                $query->where('cd_conta_con', $id_escritorio);
            }));
        }))->with(array('cidade' => function ($query) {
            $query->select('cd_cidade_cde', 'nm_cidade_cde', 'cd_estado_est');
            $query->with(array('estado' => function ($query) {
                $query->select('sg_estado_est', 'cd_estado_est');
            }));
        }))->with(array('honorario' => function ($query) {
            $query->select('cd_processo_pro', 'cd_tipo_servico_tse');
            $query->with(array('tipoServico' => function ($query) {
                $query->select('cd_tipo_servico_tse', 'nm_tipo_servico_tse');
            }));
        }))->with('status')
        ->with(array('cliente' => function ($query) {
            $query->select('cd_cliente_cli', 'nm_fantasia_cli', 'nm_razao_social_cli');
        }))->where('cd_conta_con', $id_escritorio)
        ->when(Auth::user()->role()->first()->slug == 'cliente', function ($query) {
            return $query->whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO, 
                                                                \StatusProcesso::CANCELADO]);
        })
        ->where('cd_cliente_cli', $cd_cliente_cli)
            ->take(50)
            //->orderBy('dt_prazo_fatal_pro','DESC')
            ->orderBy('created_at', 'desc')
            ->select('cd_processo_pro', 'nu_processo_pro', 'cd_cliente_cli', 'cd_cidade_cde', 'cd_correspondente_cor', 'hr_audiencia_pro', 'dt_solicitacao_pro', 'dt_prazo_fatal_pro', 'nm_autor_pro', 'cd_status_processo_stp')
            ->get();

        $status = StatusProcesso::whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO, \StatusProcesso::CANCELADO])
                  ->orderBy('nm_status_processo_conta_stp')
                  ->get();

        return view('cliente/processo/acompanhamento', ['processos' => $processos,'tiposProcesso' => $tiposProcesso,'tiposServico' => $tiposServico, 'status' => $status]);
    } 

    public function detalhes($id)
    {
        $id = \Crypt::decrypt($id);
        $cliente = Cliente::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first();

        $processo = Processo::where('cd_processo_pro', $id)->where('cd_cliente_cli', $cliente->cd_cliente_cli)->first();
        return view('cliente/processo/detalhes', ['processo' => $processo]);
    }

    public function cancelar($id)
    {
        $id = \Crypt::decrypt($id);
        $cliente = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first();

        $processo = Processo::where('cd_processo_pro', $id)->where('cd_cliente_cli', $cliente->cd_cliente_cli)->first();

        //O processo deve ser cancelado e o escritório notificado
        $processo->cd_status_processo_stp = \StatusProcesso::CANCELADO_PELO_CLIENTE;
        $processo->save();

        $grupo = GrupoNotificacao::where('cd_conta_con', $processo->cd_conta_con)
            ->where('cd_tipo_processo_tpo', $processo->cd_tipo_processo_tpo)
            ->first();

        if ($grupo && count($grupo->emails)) {
            foreach ($grupo->emails as $email) {
                $email_notificacao = $email->ds_email_egn;
                $processo->email = $email_notificacao;
                $processo->notificarCancelamento($processo);

                $log = array('tipo_notificacao' => 'cancelamento_processo_cliente',
                            'email_destinatario' => $email_notificacao,
                            'cd_remetente' => $cliente->cd_cliente_cli,
                            'cd_destinatario' => $processo->cd_conta_con,
                            'cd_processo' => $processo->cd_processo_pro,
                            'nu_processo' => $processo->nu_processo_pro,
                            'origem' => 'cliente');

                LogNotificacao::create($log);
            }
        }

        Flash::success('Processo '.$processo->nu_processo_pro.' cancelado e escritório notificado');

        return redirect('cliente/processos/acompanhamento')->withInput();
    }

    public function calendario()
    {
        Session::put('menu_pai','calendario');
        Session::put('item_pai','calendario.listar'); 

        $cliente = Cliente::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first();

        $processos = array();

        return view('cliente/calendario', ['processos' => $processos]);
    }

    public function pauta()
    {
        Session::put('menu_pai','pauta');
        Session::put('item_pai','pauta.listar');

        $id_escritorio = 64;

        $prazo_fatal = date('Y-m-d');

        $tiposProcesso = TipoProcesso::where('cd_conta_con', $id_escritorio)->get();

        $status = StatusProcesso::whereNotIn('cd_status_processo_stp', [\StatusProcesso::FINALIZADO, \StatusProcesso::CANCELADO])
                  ->orderBy('nm_status_processo_conta_stp')
                  ->get();

        return view('cliente/processo/pauta', [
            'prazo_fatal'   => $prazo_fatal,
            'tiposProcesso' => $tiposProcesso,
            'status'        => $status,
        ]);
    }

    public function listarPauta(Request $request)
    {
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $processo            = $request->processo ?: null;
        $nm_correspondente   = $request->nm_correspondente ?: null;
        $tipo                = $request->tipo ?: null;
        $servico             = $request->servico ?: null;
        $reu                 = $request->reu ?: null;
        $autor               = $request->autor ?: null;
        $comarca             = $request->comarca ?: null;
        $statusProcesso      = $request->statusProcesso ?: null;
        $numeroAcompanhamento = $request->numero_acompanhamento ?: null;
        $area                = $request->area ?: null;
        $data = $request->data
            ? date('Y-m-d', strtotime(str_replace('/', '-', $request->data)))
            : date('Y-m-d');

        $processos = (new Processo())->getProcessosAndamento(
            $id_escritorio, $processo, null, null, $tipo, $servico, null,
            $reu, $autor, $data, $comarca, false, $cd_cliente_cli,
            $statusProcesso, $numeroAcompanhamento, $area, $nm_correspondente
        );

        return response()->json($processos);
    }

    // -----------------------------------------------------------------------
    // Dashboard do cliente
    // -----------------------------------------------------------------------

    public function dashboardContadores()
    {
        $id_escritorio  = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;
        $hoje           = date('Y-m-d');
        $em7dias        = date('Y-m-d', strtotime('+7 days'));

        $base = Processo::where('cd_conta_con', $id_escritorio)
                        ->where('cd_cliente_cli', $cd_cliente_cli)
                        ->whereNull('deleted_at');

        $total_ativos = (clone $base)
            ->whereNotIn('cd_status_processo_stp', [\App\Enums\StatusProcesso::FINALIZADO, \App\Enums\StatusProcesso::CANCELADO, \App\Enums\StatusProcesso::CANCELADO_PELO_ESCRITORIO])
            ->count();

        $audiencias_hoje = (clone $base)
            ->whereNotIn('cd_status_processo_stp', [\App\Enums\StatusProcesso::FINALIZADO, \App\Enums\StatusProcesso::CANCELADO, \App\Enums\StatusProcesso::CANCELADO_PELO_ESCRITORIO])
            ->whereDate('dt_prazo_fatal_pro', $hoje)
            ->count();

        $proximos_7_dias = (clone $base)
            ->whereNotIn('cd_status_processo_stp', [\App\Enums\StatusProcesso::FINALIZADO, \App\Enums\StatusProcesso::CANCELADO, \App\Enums\StatusProcesso::CANCELADO_PELO_ESCRITORIO])
            ->whereDate('dt_prazo_fatal_pro', '>', $hoje)
            ->whereDate('dt_prazo_fatal_pro', '<=', $em7dias)
            ->count();

        $mensagens_nao_lidas = ProcessoMensagem::join('processo_pro', 'processo_pro.cd_processo_pro', '=', 'processo_mensagem_prm.cd_processo_pro')
            ->where('processo_pro.cd_conta_con', $id_escritorio)
            ->where('processo_pro.cd_cliente_cli', $cd_cliente_cli)
            ->where('processo_mensagem_prm.cd_tipo_mensagem_tim', \App\Enums\TipoMensagem::CLIENTE)
            ->where('processo_mensagem_prm.destinatario_prm', $cd_cliente_cli)
            ->whereNull('processo_mensagem_prm.fl_leitura_prm')
            ->whereNull('processo_pro.deleted_at')
            ->count();

        $honorarios_pendentes = \App\TaxaHonorarioAlteracao::whereHas('processo', function ($q) use ($id_escritorio, $cd_cliente_cli) {
            $q->where('cd_conta_con', $id_escritorio)
              ->where('cd_cliente_cli', $cd_cliente_cli);
        })->whereNull('fl_aceito_tha')->count();

        return response()->json(compact(
            'total_ativos',
            'audiencias_hoje',
            'proximos_7_dias',
            'mensagens_nao_lidas',
            'honorarios_pendentes'
        ));
    }

    public function dashboardPautaHoje()
    {
        $id_escritorio  = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $processos = (new Processo())->getProcessosAndamento(
            $id_escritorio, null, null, null, null, null, null,
            null, null, date('Y-m-d'), null, false, $cd_cliente_cli,
            null, null, null, null
        );

        return response()->json($processos);
    }

    public function dashboardProximas()
    {
        $id_escritorio  = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $processos = DB::select("
            SELECT t1.cd_processo_pro,
                   t1.nu_processo_pro,
                   t1.dt_prazo_fatal_pro,
                   t1.hr_audiencia_pro,
                   t2.nm_status_processo_conta_stp,
                   t2.ds_color_stp,
                   t7.nm_cidade_cde,
                   t8.sg_estado_est,
                   t10.nm_tipo_servico_tse,
                   t5.nm_conta_correspondente_ccr,
                   t13.nm_tipo_processo_tpo
            FROM processo_pro t1
            JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
            JOIN cidade_cde t7 ON t1.cd_cidade_cde = t7.cd_cidade_cde
            JOIN estado_est t8 ON t7.cd_estado_est = t8.cd_estado_est
            LEFT JOIN conta_correspondente_ccr t5 ON t1.cd_conta_con = t5.cd_conta_con AND t1.cd_correspondente_cor = t5.cd_correspondente_cor
            LEFT JOIN processo_taxa_honorario_pth t9 ON t1.cd_processo_pro = t9.cd_processo_pro
            LEFT JOIN tipo_servico_tse t10 ON t9.cd_tipo_servico_tse = t10.cd_tipo_servico_tse
            JOIN tipo_processo_tpo t13 ON t13.cd_tipo_processo_tpo = t1.cd_tipo_processo_tpo
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_cliente_cli = :cliente
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.dt_prazo_fatal_pro > current_date
              AND t1.deleted_at IS NULL
            ORDER BY t1.dt_prazo_fatal_pro, t1.hr_audiencia_pro
            LIMIT 10
        ", ['conta' => $id_escritorio, 'cliente' => $cd_cliente_cli]);

        return response()->json($processos);
    }

    public function dashboardMensagens()
    {
        $id_escritorio  = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $mensagens = ProcessoMensagem::with('processo')
            ->join('processo_pro', 'processo_pro.cd_processo_pro', '=', 'processo_mensagem_prm.cd_processo_pro')
            ->where('processo_pro.cd_conta_con', $id_escritorio)
            ->where('processo_pro.cd_cliente_cli', $cd_cliente_cli)
            ->where('processo_mensagem_prm.cd_tipo_mensagem_tim', \App\Enums\TipoMensagem::CLIENTE)
            ->where('processo_mensagem_prm.destinatario_prm', $cd_cliente_cli)
            ->whereNull('processo_mensagem_prm.fl_leitura_prm')
            ->whereNull('processo_pro.deleted_at')
            ->select('processo_mensagem_prm.*')
            ->orderBy('processo_mensagem_prm.created_at', 'DESC')
            ->limit(5)
            ->get();

        $dados = $mensagens->map(function ($msg) {
            return [
                'nu_processo'  => optional($msg->processo)->nu_processo_pro ?? 'N/D',
                'token'        => \Crypt::encrypt($msg->cd_processo_pro),
                'texto'        => \Illuminate\Support\Str::limit($msg->texto_mensagem_prm, 80),
                'data'         => date('d/m/Y H:i', strtotime($msg->created_at)),
            ];
        });

        return response()->json($dados);
    }

    public function dashboardStatus()
    {
        $id_escritorio  = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $resultado = DB::select("
            SELECT t2.nm_status_processo_conta_stp,
                   t2.ds_color_stp,
                   COUNT(t1.cd_processo_pro) as total
            FROM processo_pro t1
            JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_cliente_cli = :cliente
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.deleted_at IS NULL
            GROUP BY t2.nm_status_processo_conta_stp, t2.ds_color_stp
            ORDER BY total DESC
        ", ['conta' => $id_escritorio, 'cliente' => $cd_cliente_cli]);

        return response()->json($resultado);
    }

    public function relatorios()
    {
        Session::put('menu_pai','relatorios');
        Session::put('item_pai','relatorios.listar');        

        return view('cliente/menu/relatorios');
    }

    public function acompanhar($id)
    {
        $id = \Crypt::decrypt($id);

        $cliente = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first();

        $processo = Processo::with('anexos')
            ->with('anexos.entidade.usuario')
            ->where('cd_processo_pro', $id)
            ->where('cd_cliente_cli', $cliente->cd_cliente_cli)
            ->first();

        if (!$processo) {
            Flash::error('Processo não encontrado ou você não tem permissão para acessá-lo.');
            return redirect('cliente/processos/acompanhamento');
        }

        $mensagens_externas = ProcessoMensagem::where('cd_processo_pro', $id)
                                                ->where('cd_tipo_mensagem_tim', TipoMensagem::EXTERNA)
                                                ->with('entidadeRemetente')
                                                ->with('entidadeDestinatario')
                                                ->withTrashed()
                                                ->orderBy('created_at', 'ASC')
                                                ->get();

        $mensagens_internas = ProcessoMensagem::where('cd_processo_pro', $id)
                                                ->where('cd_tipo_mensagem_tim', TipoMensagem::INTERNA)
                                                ->with('entidadeRemetente')
                                                ->with('entidadeDestinatario')
                                                ->withTrashed()
                                                ->orderBy('created_at', 'ASC')
                                                ->get();

        $mensagens_cliente = ProcessoMensagem::where('cd_processo_pro', $id)
                                                ->where('cd_tipo_mensagem_tim', TipoMensagem::CLIENTE)
                                                ->withTrashed()
                                                ->orderBy('created_at', 'ASC')
                                                ->get();

        return view('cliente/processo/acompanhar', [
            'processo'           => $processo,
            'mensagens_externas' => $mensagens_externas,
            'mensagens_internas' => $mensagens_internas,
            'mensagens_cliente'  => $mensagens_cliente,
        ]);
    }

    

    public function editar($id)
    {
        $id = \Crypt::decrypt($id);

        $id_escritorio = 64;
        $id_correspondente = 83;

        $cliente = Cliente::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first();
        $correspondente = Conta::where('cd_conta_con', $id_correspondente)->first();
        
        if (!\Cache::has('estados')) {
            $estados = Estado::orderBy('nm_estado_est')->get();
            \Cache::put('estados', $estados, now()->addMinutes(1440));
        } else {
            $estados =  \Cache::get('estados');
        }

        $sub = \DB::table('vara_var')->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->whereRaw("cd_conta_con = $id_escritorio")->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
        ->get();

        $tiposProcesso = TipoProcesso::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_processo_tpo')->get();
        $tiposDeServico = TipoServico::where('cd_conta_con', $id_escritorio)->orderBy('nm_tipo_servico_tse')->get();
        $areas = AreaDireito::where('cd_conta_con', $id_escritorio)->orderBy('dc_area_direito_ado')->get();

        $processo = Processo::with('cliente')->with('correspondente')->with('cidade')->with('responsavel')->where('cd_conta_con', $id_escritorio)->where('cd_processo_pro', $id)->first();

        return view('cliente/processo/editar', ['cliente' => $cliente,
                                            'correspondente' => $correspondente,
                                            'processo' => $processo,
                                            'estados' => $estados, 
                                            'varas' => $varas,
                                            'tiposProcesso' => $tiposProcesso,
                                            'tiposDeServico' => $tiposDeServico,
                                            'areas' => $areas]);
    }

    public function importar(Request $request)
    {
        Session::put('item_pai','processo.importar');

        $id_escritorio = 64;
        $cliente = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)
                    ->where('cd_conta_con', $id_escritorio)
                    ->first();

        // Download da planilha pré-preenchida com os dados do cliente logado
        if ($request->isMethod('get') && $request->has('download')) {
            $dados = [
                'cliente'    => $cliente,
                'num_linhas' => 20,
            ];
            $fileName = 'layout-' . $cliente->nm_razao_social_cli . '.xlsx';
            return \Excel::download(new LayoutProcesso($dados), $fileName, \Maatwebsite\Excel\Excel::XLSX);
        }

        // POST: importar planilha enviada pelo cliente
        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');
            $extensions = ['xls', 'xlsx', 'XLSX', 'XLS'];

            if (!in_array($file->getClientOriginalExtension(), $extensions)) {
                Flash::error('Extensão da planilha é inválida. Extensões permitidas: "xls","xlsx","XLSX","XLS".');
                return redirect('cliente/processos/importar');
            }

            try {
                $nomeOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extensao     = $file->getClientOriginalExtension();
                $nomeUnico    = $nomeOriginal . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $extensao;
                $file->storeAs('planilhas_importacao', $nomeUnico);

                $colunas = ['CLIENTE','ADVOGADO_SOLICITANTE','NUMERO_PROCESSO','AUTOR','REU','DATA_SOLICITACAO','DATA_PRAZO_FATAL','HORA','ESTADO','COMARCA','VARA','TIPO_DE_SERVICO','TIPO_DE_PROCESSO','AREA_DO_DIREITO'];

                HeadingRowFormatter::default('none');
                $headings = (new HeadingRowImport)->toArray($file);

                foreach ($colunas as $coluna) {
                    if (!in_array($coluna, $headings[0][0])) {
                        Flash::error('Coluna (' . $coluna . ') não encontrada na planilha');
                        return redirect('cliente/processos/importar');
                    }
                }

                HeadingRowFormatter::default('slug');

                $import = new ProcessoImport($nomeUnico);
                Excel::import($import, $file);

                $rowCount = $import->getRowCount();
                Flash::success($rowCount . ' Processo(s) criado(s) com sucesso.');
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                $errors = [];
                foreach ($failures as $failure) {
                    foreach ($failure->errors() as $error) {
                        $errors[] = 'Linha ' . $failure->row() . ': ' . $error;
                    }
                }
                Flash::error('Erros na importação: ' . implode(' | ', $errors));
            } catch (\Exception $e) {
                Flash::error('Erro ao importar: ' . $e->getMessage());
            }

            return redirect('cliente/processos/importar');
        }

        return view('cliente/processo/importar', ['cliente' => $cliente]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;
        $emailsNotificados = array();

        $entidade = Entidade::create([
            'cd_conta_con'         => $id_escritorio,
            'cd_tipo_entidade_tpe' => \TipoEntidade::PROCESSO
        ]);

        if (!empty($request->dt_solicitacao_pro)) {
            $request->merge(['dt_solicitacao_pro' => date('Y-m-d', strtotime(str_replace('/', '-', $request->dt_solicitacao_pro)))]);
        }
        if (!empty($request->dt_prazo_fatal_pro)) {
            $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d', strtotime(str_replace('/', '-', $request->dt_prazo_fatal_pro)))]);
        }
        
        $request->merge(['cd_status_processo_stp' => \StatusProcesso::CADASTRADO_CLIENTE]);
        $request->merge(['cd_conta_con' => $id_escritorio]);
        $request->merge(['cd_cliente_cli' => $cd_cliente_cli]);
        $request->merge(['fl_cadastro_cliente_cli' => true]);

        if ($entidade) {

            $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);
            $request->merge(['cd_user_cadastro_pro' => Auth::user()->id]);

            $processo = new Processo();
            $processo->fill($request->all());

            if (!$processo->saveOrFail()) {
                DB::rollBack();
                Flash::error('Erro ao atualizar dados');
                return redirect('processos');
            }

        } else {
            DB::rollBack();
            Flash::error('Erro ao inserir dados');
            return redirect('processos');
        }

        $dados = new \stdClass();
        $dados->servico = $request->cd_tipo_servico_tse;
        $dados->servicoCorrespondente = $request->cd_tipo_servico_tse;
        $dados->nota_fiscal_cliente = null;
        $dados->valor_cliente = null;
        $dados->valor_correspondente = null;

        if ($processo->cd_cidade_cde && $request->cd_tipo_servico_tse) {
            $taxaCliente = TaxaHonorario::where('cd_conta_con', $id_escritorio)
                                        ->where('cd_tipo_servico_tse', $request->cd_tipo_servico_tse)
                                        ->where('cd_cidade_cde', $processo->cd_cidade_cde)
                                        ->where('cd_entidade_ete', Auth::user()->cd_entidade_ete)
                                        ->select('nu_taxa_the')->first();
            $dados->valor_cliente = $taxaCliente ? $taxaCliente->nu_taxa_the : null;
        }

        if ($processo->cd_correspondente_cor && $processo->cd_cidade_cde && $request->cd_tipo_servico_tse) {
            $entidadeCorrespondente = ContaCorrespondente::select('cd_entidade_ete')
                                                        ->where('cd_conta_con', $id_escritorio)
                                                        ->where('cd_correspondente_cor', $processo->cd_correspondente_cor)
                                                        ->first();
            if ($entidadeCorrespondente) {
                $taxaCorrespondente = TaxaHonorario::where('cd_conta_con', $id_escritorio)
                                                   ->where('cd_tipo_servico_tse', $request->cd_tipo_servico_tse)
                                                   ->where('cd_cidade_cde', $processo->cd_cidade_cde)
                                                   ->where('cd_entidade_ete', $entidadeCorrespondente->cd_entidade_ete)
                                                   ->select('nu_taxa_the')->first();
                $dados->valor_correspondente = $taxaCorrespondente ? $taxaCorrespondente->nu_taxa_the : null;
            }
        }

        $this->salvarHonorarios($processo->cd_processo_pro, $dados, $id_escritorio);

        DB::commit();

        $processo->load('cliente');

        $contaEscritorio = Conta::where('cd_conta_con', $id_escritorio)->first();

        if ($contaEscritorio && $contaEscritorio->entidade()->first()) {
            $emailsNotificacao = EnderecoEletronico::where('cd_entidade_ete', $contaEscritorio->entidade()->first()->cd_entidade_ete)
                                                ->where('cd_tipo_endereco_eletronico_tee', \App\Enums\TipoEnderecoEletronico::NOTIFICACAO)
                                                ->get();

            foreach ($emailsNotificacao as $email) {
                $processo->email = $email->dc_endereco_eletronico_ede;
                $processo->notificarCadastroCliente($processo);

                $log = array('tipo_notificacao' => 'cadastro_processo_cliente',
                            'email_destinatario' => $email->dc_endereco_eletronico_ede,
                            'cd_remetente' => $cd_cliente_cli,
                            'cd_destinatario' => $id_escritorio,
                            'cd_processo' => $processo->cd_processo_pro,
                            'nu_processo' => $processo->nu_processo_pro,
                            'origem' => 'cliente');

                LogNotificacao::create($log);
                $emailsNotificados[] = $email->dc_endereco_eletronico_ede;
            }
        }

        if (count($emailsNotificados) > 0) {
            Flash::success('Processo cadastrado com sucesso e escritório notificado: '.implode(', ', $emailsNotificados));
        } else {
            Flash::success('Processo cadastrado com sucesso. Escritório sem e-mail de notificação cadastrado.');
        }

        return redirect('cliente/processos/acompanhamento');
    }  

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

        $processo = Processo::where('cd_processo_pro', $id)
                            ->where('cd_conta_con', $id_escritorio)
                            ->where('cd_cliente_cli', $cd_cliente_cli)
                            ->first();

        if (!$processo) {
            DB::rollBack();
            Flash::error('Processo não encontrado');
            return redirect('cliente/processos/acompanhamento');
        }

        if (!empty($request->dt_solicitacao_pro)) {
            $request->merge(['dt_solicitacao_pro' => date('Y-m-d', strtotime(str_replace('/', '-', $request->dt_solicitacao_pro)))]);
        }
        if (!empty($request->dt_prazo_fatal_pro)) {
            $request->merge(['dt_prazo_fatal_pro' => date('Y-m-d', strtotime(str_replace('/', '-', $request->dt_prazo_fatal_pro)))]);
        }

        $request->merge(['cd_status_processo_stp' => \StatusProcesso::ALTERADO_PELO_CLIENTE]);

        $processo->fill($request->all());

        if (!$processo->saveOrFail()) {
            DB::rollBack();
            Flash::error('Erro ao atualizar dados');
            return redirect('cliente/processos/acompanhamento');
        }

        $dados = new \stdClass();
        $dados->servico = $request->cd_tipo_servico_tse;
        $dados->servicoCorrespondente = $request->cd_tipo_servico_tse;
        $dados->nota_fiscal_cliente = null;
        $dados->valor_cliente = null;
        $dados->valor_correspondente = null;

        if ($processo->cd_cidade_cde && $request->cd_tipo_servico_tse) {
            $taxaCliente = TaxaHonorario::where('cd_conta_con', $id_escritorio)
                                        ->where('cd_tipo_servico_tse', $request->cd_tipo_servico_tse)
                                        ->where('cd_cidade_cde', $processo->cd_cidade_cde)
                                        ->where('cd_entidade_ete', Auth::user()->cd_entidade_ete)
                                        ->select('nu_taxa_the')->first();
            $dados->valor_cliente = $taxaCliente ? $taxaCliente->nu_taxa_the : null;
        }

        if ($processo->cd_correspondente_cor && $processo->cd_cidade_cde && $request->cd_tipo_servico_tse) {
            $entidadeCorrespondente = ContaCorrespondente::select('cd_entidade_ete')
                                                        ->where('cd_conta_con', $id_escritorio)
                                                        ->where('cd_correspondente_cor', $processo->cd_correspondente_cor)
                                                        ->first();
            if ($entidadeCorrespondente) {
                $taxaCorrespondente = TaxaHonorario::where('cd_conta_con', $id_escritorio)
                                                   ->where('cd_tipo_servico_tse', $request->cd_tipo_servico_tse)
                                                   ->where('cd_cidade_cde', $processo->cd_cidade_cde)
                                                   ->where('cd_entidade_ete', $entidadeCorrespondente->cd_entidade_ete)
                                                   ->select('nu_taxa_the')->first();
                $dados->valor_correspondente = $taxaCorrespondente ? $taxaCorrespondente->nu_taxa_the : null;
            }
        }

        $this->salvarHonorarios($processo->cd_processo_pro, $dados, $id_escritorio);

        DB::commit();

        Flash::success('Processo ' . $processo->nu_processo_pro . ' atualizado com sucesso');

        return redirect('cliente/processos/acompanhamento');
    }

    private function salvarHonorarios($id, $dados, $cdContaCon)
    {
        if (empty($dados->nota_fiscal_cliente)) {
            $dados->nota_fiscal_cliente = null;
        }

        if (empty($dados->valor_cliente)) {
            $dados->valor_cliente = null;
        }

        if (empty($dados->valor_correspondente)) {
            $dados->valor_correspondente = null;
        }

        $valor = ProcessoTaxaHonorario::where('cd_conta_con', $cdContaCon)
                                      ->where('cd_processo_pro', $id)->first();

        if (!empty($valor)) {
            $valor->vl_taxa_honorario_cliente_pth      = $dados->valor_cliente;
            $valor->vl_taxa_honorario_correspondente_pth = $dados->valor_correspondente;
            $valor->cd_tipo_servico_tse                = $dados->servico;
            $valor->cd_tipo_servico_correspondente_tse = $dados->servicoCorrespondente;
            $valor->vl_taxa_cliente_pth                = $dados->nota_fiscal_cliente;

            if (!$valor->saveOrFail()) {
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('cliente/processos/acompanhamento');
            }
        } else {
            $valor = ProcessoTaxaHonorario::create([
                'cd_conta_con'                         => $cdContaCon,
                'cd_processo_pro'                      => $id,
                'cd_tipo_servico_tse'                  => $dados->servico,
                'cd_tipo_servico_correspondente_tse'   => $dados->servicoCorrespondente,
                'vl_taxa_honorario_cliente_pth'        => $dados->valor_cliente,
                'vl_taxa_honorario_correspondente_pth' => $dados->valor_correspondente,
                'vl_taxa_cliente_pth'                  => $dados->nota_fiscal_cliente,
            ]);

            if (!$valor) {
                Flash::error('Erro ao atualizar dados');
                DB::rollBack();
                return redirect('cliente/processos/acompanhamento');
            }
        }

        return true;
    }

    //Controle de Mensagens dos Processos dos Cliente
    public function enviarMensagem(Request $request)
    {
        try {
            $processo = Processo::where('cd_processo_pro', $request->processo)->first();

            if (!$processo) {
                return response()->json(['success' => false, 'message' => 'Processo não encontrado'], 404);
            }

            $mensagem = new ProcessoMensagem();
            $mensagem->remetente_prm        = $processo->cd_cliente_cli;
            $mensagem->destinatario_prm     = $processo->cd_conta_con;
            $mensagem->cd_tipo_mensagem_tim = TipoMensagem::CLIENTE;
            $mensagem->cd_processo_pro      = $request->processo;
            $mensagem->texto_mensagem_prm   = $request->msg;

            $mensagem->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}