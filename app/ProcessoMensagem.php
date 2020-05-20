<?php

namespace App;

use DB;
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
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'remetente_prm');
    }

    public function entidadeRemetenteColaborador()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'remetente_prm');
    }

    public function entidadeRemetente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'remetente_prm');
    }

    public function entidadeDestinatario()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'destinatario_prm');
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
                    ->where('remetente_prm','<>',$destinatario)
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
