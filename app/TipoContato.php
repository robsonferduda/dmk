<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoContato extends Model
{

	use SoftDeletes;

    protected $table = 'tipo_contato_tct';
    protected $primaryKey = 'cd_tipo_contato_tct';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_tipo_contato_tct',
    						'cd_conta_con'
    					  ];

    public $timestamps = true;
}
