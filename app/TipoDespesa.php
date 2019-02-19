<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDespesa extends Model
{
    use SoftDeletes;
    
    protected $table = 'tipo_despesa_tds';
    protected $primaryKey = 'cd_tipo_despesa_tds';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_despesa_tds',
    						'cd_conta_con',
    						'fl_reembolso_tds'
    						
    					  ];

    public $timestamps = true;
}
