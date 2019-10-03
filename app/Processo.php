<?php

namespace App;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Notifications\CorrespondenteProcessoNotification;
use App\Notifications\ContaProcessoNotification;
use App\Notifications\MensagemProcessoNotification;
use App\Notifications\ClienteProcessoNotification;
use App\Notifications\EnvioDocumentosProcessoNotification;


class Processo extends Model implements AuditableContract
{

	use SoftDeletes;
    use Auditable;
    use Notifiable;

    protected $table = 'processo_pro';
    protected $primaryKey = 'cd_processo_pro';
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'cd_cidade_cde',
    						'cd_cliente_cli',
    						'cd_conta_con',
    						'cd_contato_cot',
    						'cd_correspondente_cor',
    						'cd_entidade_ete',
    						'cd_tipo_processo_tpo',
    						'cd_vara_var',
    						'dc_observacao_pro',
    						'dt_solicitacao_pro',
    						'dt_prazo_fatal_pro',
    						'hr_audiencia_pro',
    						'nm_advogado_pro',
    						'nm_autor_pro',
    						'nm_preposto_pro',
    						'nm_reu_pro',
    						'nu_processo_pro',
                            'nu_acompanhamento_pro',
                            'cd_status_processo_stp',
                            'fl_envio_anexos_pro',
                            'fl_recebimento_anexos_pro',
                            'cd_responsavel_pro'
    					  ];

    public $timestamps = true;

    public function responsavel()
    {
        return $this->hasOne('App\User','id', 'cd_responsavel_pro');
    }

    public function anexos()
    {
        return $this->hasMany('App\AnexoProcesso','cd_processo_pro', 'cd_processo_pro');
    }

    public function cliente()
    {
        return $this->hasOne('App\Cliente','cd_cliente_cli', 'cd_cliente_cli');
    }

    public function cidade()
    {
        return $this->hasOne('App\Cidade','cd_cidade_cde', 'cd_cidade_cde');
    }

    public function advogadoSolicitante()
    {
        return $this->hasOne('App\Contato','cd_contato_cot', 'cd_contato_cot');
    }

    public function tipoProcesso()
    {
        return $this->hasOne('App\TipoProcesso','cd_tipo_processo_tpo', 'cd_tipo_processo_tpo');
    }

    public function vara()
    {
        return $this->hasOne('App\Vara','cd_vara_var', 'cd_vara_var');
    }

    public function tiposDespesa()
    {
        return $this->belongsToMany('App\TipoDespesa','processo_despesa_pde','cd_processo_pro','cd_tipo_despesa_tds')->withTimestamps()->withPivot('vl_processo_despesa_pde','fl_despesa_reembolsavel_pde','cd_tipo_entidade_tpe');
    }

    public function processoDespesa()
    {
        return $this->hasMany('App\ProcessoDespesa','cd_processo_pro','cd_processo_pro');
    }

    public function correspondente()
    {
        return $this->hasOne('App\Correspondente','cd_conta_con', 'cd_correspondente_cor');
    }

    public function conta()
    {
        return $this->hasOne('App\Conta','cd_conta_con', 'cd_conta_con');
    }

    public function honorario()
    {
        return $this->hasOne('App\ProcessoTaxaHonorario','cd_processo_pro', 'cd_processo_pro');
    }

    public function status()
    {
        return $this->hasOne('App\StatusProcesso','cd_status_processo_stp', 'cd_status_processo_stp');
    }

    public function notificarEnvioDocumentos($processo)
    {
        $this->notify(new EnvioDocumentosProcessoNotification($processo));
    }

    public function notificarCorrespondente($processo)
    {
        $this->notify(new CorrespondenteProcessoNotification($processo));
    }

    public function notificarConta($processo)
    {
        $this->notify(new ContaProcessoNotification($processo));
    }

    public function notificarCliente($processo)
    {
        $this->notify(new ClienteProcessoNotification($processo));
    }

    public function notificarNovaMensagem($processo)
    {
        $this->notify(new MensagemProcessoNotification($processo));
    }

    public static function boot(){

        parent::boot();

        static::deleting(function($processo)
        {
            if($processo->honorario()->first()) $processo->honorario()->first()->delete();
        });

    }
}
