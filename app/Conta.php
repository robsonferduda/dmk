<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
