<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaDespesa extends Model
{
    use SoftDeletes;
    
    protected $table = 'categoria_despesa_cad';
    protected $primaryKey = 'cd_categoria_despesa_cad';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_categoria_despesa_cad',
    						'cd_conta_con'    						    					
    					  ];

    public $timestamps = true;
}
