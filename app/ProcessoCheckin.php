<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [CHECK-IN]
 * Check-in do correspondente ao chegar no fórum para resolver um processo.
 * Diferente do Timemark (\App\ProcessoComprovacao), NÃO armazena foto.
 * Apenas data/hora + GPS (lat/lng/precisão).
 */
class ProcessoCheckin extends Model
{
    use SoftDeletes;

    protected $table      = 'processo_checkin_pck';
    protected $primaryKey = 'cd_processo_checkin_pck';
    protected $dates      = ['deleted_at', 'dt_checkin_pck'];

    protected $fillable = [
        'cd_processo_pro',
        'cd_conta_con',
        'cd_correspondente_cor',
        'cd_entidade_ete',
        'cd_user_checkin_pck',
        'dt_checkin_pck',
        'nu_latitude_pck',
        'nu_longitude_pck',
        'nu_precisao_metros_pck',
        'ds_endereco_pck',
        'nu_distancia_metros_pck',
        'ds_observacao_pck',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'cd_processo_pro', 'cd_processo_pro');
    }
}
