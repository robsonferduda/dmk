<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Identificacao extends Model
{

	use SoftDeletes;

    protected $table = 'identificacao_ide';
    protected $primaryKey = 'cd_identificacao_ide';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_entidade_ete',
    						'cd_conta_con',
    						'cd_tipo_identificacao_tpi',
    						'nu_identificacao_ide',
    					  ];

    public $timestamps = true;
}
