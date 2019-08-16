<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaCorrespondente extends Model
{
    use SoftDeletes;
    
    protected $table = 'categoria_correspondente_cac';
    protected $primaryKey = 'cd_categoria_correspondente_cac';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
    						'dc_categoria_correspondente_cac',
    						'color_cac'    						    					
    					  ];

    public $timestamps = true;
}
