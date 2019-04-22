<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaCorrespondente extends Model
{
	use SoftDeletes;
    protected $table = 'conta_correspondente_ccr';
    protected $primaryKey = 'cd_conta_correspondente_ccr';
    protected $fillable = [
                            'cd_conta_con',
                            'cd_correspondente_cor'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function correspondente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_correspondente_cor');
    }

}