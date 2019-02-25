<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use SoftDeletes;
    
    protected $table = 'estado_est';
    protected $primaryKey = 'cd_estado_est';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_estado_est',
                            'sg_estado_est'
    					  ];

    public $timestamps = true;

}
