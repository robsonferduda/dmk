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

    public static function loadCategorias()
    {

        $categorias = array();

        if (empty(\Cache::tags('dmk_categorias','listaCategorias')->get('categorias')))
        {
            $categorias = CategoriaCorrespondente::where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->orderBy('dc_categoria_correspondente_cac')->get();
            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags('dmk_categorias','listaCategorias')->put('categorias', $categorias, $expiresAt);

        }

        return $categorias = \Cache::tags('dmk_categorias','listaCategorias')->get('categorias');
    }
}
