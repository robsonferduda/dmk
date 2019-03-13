<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroBancario extends Model
{

	use SoftDeletes;

    protected $table = 'dados_bancarios_dba';
    protected $primaryKey = 'cd_dados_bancarios_dba';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_titular_dba',
    						'nu_cpf_cnpj_dba',
    						'nu_agencia_dba',
    						'nu_conta_dba',
    						'cd_banco_ban',
    						'cd_tipo_conta_tcb',
    						'cd_entidade_ete',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;

    public function banco()
    {
        return $this->hasOne('App\Banco','cd_banco_ban', 'cd_banco_ban');
    }

    public function tipoConta()
    {
        return $this->hasOne('App\TipoConta','cd_tipo_conta_tcb', 'cd_tipo_conta_tcb');
    }
}