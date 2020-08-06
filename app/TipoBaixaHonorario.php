<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoBaixaHonorario extends Model
{
    protected $table = 'tipo_baixa_honorario_bho';
    protected $primaryKey = 'cd_tipo_baixa_honorario_bho';
    
    protected $fillable = [
    						'nm_tipo_baixa_honorario_bho'
    					  ];

}
