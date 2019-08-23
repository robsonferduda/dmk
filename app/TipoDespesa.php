<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDespesa extends Model
{
    use SoftDeletes;
    
    protected $table = 'tipo_despesa_tds';
    protected $primaryKey = 'cd_tipo_despesa_tds';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_despesa_tds',
    						'cd_conta_con',
    						'fl_reembolso_tds',
                            'cd_categoria_despesa_cad'
    						
    					  ];

    public $timestamps = true;

    public function categoriaDespesa()
    {
        return $this->hasOne('App\CategoriaDespesa','cd_categoria_despesa_cad', 'cd_categoria_despesa_cad');
    }

    public function reembolsoTipoDespesa()
    {
        return $this->hasMany('App\ReembolsoTipoDespesa','cd_tipo_despesa_tds', 'cd_tipo_despesa_tds');
    }
}
