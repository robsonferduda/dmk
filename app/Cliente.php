<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente_cli';
    protected $primaryKey = 'cd_cliente_cli';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
