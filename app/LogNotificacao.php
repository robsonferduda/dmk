<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogNotificacao extends Model
{

	use SoftDeletes;

    protected $table = 'log_notificacao';
    protected $primaryKey = 'cd_log_notificacao';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_remetente',
    						'cd_destinatario',
                            'cd_processo',
                            'nu_processo',
                            'origem',
                            'email_destinatario',
                            'tipo_notificacao'
    					  ];

    public $timestamps = true;
}
