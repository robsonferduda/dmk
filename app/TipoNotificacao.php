<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoNotificacao extends Model
{
	use SoftDeletes;

    protected $table = 'tipo_notificacao_tin';
    protected $primaryKey = 'cd_tipo_notificacao_tin';
    protected $dates = ['deleted_at'];
    protected $fillable = [''];

    public $timestamps = true;

}