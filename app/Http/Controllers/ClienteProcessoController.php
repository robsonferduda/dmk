<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use App\User;
use App\Conta;
use App\Cliente;
use App\Estado;
use App\Entidade;
use App\Processo;
use App\TipoServico;
use App\TipoProcesso;
use App\StatusProcesso;
use App\ProcessoMensagem;
use App\EnderecoEletronico;
use App\Enums\TipoMensagem;
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
    }

    public function processos()
    {
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
        ->when(Auth::user()->role()->first()->slug == 'correspondente', function ($query) {
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

    public function getProcessosAndamento()
    {
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;
        $prazo_fatal = date("Y-m-d");

        $processos = (new Processo())->getProcessosAndamento($id_escritorio, null, null, null, null, null, null, null, null, $prazo_fatal, null, false, $cd_cliente_cli, null, null);
        return response()->json($processos);
    }

    public function acompanhamento()
    {
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
        ->when(Auth::user()->role()->first()->slug == 'correspondente', function ($query) {
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
        $vinculo = Conta::where('cd_conta_con', $processo->cd_conta_con)->first();

        $emails = EnderecoEletronico::where('cd_entidade_ete', $vinculo->entidade()->first()->cd_entidade_ete)->where('cd_tipo_endereco_eletronico_tee', \App\Enums\TipoEnderecoEletronico::NOTIFICACAO)->get();

        foreach ($emails as $email) {

            $processo->email = $email->dc_endereco_eletronico_ede;
            $processo->notificarCancelamento($processo);
        }

        Flash::success('Processo '.$processo->nu_processo_pro.' cancelado e escritório notificado');

        return redirect('cliente/processos/acompanhamento')->withInput();
    }

    public function pauta()
    {
        $cliente = Cliente::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first();

        $processos = array();

        return view('cliente/processo/pauta', ['processos' => $processos]);
    }

    public function relatorios()
    {
        return view('cliente/menu/relatorios');
    }

    public function acompanhar($id)
    {
        $id = \Crypt::decrypt($id);

            $processo = Processo::with('anexos')
                ->with('anexos.entidade.usuario')
                ->where('cd_processo_pro', $id)
                ->first();
        
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
    
        return view('cliente/processo/acompanhar', ['processo' => $processo, 'mensagens_externas' => $mensagens_externas, 'mensagens_internas' => $mensagens_internas]);
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

        $cliente = Cliente::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first();
        $correspondente = Conta::where('cd_conta_con', $id_correspondente)->first();

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

        return view('cliente/processo/novo', ['cliente' => $cliente,
                                            'correspondente' => $correspondente,
                                            'estados' => $estados,
                                            'varas' => $varas, 
                                            'tiposProcesso' => $tiposProcesso, 
                                            'tiposDeServico' => $tiposDeServico]);
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

        $processo = Processo::with('cliente')->with('correspondente')->with('cidade')->with('responsavel')->where('cd_conta_con', $id_escritorio)->where('cd_processo_pro', $id)->first();

        return view('cliente/processo/editar', ['cliente' => $cliente,
                                            'correspondente' => $correspondente,
                                            'processo' => $processo,
                                            'estados' => $estados, 
                                            'varas' => $varas,
                                            'tiposProcesso' => $tiposProcesso,                                              
                                            'tiposDeServico' => $tiposDeServico]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $id_escritorio = 64;
        $cd_cliente_cli = Cliente::where('cd_entidade_ete', Auth::user()->cd_entidade_ete)->first()->cd_cliente_cli;

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

        DB::commit();

        Flash::success('Processo cadastrado com sucesso');
        return redirect('cliente/processos/acompanhamento');
    }  

    public function update(Request $request)
    {

    }
}