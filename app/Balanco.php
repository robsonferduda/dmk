<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Balanco extends Model
{

	

    protected $table = 'balanco_bal';
    protected $primaryKey = "cd_conta_con";
    protected $fillable = [
    						'categoria',
                            'cod_tipo_despesa',
                            'date',
                            'valor',
                            'despesa',
                            'nota',
                            'valor_total'
    					  ];

}
