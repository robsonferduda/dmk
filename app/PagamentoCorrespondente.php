<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\StatusPagamentoCorrespondente;

class PagamentoCorrespondente extends Model
{
    use SoftDeletes;

    protected $table      = 'pagamento_correspondente_pag';
    protected $primaryKey = 'cd_pagamento_correspondente_pag';

    protected $fillable = [
        'cd_conta_con',
        'cd_correspondente_cor',
        'nu_mes_pag',
        'nu_ano_pag',
        'vl_total_pag',
        'cd_status_pag',
        'dt_envio_aprovacao_pag',
        'dt_aprovacao_pag',
        'dt_pagamento_pag',
        'ds_observacao_pag',
    ];

    protected $dates = ['deleted_at', 'dt_envio_aprovacao_pag', 'dt_aprovacao_pag', 'dt_pagamento_pag'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function itens()
    {
        return $this->hasMany(PagamentoCorrespondenteItem::class, 'cd_pagamento_correspondente_pag');
    }

    public function correspondente()
    {
        return $this->belongsTo(Correspondente::class, 'cd_correspondente_cor', 'cd_conta_con');
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class, 'cd_conta_con', 'cd_conta_con');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getNmStatusAttribute(): string
    {
        return StatusPagamentoCorrespondente::label($this->cd_status_pag);
    }

    public function getNmMesAnoAttribute(): string
    {
        return str_pad($this->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $this->nu_ano_pag;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function podeEnviarAprovacao(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::GERADO;
    }

    public function podeAprovar(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
    }

    public function podePagar(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::APROVADO;
    }
}
