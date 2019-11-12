<?php

namespace App\Http\Controllers;

use Auth;
use App\Conta;
use App\Processo;
use App\ProcessoMensagem;
use App\EnderecoEletronico;
use App\Enums\Nivel;
use App\Enums\TipoMensagem;
use App\Events\EventNotification;
use App\Enums\TipoEnderecoEletronico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class MensagemController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
        $this->nivel = \Session::get('SESSION_NIVEL');

    }

    public function index()
    {
        return view('home');
    }

    public function enviar(Request $request)
    {
    	$processo = Processo::where('cd_processo_pro',$request->processo)->first();

        $tipo = ($request->tipo == 'interna') ? TipoMensagem::INTERNA : TipoMensagem::EXTERNA;
        $remetente = ($this->nivel == Nivel::COLABORADOR) ? $this->entidade : $this->conta;

        if($tipo == TipoMensagem::INTERNA){

            $destinatario = $this->conta; 
        }

        if($tipo == TipoMensagem::EXTERNA){

            if($this->nivel == Nivel::CORRESPONDENTE) 
                $destinatario = $processo->cd_conta_con; 
            else
                $destinatario = $processo->cd_correspondente_cor;
        }

		$mensagem = new ProcessoMensagem();

    	$mensagem->remetente_prm = $remetente;            
        $mensagem->destinatario_prm = $destinatario;
        $mensagem->cd_tipo_mensagem_tim = $tipo;    	
    	$mensagem->cd_processo_pro = $request->processo;
        $mensagem->texto_mensagem_prm = $request->msg;

    	if($mensagem->save()){

            $emails = EnderecoEletronico::where('cd_conta_con',$destinatario)->where('cd_tipo_endereco_eletronico_tee',TipoEnderecoEletronico::NOTIFICACAO)->get();

            foreach ($emails as $email) {

                $processo->email = $email->dc_endereco_eletronico_ede;
                //$processo->notificarNovaMensagem($processo);
                
            }

            $mensagens_tmp = (new \App\ProcessoMensagem)->getMensagensPendentesDestinatario($mensagem->destinatario_prm);

            $mensagens = array();

            foreach($mensagens_tmp as $m){

                if($m->cd_tipo_mensagem_tim == TipoMensagem::EXTERNA){

                    $cd_entidade = $m->entidadeRemetente->entidade->cd_entidade_ete;

                    if(file_exists(public_path().'/img/users/ent'.$cd_entidade.'.png'))
                        $img = 'ent'.$cd_entidade.'.png';
                    else
                        $img = 'user.png';


                    $mensagens[] = array('remetente' => $m->entidadeRemetente->nm_razao_social_con,
                                         'entidade' => $cd_entidade,
                                         'img' => $img,
                                         'mensagem' => str_limit($m->texto_mensagem_prm , 50),
                                         'data' => date('H:i:s d/m/Y', strtotime($m->created_at)),
                                         'processo' => $m->processo->nu_processo_pro,
                                         'url' => url('processos/acompanhamento/'.\Crypt::encrypt($m->cd_processo_pro)));
                }else{

                    $cd_entidade = $m->entidadeInterna->cd_entidade_ete;

                    if(file_exists(public_path().'img/users/ent'.$cd_entidade))
                        $img = 'ent'.$cd_entidade;
                    else
                        $img = 'user.png';


                    $mensagens[] = array('remetente' => $m->entidadeInterna->usuario->name,
                                         'entidade' => $cd_entidade,
                                         'img' => $img,
                                         'mensagem' => str_limit($m->texto_mensagem_prm , 50),
                                         'data' => date('H:i:s d/m/Y', strtotime($m->created_at)),
                                         'processo' => $m->processo->nu_processo_pro,
                                         'url' => url('processos/acompanhamento/'.\Crypt::encrypt($m->cd_processo_pro)));

                }
            }

            //event(new EventNotification(array('canal' => 'notificacao', 'conta' => $mensagem->destinatario_prm, 'total' => count($mensagens), 'mensagens' => $mensagens)));          

            return Response::json(array('message' => 'Registro adicionado com sucesso','objeto' => $mensagem, 'id'=> \Crypt::encrypt($mensagem->cd_processo_mensagem_prm)), 200);
        }else{
            return Response::json(array('message' => 'Erro ao adicionar registro'), 500);
        }

    }

    public function excluir($id)
    {

        $id = \Crypt::decrypt($id);
        
        $mensagem = ProcessoMensagem::findOrFail($id);
        
        if($mensagem->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }

}