<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaDireito extends Model
{
	use SoftDeletes;
	
    protected $table = 'area_direito_ado';
    protected $primaryKey = 'cd_area_direito_ado';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'dc_area_direito_ado',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
