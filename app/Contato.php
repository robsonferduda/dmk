<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contato extends Model
{
	use SoftDeletes;
    protected $table = 'contato_cot';
    protected $primaryKey = 'cd_contato_cot';
    protected $fillable = [
    						'nm_contato_cot',
                            'cd_conta_con',
                            'cd_entidade_ete',
                            'cd_entidade_contato_ete',
                            'cd_tipo_contato_tct',
                            'dc_observacao_cot'
    					  ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
  
}
