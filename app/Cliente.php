<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
	use SoftDeletes;
	
    protected $table = 'cliente_cli';
    protected $primaryKey = 'cd_cliente_cli';

     protected $fillable = [
        'nm_razao_social_cli',
        'cd_conta_con',
        'cd_entidade_ete',
        'cd_tipo_pessoa_tpp'
    ];

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

    public static function boot(){

        parent::boot();

        static::deleting(function($cliente)
        {
            foreach($cliente->entidade()->first()->identificacao()->get() as $identificacao){
                $identificacao->delete();
            }            

            $cliente->entidade()->delete();
        });

    }
}
