<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{

	use SoftDeletes;

    protected $table = 'cargo_car';
    protected $primaryKey = 'cd_cargo_car';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_cargo_car',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
