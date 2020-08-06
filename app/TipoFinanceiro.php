<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class TipoFinanceiro extends Model
{
    
    protected $table = 'tipo_financeiro_tfn';
    protected $primaryKey = 'cd_tipo_financeiro_tfn';
    protected $fillable = [
    						'nm_tipo_financeiro_tfn'                                       
    					  ];

}
