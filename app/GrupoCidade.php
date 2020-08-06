<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoCidade extends Model
{
    use SoftDeletes;
    
    protected $table = 'grupo_cidade_grc';
    protected $primaryKey = 'cd_grupo_cidade_grc';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_grupo_cidade_grc',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;

    public function grupoCidadeRelacionamentos()
    {
        return $this->hasMany('App\GrupoCidadeRelacionamento','cd_grupo_cidade_grc', 'cd_grupo_cidade_grc');
    }

    public function cidades()
    {
        return $this->belongsToMany('App\Cidade','grupo_cidade_relacionamento_gcr','cd_grupo_cidade_grc','cd_cidade_cde')->withTimestamps();
    }
}
