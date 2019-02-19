<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends Model
{
	use SoftDeletes;
	
    protected $table = 'entidade_ete';
    protected $primaryKey = 'cd_entidade_ete';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
