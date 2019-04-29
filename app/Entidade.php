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

    public function cnpj()
    {
        return $this->hasOne('App\Identificacao','cd_entidade_ete', 'cd_entidade_ete')->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CNPJ);
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

    public function enderecoEletronico()
    {
        return $this->hasOne('App\EnderecoEletronico','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function banco()
    {
        return $this->hasOne('App\RegistroBancario','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function reembolso()
    {
        return $this->hasOne('App\ReembolsoTipoDespesa','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function usuario()
    {
        return $this->hasOne('App\User','cd_entidade_ete', 'cd_entidade_ete');
    }

     public function atuacao()
    {
        return $this->hasOne('App\CidadeAtuacao','cd_entidade_ete', 'cd_entidade_ete');
    }

    public static function boot(){

        parent::boot();

        static::deleting(function($entidade)
        {
            $entidade->identificacao()->delete();
            $entidade->fone()->delete();
            $entidade->endereco()->delete();
            $entidade->banco()->delete();
        });

    }
}
