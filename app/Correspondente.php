<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Correspondente extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;

    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';
    protected $fillable = [
                            'cd_tipo_pessoa_tpp',
                            'nm_fantasia_con',
                            'nm_razao_social_con'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function correspondente()
    {
        return $this::whereHas('entidade', function ($query){
            $query->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE);
        })
        ->get();
    }

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_conta_con', 'cd_conta_con');
    }

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

    public function contaCorrespondente()
    {
        return $this->hasOne('App\ContaCorrespondente','cd_correspondente_cor', 'cd_conta_con');
    }
}