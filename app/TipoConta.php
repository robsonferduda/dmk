<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoConta extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_conta_banco_tcb';
    protected $primaryKey = 'cd_tipo_conta_tcb';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_conta_tcb'
    					  ];

    public $timestamps = true;
}
