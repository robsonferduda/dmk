<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnexoProcesso extends Model
{

	use SoftDeletes;

    protected $table = 'anexo_processo_apr';
    protected $primaryKey = 'cd_anexo_processo_apr';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
    						'cd_entidade_ete',
    						'cd_processo_pro',
                            'cd_tipo_anexo_processo_tap',
    						'nm_anexo_processo_apr',
    						'nm_local_anexo_processo_apr'
    					  ];

    public $timestamps = true;

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'cd_entidade_ete');
    }

}