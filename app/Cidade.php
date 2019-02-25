<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cidade extends Model
{
    use SoftDeletes;
    
    protected $table = 'cidade_cde';
    protected $primaryKey = 'cd_cidade_cde';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_cidade_cde',
                            'cd_estado_est'
    					  ];

    public $timestamps = true;

    public function estado()
    {
        return $this->hasOne('App\Estado','cd_estado_est', 'cd_estado_est');
    }
}
