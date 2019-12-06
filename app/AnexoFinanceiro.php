<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnexoFinanceiro extends Model
{

	use SoftDeletes;

    protected $table = 'anexo_financeiro_afn';
    protected $primaryKey = 'cd_anexo_financeiro_afn';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',    						    					
    						'nm_anexo_financeiro_afn',
    						'nm_local_anexo_financeiro_afn',
                            'cd_tipo_financeiro_tfn',
                            'cd_baixa_honorario_bho'
    					  ];

    public $timestamps = true;

    public function tipoFinanceiro()
    {
        return $this->hasOne('App\TipoFinanceiro','cd_tipo_financeiro_tfn', 'cd_tipo_financeiro_tfn');
    }

}