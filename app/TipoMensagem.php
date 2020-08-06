<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoMensagem extends Model
{
		
    protected $table = 'tipo_mensagem_tim';
    protected $primaryKey = 'cd_tipo_mensagem_tim';

}