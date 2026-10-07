<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutentiqueEvento extends Model
{
    protected $table = 'autentique_evento_aev';
    protected $primaryKey = 'cd_autentique_evento_aev';
    protected $fillable = [
        'dc_evento_id_aev',
        'dc_tipo_aev',
        'cd_documento_autentique_aev',
        'js_payload_aev',
        'dt_processado_aev',
        'dc_erro_aev',
    ];
    protected $dates = ['dt_processado_aev'];

    public function payload(): array
    {
        return json_decode($this->js_payload_aev, true) ?: [];
    }
}
