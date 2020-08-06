<?php

namespace App\Traits;

use App\Conta;
use Illuminate\Support\Facades\Session;

trait VerifyNotification
{

	public function __construct()
    {
        
    }

    public function getFlagEnvio()
    {
    	$conta = Conta::where('cd_conta_con',\Session::get('SESSION_CD_CONTA'))->first();
        return $conta->fl_envio_notificacao_con;
    }

    public function getFlagEnvioCorrespondente()
    {
    	$conta = Conta::where('cd_conta_con',\Session::get('SESSION_CD_CONTA'))->first();
        return $conta->fl_notificacao_correspondente_con;
    }

}