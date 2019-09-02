<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReembolsoTipoDespesa extends Model
{

	use SoftDeletes;

    protected $table = 'reembolso_tipo_despesa_rtd';
    protected $primaryKey = 'cd_reembolso_tipo_servico_rst';
    
    protected $fillable = [
    						'cd_entidade_ete',
    						'cd_conta_con',
    						'cd_tipo_despesa_tds'
    					  ];

    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function tipoDespesa()
    {
        return $this->hasOne('App\TipoDespesa','cd_tipo_despesa_tds', 'cd_tipo_despesa_tds');
    }

    public function cliente()
    {
        return $this->hasOne('App\Cliente','cd_entidade_ete', 'cd_entidade_ete');
    }

}