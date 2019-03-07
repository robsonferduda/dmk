<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoFone extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_fone_tfo';
    protected $primaryKey = 'cd_tipo_fone_tfo';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'dc_tipo_fone_tfo' 	
    					  ];

    public $timestamps = true;
}
