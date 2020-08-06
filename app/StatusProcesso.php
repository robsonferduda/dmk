<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusProcesso extends Model
{

    protected $table = 'status_processo_stp';
    protected $primaryKey = 'cd_status_processo_stp';
    protected $fillable = [
    						'nm_status_processo_conta_stp'
    					  ];

    protected static function visivelCorrespondente($status)
    {
    	$status = StatusProcesso::where('cd_status_processo_stp', $status)->first();

    	if($status->fl_visivel_correspondente_stp == 'S')
    		return true;
    	else
    		return false;
    }
}