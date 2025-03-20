<?php

namespace App;

use DB;
use URL;
use App\Enums\TipoMensagem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessoMensagem extends Model
{
	use SoftDeletes;
	
    protected $table = 'processo_mensagem_prm';
    protected $primaryKey = 'cd_processo_mensagem_prm';
    protected $fillable = [
    						'cd_processo_pro',
    						'remetente_prm',
    						'destinatario_prm',
                            'texto_mensagem_prm',
                            'cd_tipo_mensagem_tim',
                            'fl_leitura_prm' 						
    					  ];

    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function processo()
    {
        return $this->hasOne('App\Processo','cd_processo_pro', 'cd_processo_pro');
    }

    public function entidadeInterna()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'remetente_prm')->withTrashed();
    }

    public function entidadeRemetenteColaborador()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'remetente_prm');
    }

    public function entidadeRemetente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'remetente_prm')->withTrashed();
    }

    public function entidadeDestinatario()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'destinatario_prm')->withTrashed();
    }

    public function getMensagensPendentes($conta)
    {
        $dados = array();
        $mensagens = $this->with('processo')
                    ->where('destinatario_prm',$conta)
                    ->where('remetente_prm','<>',$conta)
                    ->whereNull('fl_leitura_prm')
                    ->withTrashed()
                    ->orderBy('created_at', 'DESC')->get();

        foreach($mensagens as $msg){

            if($msg->cd_tipo_mensagem_tim == \App\Enums\TipoMensagem::EXTERNA){

                $remetente = ($msg->entidadeRemetente) ? $msg->entidadeRemetente->nm_razao_social_con : 'Desconhecido';
                $img_user = ($msg->entidadeRemetente) ? public_path().'/img/users/ent'.$msg->entidadeRemetente->entidade->cd_entidade_ete.'.png' : null;

                if($img_user and file_exists($img_user)){
                    $avatar = URL::to('/').'/img/users/ent'.$msg->entidadeRemetente->entidade->cd_entidade_ete.'.png';
                }else{
                    $avatar = URL::to('/').'/img/users/user.png';
                }

            }else{

                if($msg->entidadeInterna and $msg->entidadeInterna->usuario){
                    $remetente = $msg->entidadeInterna->usuario->name;
                    if(file_exists(public_path().'/img/users/ent'.$msg->entidadeInterna->cd_entidade_ete.'.png')){
                        $avatar = URL::to('/').'/img/users/ent'.$msg->entidadeInterna->cd_entidade_ete.'.png';
                    }else{
                        $avatar = URL::to('/').'/img/users/user.png';
                    }
                }else{
                    $remetente = 'Desconhecido';
                    $avatar = URL::to('/').'/img/users/user.png'; 
                }
            }

            $dados[] = array('nu_processo' => ($msg->processo) ? $msg->processo->nu_processo_pro : 'Processo Excluído',
                             'token' => \Crypt::encrypt($msg->cd_processo_pro),
                             'remetente' => $remetente,
                             'avatar' => $avatar,
                             'url' => URL::to('/').'/processos/acompanhamento/'.\Crypt::encrypt($msg->cd_processo_pro),
                             'data' => date('d/m/Y H:i:s', strtotime($msg->created_at)));
        }

        return $dados;
    }

    public function getMensagensPendentesRemetente($conta)
    {
        return $this->where('destinatario_prm',$conta)
                    ->where('fl_leitura_prm','<>','S')
                    ->where('remetente_prm','<>',$conta)
                    ->orderBy('created_at', 'DESC')->get();
    }

    public function getMensagensPendentesDestinatario($destinatario)
    {
        return $this->where('destinatario_prm',$destinatario)
                    ->where('fl_leitura_prm','<>','S')
                    ->where('destinatario_prm','<>',$destinatario)
                    ->orderBy('created_at', 'DESC')->get();
    }

    public function atualizaMensagensLidas($id,$conta)
    {
        return DB::table('processo_mensagem_prm')
            ->where('cd_processo_pro', $id)
            ->where('destinatario_prm', $conta)
            ->update(['fl_leitura_prm' => "S"]);
    }
}
