<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fone extends Model
{

	use SoftDeletes;

    protected $table = 'fone_fon';
    protected $primaryKey = 'cd_fone_fon';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nu_fone_fon',
    						'cd_conta_con',
    						'cd_contato_cot',
    						'cd_entidade_ete',
    						'cd_tipo_fone_tfo'
    					  ];

    public $timestamps = true;

    public function tipo()
    {
        return $this->hasOne('App\TipoFone','cd_tipo_fone_tfo', 'cd_tipo_fone_tfo');
    }
}
