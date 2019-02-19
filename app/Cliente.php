<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
	use SoftDeletes;
	
    protected $table = 'cliente_cli';
    protected $primaryKey = 'cd_cliente_cli';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
