<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoCivil extends Model
{

	use SoftDeletes;

    protected $table = 'estado_civil_esc';
    protected $primaryKey = 'cd_estado_civil_esc';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_estado_civil_esc'
    					  ];

    public $timestamps = true;
}
