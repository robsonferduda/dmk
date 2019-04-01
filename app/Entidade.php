<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends Model
{
	use SoftDeletes;
	
    protected $table = 'entidade_ete';
    protected $primaryKey = 'cd_entidade_ete';
	protected $fillable = [
	    					'cd_conta_con',
                            'cd_tipo_entidade_tpe'
    					  ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function identificacao()
    {
        return $this->hasOne('App\Identificacao','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function cpf()
    {
        return $this->hasOne('App\Identificacao','cd_entidade_ete', 'cd_entidade_ete')->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF);
    }

    public function oab()
    {
        return $this->hasOne('App\Identificacao','cd_entidade_ete', 'cd_entidade_ete')->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::OAB);
    }

    public function rg()
    {
        return $this->hasOne('App\Identificacao','cd_entidade_ete', 'cd_entidade_ete')->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::RG);
    }

    public function fone()
    {
        return $this->hasOne('App\Fone','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function endereco()
    {
        return $this->hasOne('App\Endereco','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function banco()
    {
        return $this->hasOne('App\RegistroBancario','cd_entidade_ete', 'cd_entidade_ete');
    }

}
