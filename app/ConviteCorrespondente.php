<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ConviteCorrespondente extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;

    protected $table = 'convite_correspondente_coc';
    protected $primaryKey = 'cd_convite_correspondente_coc';
    protected $fillable = [
                            'cd_conta_con',
                            'token_coc',
                            'email_coc'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function correspondente()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_convite_correspondente_coc');
    }

    public function conta()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_conta_con');
    }

}