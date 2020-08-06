<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use SoftDeletes;
    
    protected $table = 'estado_est';
    protected $primaryKey = 'cd_estado_est';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_estado_est',
                            'sg_estado_est'
    					  ];

    public $timestamps = true;

    public static function loadEstados()
    {
        $estados = array();

        if (empty(\Cache::tags('dmk_estados','listaEstados')->get('estados')))
        {
            $estados = Estado::orderBy('nm_estado_est')->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags('dmk_estados','listaEstados')->put('estados', $estados, $expiresAt);

        }

        return $estados = \Cache::tags('dmk_estados','listaEstados')->get('estados');

    }

}
