<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{

	use SoftDeletes;

    protected $table = 'banco_ban';
    protected $primaryKey = "cd_banco_ban";
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'nm_banco_ban'
    					  ];

    public $timestamps = true;

    public function getNmBancoBanAttribute($value){
        return  str_pad($this->cd_banco_ban,3, '0', STR_PAD_LEFT).' - '.$value;
    }
}
