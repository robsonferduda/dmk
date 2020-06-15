<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use App\Traits\VerifyNotification;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Notifications\CorrespondenteCadastroContaNotification;
use App\Notifications\CorrespondenteFiliacaoNotification;
use App\Notifications\CorrespondenteNotification;
use App\Notifications\CorrespondenteSenhaNotification;

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
        return $this->hasOne('App\ContaCorrespondente','cd_correspondente_cor', 'cd_conta_con')->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'));
    }

    public function contaCorrespondenteTrashedToo()
    {
        return $this->hasOne('App\ContaCorrespondente','cd_correspondente_cor', 'cd_conta_con')->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))->withTrashed();
    }

    public function notificacaoConfirmacao($correspondente)
    {
        $this->notify(new CorrespondenteNotification($correspondente));
    }

    //Mensagem enviado no momento do cadastro de um novo correspondente, ainda sem conta, por um escritório interessado
    public function notificarCadastroConta($conta)
    {
        if($this->getFlagEnvio() == 'S'){
            $this->notify(new CorrespondenteCadastroContaNotification($conta));
            return true;
        }else{
            return false;
        }
    }

    public function notificarFiliacaoConta($conta)
    {
        if($this->getFlagEnvioCorrespondente() == 'S'){
            $this->notify(new CorrespondenteFiliacaoNotification($conta));
            return true;
        }else{
            return false;
        }
    }

    public function notificarAlteracaoSenha($conta)
    {
        if($this->getFlagEnvioCorrespondente() == 'S'){
            $this->notify(new CorrespondenteSenhaNotification($conta));
            return true;
        }else{
            return false;
        }
    }

}