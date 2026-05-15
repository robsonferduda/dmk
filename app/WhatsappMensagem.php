<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [CHATPRO / WHATSAPP]
 * Histórico unificado de mensagens (inbound + outbound + acks).
 *
 * tp_direcao_wmm:
 *   I = inbound  (recebida via webhook do ChatPro)
 *   O = outbound (enviada por nós via API)
 *   A = ack / atualização de status (delivered, read, failed)
 */
class WhatsappMensagem extends Model
{
    use SoftDeletes;

    protected $table      = 'whatsapp_mensagem_wmm';
    protected $primaryKey = 'cd_whatsapp_mensagem_wmm';

    public $timestamps = true;
    protected $dates   = ['deleted_at', 'dt_evento_wmm'];

    protected $fillable = [
        'cd_conta_con',
        'tp_direcao_wmm',
        'nu_telefone_origem_wmm',
        'nu_telefone_destino_wmm',
        'ds_mensagem_wmm',
        'ds_tipo_wmm',
        'ds_message_id_wmm',
        'ds_status_wmm',
        'ds_payload_raw_wmm',
        'cd_processo_pro',
        'cd_correspondente_cor',
        'cd_processo_checkin_pck',
        'dt_evento_wmm',
    ];

    // O Postgres devolve JSONB como string; expomos como array.
    protected $casts = [
        'ds_payload_raw_wmm' => 'array',
    ];

    public function processo()
    {
        return $this->belongsTo(\App\Processo::class, 'cd_processo_pro', 'cd_processo_pro');
    }

    public function checkin()
    {
        return $this->belongsTo(\App\ProcessoCheckin::class, 'cd_processo_checkin_pck', 'cd_processo_checkin_pck');
    }
}
