<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoServico extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_servico_tse';
    protected $primaryKey = 'cd_tipo_servico_tse';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_servico_tse',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
