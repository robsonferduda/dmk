<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoEntidade extends Model
{
    protected $table = 'tipo_entidade_tpe';
    protected $primaryKey = 'cd_tipo_entidade_tpe';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
