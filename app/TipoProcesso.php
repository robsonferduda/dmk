<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoProcesso extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_processo_tpo';
    protected $primaryKey = 'cd_tipo_processo_tpo';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_processo_tpo',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
