<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnexoDespesa extends Model
{

	use SoftDeletes;

    protected $table = 'anexo_despesa_des';
    protected $primaryKey = 'cd_anexo_despesa_des';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_despesa_des',
    						'nm_anexo_despesa_des',
    						'arquivo_anexo_despesa_des'
    					  ];

    public $timestamps = true;

}