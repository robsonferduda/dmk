<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Notifications\ConviteNotification as ConviteNotification;
use App\Notifications\FiliacaoNotification as FiliacaoNotification;

class Conta extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;
    use Notifiable;

    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';
    protected $fillable = [
    						'nm_razao_social_con',
                            'cd_tipo_pessoa_tpp',
                            'nm_fantasia_con',
                            'fl_despesa_nao_reembolsavel_con',
                            'fl_envio_enter_con'
    					  ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_conta_con', 'cd_conta_con');
    }

    public function fone()
    {
        return $this->hasOne('App\Fone','cd_conta_con', 'cd_conta_con');
    }

    public function processo()
    {
        return $this->hasMany('App\Processo','cd_conta_con','cd_conta_con');
    }

    public function convite()
    {
        return $this->hasMany('App\ConviteCorrespondente','cd_conta_con','cd_conta_con');
    }

    public function enviarConvite($convite)
    {
        $this->notify(new ConviteNotification($convite));
    }

    public function enviarFiliacao($convite)
    {
        $this->notify(new FiliacaoNotification($convite));
    }
}
