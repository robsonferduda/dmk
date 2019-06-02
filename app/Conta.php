<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Conta extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;

    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';
    protected $fillable = [
    						'nm_razao_social_con',
                            'cd_tipo_pessoa_tpp',
                            'nm_fantasia_con',
                            'fl_despesa_nao_reembolsavel_con'
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
