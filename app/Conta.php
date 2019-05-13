<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conta extends Model
{
	use SoftDeletes;
    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';
    protected $fillable = [
    						'nm_razao_social_con'
    					  ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_conta_con', 'cd_conta_con');
    }

    public function fone()
    {
        return $this->hasOne('App\Fone','cd_conta_con', 'cd_conta_con');
    }

    public function processo()
    {
        return $this->hasMany('App\Processo','cd_conta_con','cd_conta_con');
    }
}
