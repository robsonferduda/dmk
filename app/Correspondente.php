<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use App\Traits\VerifyNotification;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Notifications\CadastroCorrespondenteNotification;
use App\Notifications\VinculoCorrespondenteNotification;
use App\Notifications\CorrespondenteNotification;

class Correspondente extends Model implements AuditableContract
{
	use SoftDeletes;
    use Auditable;
    use Notifiable;
    use VerifyNotification;

    protected $table = 'conta_con';
    protected $primaryKey = 'cd_conta_con';
    protected $fillable = [
                            'cd_tipo_pessoa_tpp',
                            'nm_fantasia_con',
                            'nm_razao_social_con'
                          ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function correspondente()
    {
        return $this::whereHas('entidade', function ($query){
            $query->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE);
        })
        ->get();
    }

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_conta_con', 'cd_conta_con');
    }

    public function tipoPessoa()
    {
        return $this->hasOne('App\TipoPessoa','cd_tipo_pessoa_tpp', 'cd_tipo_pessoa_tpp');
    }

    public function contaCorrespondente()
    {
        return $this->hasOne('App\ContaCorrespondente','cd_correspondente_cor', 'cd_conta_con');
    }

    public function notificacaoConfirmacao($correspondente)
    {
        $this->notify(new CorrespondenteNotification($correspondente));
    }

    public function notificacaoCadastro($conta)
    {
        if($this->getFlagEnvio() == 'S')
            $this->notify(new CadastroCorrespondenteNotification($conta));
        else
            return false;
    }

    public function notificacaoFiliacao($conta)
    {
        if($this->getFlagEnvio() == 'S')
            $this->notify(new VinculoCorrespondenteNotification($conta));
        else
            return false;
    }
}