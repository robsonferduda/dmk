<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conta extends Model
{
	use SoftDeletes;
    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';

    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
