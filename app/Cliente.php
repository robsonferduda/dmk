<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
	use SoftDeletes;
	
    protected $table = 'cliente_cli';
    protected $primaryKey = 'cd_cliente_cli';

    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

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
}
