<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoPessoa extends Model
{
	use SoftDeletes;
	
    protected $table = 'tipo_pessoa_tpp';
    protected $primaryKey = 'cd_tipo_pessoa_tpp';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
