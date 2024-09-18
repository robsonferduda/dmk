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
    protected $fillable = [];

    public $timestamps = true;
}