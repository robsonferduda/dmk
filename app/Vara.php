<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vara extends Model
{

	use SoftDeletes;

    protected $table = 'vara_var';
    protected $primaryKey = 'cd_vara_var';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_vara_var',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
