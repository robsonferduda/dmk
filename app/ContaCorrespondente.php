<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ContaCorrespondente extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;

    protected $table = 'conta_correspondente_ccr';
    protected $primaryKey = 'cd_conta_correspondente_ccr';
    protected $fillable = [
                            'cd_conta_con',
                            'cd_correspondente_cor'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function correspondente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_correspondente_cor');
    }

}