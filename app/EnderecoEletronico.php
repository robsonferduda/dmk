<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnderecoEletronico extends Model
{

	use SoftDeletes;

    protected $table = 'endereco_eletronico_ele';
    protected $primaryKey = 'cd_endereco_eletronico_ele';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
    						'cd_entidade_ete',
    						'cd_tipo_endereco_eletronico_tee',
    						'dc_endereco_eletronico_ede'
    					  ];

    public $timestamps = true;

    public function tipo()
    {
        return $this->hasOne('App\TipoEnderecoEletronico','cd_tipo_endereco_eletronico_tee', 'cd_tipo_endereco_eletronico_tee');
    }
}
