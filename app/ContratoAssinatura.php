<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContratoAssinatura extends Model
{
    const ENVIANDO = 'enviando';
    const PENDENTE = 'pendente';
    const ASSINADO = 'assinado';
    const RECUSADO = 'recusado';
    const CANCELADO = 'cancelado';
    const ERRO = 'erro';

    const EM_ANDAMENTO = [self::ENVIANDO, self::PENDENTE];

    const CANAL_EMAIL = 'email';
    const CANAL_WHATSAPP = 'whatsapp';

    /** status => [rótulo, cor contextual do Bootstrap] */
    const SITUACOES = [
        self::ENVIANDO  => ['Enviando', 'secondary'],
        self::PENDENTE  => ['Aguardando assinaturas', 'warning'],
        self::ASSINADO  => ['Assinado por todos', 'success'],
        self::RECUSADO  => ['Recusado', 'danger'],
        self::CANCELADO => ['Cancelado', 'secondary'],
        self::ERRO      => ['Erro no envio', 'danger'],
    ];

    protected $table = 'contrato_assinatura_cas';
    protected $primaryKey = 'cd_contrato_assinatura_cas';
    protected $fillable = [
        'cd_conta_correspondente_ccr',
        'cd_conta_con',
        'cd_documento_autentique_cas',
        'dc_status_cas',
        'dc_canal_cas',
        'dc_caminho_original_cas',
        'dc_caminho_assinado_cas',
        'fl_sandbox_cas',
        'dc_erro_cas',
        'cd_usuario_envio_cas',
        'dt_envio_cas',
        'dt_concluido_cas',
        'dt_recusado_cas',
        'dt_cancelado_cas',
    ];
    protected $dates = ['dt_envio_cas', 'dt_concluido_cas', 'dt_recusado_cas', 'dt_cancelado_cas'];
    protected $casts = ['fl_sandbox_cas' => 'boolean'];

    public function vinculo()
    {
        return $this->belongsTo('App\ContaCorrespondente', 'cd_conta_correspondente_ccr', 'cd_conta_correspondente_ccr');
    }

    public function signatarios()
    {
        return $this->hasMany('App\ContratoSignatario', 'cd_contrato_assinatura_cas', 'cd_contrato_assinatura_cas')
            ->orderBy('nu_ordem_csi');
    }

    public function usuarioEnvio()
    {
        return $this->belongsTo('App\User', 'cd_usuario_envio_cas', 'id');
    }

    public function emAndamento(): bool
    {
        return in_array($this->dc_status_cas, self::EM_ANDAMENTO, true);
    }

    public function rotuloSituacao(): string
    {
        return self::SITUACOES[$this->dc_status_cas][0] ?? $this->dc_status_cas;
    }

    public function corSituacao(): string
    {
        return self::SITUACOES[$this->dc_status_cas][1] ?? 'secondary';
    }

    public function rotuloCanal(): string
    {
        return $this->dc_canal_cas === self::CANAL_WHATSAPP ? 'WhatsApp' : 'E-mail';
    }

    public function signatario(string $papel): ?ContratoSignatario
    {
        return $this->signatarios->firstWhere('dc_papel_csi', $papel);
    }
}
