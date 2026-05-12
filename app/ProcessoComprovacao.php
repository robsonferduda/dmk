<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [TIMEMARK]
 * Comprovação de diligência (foto com data/hora, GPS e logo)
 * enviada pelo correspondente para um processo.
 */
class ProcessoComprovacao extends Model
{
    use SoftDeletes;

    protected $table      = 'processo_comprovacao_pcm';
    protected $primaryKey = 'cd_processo_comprovacao_pcm';
    protected $dates      = ['deleted_at', 'dt_captura_pcm'];

    protected $fillable = [
        'cd_processo_pro',
        'cd_conta_con',
        'cd_correspondente_cor',
        'cd_entidade_ete',
        'cd_user_upload',
        'arquivo_original_pcm',
        'arquivo_marcado_pcm',
        'mime_pcm',
        'tamanho_bytes_pcm',
        'dt_captura_pcm',
        'nu_latitude_pcm',
        'nu_longitude_pcm',
        'nu_precisao_metros_pcm',
        'ds_endereco_pcm',
        'nu_distancia_metros_pcm',
        'ds_observacao_pcm',
        'hash_sha256_pcm',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'cd_processo_pro', 'cd_processo_pro');
    }
}
