<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ContaCorrespondente extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;

    protected $table = 'conta_correspondente_ccr';
    protected $primaryKey = 'cd_conta_correspondente_ccr';
    protected $fillable = [
                            'cd_conta_con',
                            'cd_correspondente_cor',
                            'cd_entidade_ete',
                            'nm_conta_correspondente_ccr',
                            'cd_tipo_pessoa_tpp',
                            'cd_categoria_correspondente_cac',
                            'obs_ccr',
                            'fl_correspondente_escritorio_ccr'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

    public function correspondente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_correspondente_cor');
    }

    public function conta()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_conta_con');
    }

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function categoria()
    {
        return $this->hasOne('App\CategoriaCorrespondente','cd_categoria_correspondente_cac','cd_categoria_correspondente_cac');
    }

    public static function boot(){

        parent::boot();

        static::deleting(function($cliente)
        {
            //limpa todas as cidades de atuação, se houver
            $cliente->entidade->atuacao()->delete();


        });

    }

}