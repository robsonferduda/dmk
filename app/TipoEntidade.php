<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEntidade extends Model
{
	use SoftDeletes;
    protected $table = 'tipo_entidade_tpe';
    protected $primaryKey = 'cd_tipo_entidade_tpe';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
