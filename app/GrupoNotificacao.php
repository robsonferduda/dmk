<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoNotificacao extends Model
{
    use SoftDeletes;
    
    protected $table = 'grupo_notificacao_grn';
    protected $primaryKey = 'cd_grupo_notificacao_grn';
    protected $dates = ['deleted_at'];
    protected $fillable = [];

    public $timestamps = true;

    public function tipoProcesso()
    {
        return $this->hasOne('App\TipoProcesso','cd_tipo_processo_tpo', 'cd_tipo_processo_tpo');
    }

    public function emails()
    {
        return $this->hasMany('App\EmailNotificacao','cd_grupo_notificacao_grn', 'cd_grupo_notificacao_grn');
    }
}