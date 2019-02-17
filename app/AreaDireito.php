<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaDireito extends Model
{
    protected $table = 'area_direito_ado';
    protected $primaryKey = 'cd_area_direito_ado';
    protected $dates = ['deleted_at'];
    protected $fillable = ['dc_area_direito_ado'];

    public $timestamps = true;
}
