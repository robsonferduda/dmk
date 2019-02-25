<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoCidadeRelacionamento extends Model
{
    use SoftDeletes;
    
    protected $table = 'grupo_cidade_relacionamento_gcr';
    protected $primaryKey = 'cd_grupo_cidade_relacionamento_gcr';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_cidade_cde',
                            'cd_grupo_cidade_grc',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }
}
