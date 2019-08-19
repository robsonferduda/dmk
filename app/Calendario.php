<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendario extends Model
{
    use SoftDeletes;
    
    protected $table = 'calendario_cal';
    protected $primaryKey = 'cd_calendario_cal';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_conta_con',
                            'id_calendario_google_cal'
    					  ];

    public $timestamps = true;

}
