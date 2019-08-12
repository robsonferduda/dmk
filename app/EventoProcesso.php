<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoProcesso extends Model
{
    use SoftDeletes;
    
    protected $table = 'evento_processo_epr';
    protected $primaryKey = 'cd_evento_processo_epr';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
                            'id_evento_calendario_google_epr',
                            'cd_processo_pro'
    					  ];

    public $timestamps = true;

}
