<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPessoa extends Model
{
    protected $table = 'tipo_pessoa_tpp';
    protected $primaryKey = 'cd_tipo_pessoa_tpp';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
