<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Endereco extends Model
{

	use SoftDeletes;

    protected $table = 'endereco_ede';
    protected $primaryKey = 'cd_endereco_ede';
    protected $nu_cep_ede;
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'dc_logradouro_ede',
    						'nu_cep_ede',
    						'nu_numero_ede',
    						'dc_complemento_ede',
    						'cd_cidade_cde',
    						'cd_entidade_ete',
    						'cd_conta_con',
    						'nm_bairro_ede'
    					  ];

    public $timestamps = true;

    public function getNuCepEdeAttribute($value){
        return  str_pad($value,8, '0', STR_PAD_LEFT);
    }

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }
}
