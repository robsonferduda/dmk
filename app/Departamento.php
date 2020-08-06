<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{

	use SoftDeletes;

    protected $table = 'departamento_dep';
    protected $primaryKey = 'cd_departamento_dep';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_departamento_dep',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
