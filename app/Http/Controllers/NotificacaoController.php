<?php
 
namespace App\Http\Controllers;
 
use App\Conta;
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

    public function preferencias()
    {
        $conta = Conta::where('cd_conta_con',$this->conta)->first();
        return view('configuracoes/notificacoes',['conta' => $conta]);
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