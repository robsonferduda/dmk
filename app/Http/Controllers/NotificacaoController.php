<?php
 
namespace App\Http\Controllers;
 
use App\Conta;
use App\TipoProcesso;
use App\GrupoNotificacao;
use App\EmailNotificacao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
 
class NotificacaoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }

    public function processos()
    {
        Session::put('menu_pai','processos');
        Session::put('item_pai','processo.notificacao');

        $conta = Conta::where('cd_conta_con',$this->conta)->first();
        $tipos = TipoProcesso::where('cd_conta_con',$this->conta)->get();
        $grupos = GrupoNotificacao::where('cd_conta_con',$this->conta)->orderBy('ds_grupo_grn')->get();

        return view('notificacao/processos', compact('grupos','tipos'));
    }

    public function novoGrupo()
    {
        Session::put('menu_pai','processos');

        $conta = Conta::where('cd_conta_con',$this->conta)->first();

        return view('notificacao/novo-grupo',['conta' => $conta]);
    }

    public function preferencias()
    {
        $conta = Conta::where('cd_conta_con',$this->conta)->first();
        return view('configuracoes/notificacoes',['conta' => $conta]);
    }

    public function grupo(Request $request)
    {
        if($request->id_grupo){

            $grupo = GrupoNotificacao::find($request->id_grupo);

            $grupo->cd_tipo_processo_tpo = $request->cd_tipo_processo_tpo;
            $grupo->ds_grupo_grn = $request->ds_grupo_grn;

            $grupo->save();

        }else{

            $request->merge(['cd_conta_con' => $this->conta]);
            GrupoNotificacao::create($request->all());
        }

        return redirect('notificacao/processos');
    }

    public function addEmailGrupo(Request $request)
    {
        $dados = array('cd_grupo_notificacao_grn' => $request->id_grupo_email, 
                       'ds_email_egn' => $request->ds_email_egn);

        EmailNotificacao::create($dados);

        return redirect()->back();
    }

    public function deleteEmail($id)
    {
        $email = EmailNotificacao::find($id);
        $email->delete();

        return redirect()->back();
    }

    public function salvarPreferencias(Request $request)
    {
        $conta = Conta::find($this->conta);

        if(isset($request->fl_envio_notificacao_con)){
            $request->merge(['fl_envio_notificacao_con' => 'S']);
        }else{
            $request->merge(['fl_envio_notificacao_con' => 'N']);
        }

        if(isset($request->fl_notificacao_correspondente_con)){
            $request->merge(['fl_notificacao_correspondente_con' => 'S']);
        }else{
            $request->merge(['fl_notificacao_correspondente_con' => 'N']);
        }

        $conta->fl_envio_notificacao_con = $request->fl_envio_notificacao_con;
        $conta->fl_notificacao_correspondente_con = $request->fl_notificacao_correspondente_con;

        if($conta->saveOrFail())
            Flash::success('Preferências de notificações atualizadas com sucesso');
        else
            Flash::error('Erro ao atualizar preferências');

        return view('configuracoes/notificacoes',['conta' => $conta]);
    }    
    
}