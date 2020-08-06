<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CidadeAtuacao extends Model
{

	use SoftDeletes;

    protected $table = 'cidade_atuacao_cat';
    protected $primaryKey = 'cd_cidade_atuacao_cat';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_entidade_ete',
    						'cd_cidade_cde',
                            'fl_origem_cat'
    					  ];

    public $timestamps = true;

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }

}