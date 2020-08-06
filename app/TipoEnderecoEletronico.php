<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEnderecoEletronico extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_endereco_eletronico_tee';
    protected $primaryKey = 'cd_tipo_endereco_eletronico_tee';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'dc_tipo_endereco_eletronico_tee'
    					  ];

    public $timestamps = true;
}