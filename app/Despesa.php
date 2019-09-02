<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Despesa extends Model
{

	use SoftDeletes;

    protected $table = 'despesa_des';
    protected $primaryKey = 'cd_despesa_des';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
    						'cd_tipo_despesa_tds',
    						'dc_descricao_des',
    						'dt_pagamento_des',
    						'dt_vencimento_des',
    						'vl_valor_des',
    						'obs_des',
                            'anexo_des'
    					  ];

    public $timestamps = true;

    public function tipo()
    {
        return $this->hasOne('App\TipoDespesa','cd_tipo_despesa_tds', 'cd_tipo_despesa_tds');
    }
}