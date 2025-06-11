<?php

namespace App;
use DB;
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
use App\Notifications\ProcessoRequisitarDadosNotification;
use App\Notifications\ProcessoAtualizacaoDadosNotification;
use App\Notifications\ProcessoAvisoLeituraNotification;
use App\Notifications\ProcessoCorrespondenteFinalizarNotification;

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
                            'cd_responsavel_pro',
                            'fl_documento_representacao_pro',
                            'dc_observacao_processo_pro',
                            'ds_link_dados_pro',
                            'ds_link_audiencia_pro',
                            'fl_dados_enviados_pro',
                            'txt_finalizacao_pro',
                            'dt_finalizacao_pro',
                            'cd_user_cadastro_pro',
                            'cd_user_finalizacao_pro',
                            'nu_lote'
    					  ];

    public $timestamps = true;

    public function responsavel()
    {
        return $this->hasOne('App\User','id', 'cd_responsavel_pro');
    }

    public function usuarioCadastro()
    {
        return $this->hasOne('App\User','id', 'cd_user_cadastro_pro');
    }

    public function usuario()
    {
        return $this->hasOne('App\User','id', 'cd_user_finalizacao_pro');
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

    public function notificarLeituraDocumentos($processo)
    {
        $this->notify(new ProcessoAvisoLeituraNotification($processo));
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

    public function notificarRequisitarDados($processo)
    {
        $this->notify(new ProcessoRequisitarDadosNotification($processo));
    }

    public function notificarAtualizacaoDados($processo)
    {
        $this->notify(new ProcessoAtualizacaoDadosNotification($processo));
    }

    public function notificarFinalizacaoCorrespondente($processo)
    {
        $this->notify(new ProcessoCorrespondenteFinalizarNotification($processo));
    }

    public function getAssuntoNotification()
    {
        $assunto = "";

        $data = ($this->attributes['dt_prazo_fatal_pro']) ? date('d/m/Y', strtotime($this->attributes['dt_prazo_fatal_pro'])) : 'Não informada';
        $hora = ($this->attributes['hr_audiencia_pro']) ? date('H:i', strtotime($this->attributes['hr_audiencia_pro'])) : 'Não informada';
        $parte_autora = ($this->attributes['nm_autor_pro']) ? $this->attributes['nm_autor_pro'] : 'Não informada';
        $parte_re = ($this->attributes['nm_reu_pro']) ? $this->attributes['nm_reu_pro'] : 'Não informada';
        $num_processo = $this->attributes['nu_processo_pro'];

        $tipo_servico = $this->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse;

        $assunto = $data.' - '.$hora.' - '.$tipo_servico.' - Autor: '.$parte_autora.' - Réu: '.$parte_re.' - Processo '.$num_processo;

        return $assunto;
    }

    public function getProcessosAndamento($conta, $processo, $nm_cliente, $responsavel, $tipo, $servico, $status, $reu, $autor, $data, $comarca, $flag_correspondente, $cliente, $statusProcesso, $numero_acompanhamento)
    {

        $sql = "SELECT t1.cd_processo_pro, 
                       t1.nu_processo_pro, 
                       t1.dt_prazo_fatal_pro,
                       t1.hr_audiencia_pro,
                       t1.nm_autor_pro,
                       t1.nm_reu_pro,
                       t2.cd_status_processo_stp, 
                       t2.nm_status_processo_conta_stp,
                       t3.cd_cliente_cli,
                       t3.nm_razao_social_cli,
                       t4.nm_vara_var,
                       t5.cd_correspondente_cor,
                       t5.nm_conta_correspondente_ccr,
                       t6.name,
                       t11.nm_razao_social_con,
                       t7.nm_cidade_cde,
                       t8.sg_estado_est,
                       t10.nm_tipo_servico_tse,
                       t1.nu_acompanhamento_pro,
                       t2.ds_color_stp,
                       t1.created_at
                FROM processo_pro t1
                JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
                JOIN conta_con t11 ON t1.cd_conta_con = t11.cd_conta_con
                JOIN cliente_cli t3 ON t1.cd_cliente_cli = t3.cd_cliente_cli
                LEFT JOIN vara_var t4 ON t1.cd_vara_var = t4.cd_vara_var AND t1.cd_conta_con = t4.cd_conta_con
                LEFT JOIN conta_correspondente_ccr t5 ON t1.cd_conta_con = t5.cd_conta_con AND t1.cd_correspondente_cor = t5.cd_correspondente_cor
                LEFT JOIN users t6 ON t1.cd_responsavel_pro = t6.id
                JOIN cidade_cde t7 ON t1.cd_cidade_cde = t7.cd_cidade_cde
                JOIN estado_est t8 ON t7.cd_estado_est = t8.cd_estado_est
                LEFT JOIN processo_taxa_honorario_pth t9 ON t1.cd_processo_pro = t9.cd_processo_pro
                JOIN tipo_servico_tse t10 ON t9.cd_tipo_servico_tse = t10.cd_tipo_servico_tse
                WHERE t1.cd_status_processo_stp NOT IN(6,7)";

        if($processo) $sql .= " AND t1.nu_processo_pro like '%$processo%' ";
        if($responsavel) $sql .= " AND t1.cd_responsavel_pro = $responsavel ";
        if($tipo) $sql .= " AND t1.cd_tipo_processo_tpo = $tipo ";
        if($servico) $sql .= " AND t9.cd_tipo_servico_tse = $servico ";
        if($reu) $sql .= " AND t1.nm_reu_pro ilike '%$reu%'";
        if($nm_cliente) $sql .= " AND t3.nm_razao_social_cli ilike '%$nm_cliente%'";
        if($autor) $sql .= " AND t1.nm_autor_pro ilike '%$autor%' ";
        if($data) $sql .= " AND t1.dt_prazo_fatal_pro = '$data' ";
        if($comarca) $sql .= " AND t1.cd_cidade_cde = $comarca ";
        if($numero_acompanhamento) $sql .= " AND t1.nu_acompanhamento_pro = '$numero_acompanhamento' ";
        if($flag_correspondente == true) $sql .= " AND t1.cd_correspondente_cor = $conta";
        if($flag_correspondente == false) $sql .= " AND t1.cd_conta_con = $conta";

        if($status){

            if($status == 'dentro-prazo') $sql .= " AND dt_prazo_fatal_pro > current_date  ";
            if($status == 'data-limite') $sql .= " AND dt_prazo_fatal_pro = current_date ";
            if($status == 'atrasado') $sql .= " AND dt_prazo_fatal_pro < current_date ";

        }

        if($statusProcesso) $sql .= "AND t1.cd_status_processo_stp = $statusProcesso ";

        $sql .= " AND t1.deleted_at is null ORDER BY dt_prazo_fatal_pro, hr_audiencia_pro";

        $processos = DB::select($sql);

        foreach ($processos as $key => $processo) {

            $cor = null;
            $situacao = null;
            $background = null;

            if(strtotime(\Carbon\Carbon::today())  < strtotime($processo->dt_prazo_fatal_pro)){  

                $cor = "#356635";
                $situacao = 'DENTRO DO PRAZO';
                $background = "#58ab583d";

            }elseif(strtotime(date(\Carbon\Carbon::today()->toDateString()))  == strtotime($processo->dt_prazo_fatal_pro)){  
                $cor = "#f1bc0b";   
                $situacao = 'DATA LIMITE';
                $background = "#ffeba8";

            }elseif(strtotime(\Carbon\Carbon::today())  > strtotime($processo->dt_prazo_fatal_pro)){
                $cor = "#c26565";  
                $situacao = 'ATRASADO';
                $background = "#ffc3c3";
            }
            
            $processos[$key]->hash = \Crypt::encrypt($processo->cd_processo_pro); 
            $processos[$key]->fonte = $cor;
            $processos[$key]->background = $background;
            $processos[$key]->situacao = $situacao;
            $processos[$key]->dt_prazo_fatal_pro = date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)); 
            $processos[$key]->created_at = date('d/m/Y H:i:s', strtotime($processo->created_at)); 

        }

        return $processos;
    }

    public function getStatusPrazo($nivel, $conta)
    {
        $visivel = ($nivel == 1) ? "" : " AND fl_visivel_correspondente_stp = 'S' ";
        $tipo = ($nivel == 1) ? ' cd_conta_con ' : 'cd_correspondente_cor';

        $sql = "SELECT 
                count(cd_processo_pro) filter (where dt_prazo_fatal_pro = current_date) as hoje,
                count(cd_processo_pro) filter (where dt_prazo_fatal_pro < current_date) as atrasado,
                count(cd_processo_pro) filter (where dt_prazo_fatal_pro > current_date) as prazo,
                count(cd_processo_pro) as total
            FROM processo_pro t1, status_processo_stp t2 
            WHERE t1.cd_status_processo_stp = t2.cd_status_processo_stp 
            $visivel
            AND $tipo = $conta
            AND t1.cd_status_processo_stp NOT IN (6,7)
            AND deleted_at is null";

        return DB::select($sql);
    }

    public static function boot(){

        parent::boot();

        static::deleting(function($processo)
        {
            if($processo->honorario()->first()) $processo->honorario()->first()->delete();
        });

    }
}
