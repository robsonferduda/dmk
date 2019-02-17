<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entidade extends Model
{
    protected $table = 'entidade_ete';
    protected $primaryKey = 'cd_entidade_ete';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
