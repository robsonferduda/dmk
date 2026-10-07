<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContratoSignatario extends Model
{
    const CORRESPONDENTE = 'correspondente';
    const CONTRATANTE = 'contratante';
    const TESTEMUNHA_1 = 'testemunha1';
    const TESTEMUNHA_2 = 'testemunha2';

    const ROTULOS = [
        self::CORRESPONDENTE => 'Correspondente',
        self::CONTRATANTE    => 'DMK (contratante)',
        self::TESTEMUNHA_1   => 'Testemunha 1',
        self::TESTEMUNHA_2   => 'Testemunha 2',
    ];

    /** situação => [rótulo, cor contextual do Bootstrap] */
    const SITUACOES = [
        'aguardando'    => ['Aguardando', 'secondary'],
        'visualizou'    => ['Visualizou', 'info'],
        'assinou'       => ['Assinou', 'success'],
        'recusou'       => ['Recusou', 'danger'],
        'falha_entrega' => ['Falha na entrega', 'warning'],
    ];

    protected $table = 'contrato_signatario_csi';
    protected $primaryKey = 'cd_contrato_signatario_csi';
    protected $fillable = [
        'cd_contrato_assinatura_cas',
        'dc_papel_csi',
        'nu_ordem_csi',
        'nm_signatario_csi',
        'dc_email_csi',
        'dc_telefone_csi',
        'dc_public_id_csi',
        'dc_link_csi',
        'dt_visualizado_csi',
        'dt_assinado_csi',
        'dt_recusado_csi',
        'dt_falha_entrega_csi',
        'dc_motivo_csi',
    ];
    protected $dates = ['dt_visualizado_csi', 'dt_assinado_csi', 'dt_recusado_csi', 'dt_falha_entrega_csi'];

    public function assinatura()
    {
        return $this->belongsTo('App\ContratoAssinatura', 'cd_contrato_assinatura_cas', 'cd_contrato_assinatura_cas');
    }

    public function rotulo(): string
    {
        return self::ROTULOS[$this->dc_papel_csi] ?? $this->dc_papel_csi;
    }

    public function rotuloSituacao(): string
    {
        return self::SITUACOES[$this->situacao()][0];
    }

    public function corSituacao(): string
    {
        return self::SITUACOES[$this->situacao()][1];
    }

    /**
     * Data do último marco relevante (assinatura, recusa, falha ou visualização).
     */
    public function dataSituacao()
    {
        return $this->dt_assinado_csi ?: ($this->dt_recusado_csi ?: ($this->dt_falha_entrega_csi ?: $this->dt_visualizado_csi));
    }

    public function destino(): ?string
    {
        if ($this->dc_email_csi) {
            return $this->dc_email_csi;
        }

        if (preg_match('/^\+55(\d{2})(\d{4,5})(\d{4})$/', (string) $this->dc_telefone_csi, $m)) {
            return "WhatsApp ({$m[1]}) {$m[2]}-{$m[3]}";
        }

        return $this->dc_telefone_csi;
    }

    public function situacao(): string
    {
        if ($this->dt_recusado_csi) {
            return 'recusou';
        }
        if ($this->dt_assinado_csi) {
            return 'assinou';
        }
        if ($this->dt_falha_entrega_csi) {
            return 'falha_entrega';
        }
        if ($this->dt_visualizado_csi) {
            return 'visualizou';
        }

        return 'aguardando';
    }
}
