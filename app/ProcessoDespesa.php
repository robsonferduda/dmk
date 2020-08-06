<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ProcessoDespesa extends Model implements AuditableContract
{

	use SoftDeletes;
    use Auditable;

    protected $table = 'processo_despesa_pde';
    protected $primaryKey = 'cd_processo_despesa_pde';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
    					    'cd_processo_pro',
                            'cd_tipo_despesa_tds',
                            'cd_tipo_entidade_tpe',
                            'fl_despesa_reembolsavel_pde',
                            'vl_processo_despesa_pde'
    					  ];

    public $timestamps = true;

}
