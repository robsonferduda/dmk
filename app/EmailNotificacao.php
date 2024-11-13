<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailNotificacao extends Model
{
    use SoftDeletes;
    
    protected $table = 'email_grupo_notificacao_egn';
    protected $primaryKey = 'cd_email_grupo_notificacao_egn';
    protected $dates = ['deleted_at'];
    protected $fillable = ['cd_grupo_notificacao_grn','ds_email_egn'];

    public $timestamps = true;
}