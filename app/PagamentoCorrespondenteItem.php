<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TipoBaixaHonorario;

class PagamentoCorrespondenteItem extends Model
{
    protected $table      = 'pagamento_correspondente_item_pai';
    protected $primaryKey = 'cd_pagamento_correspondente_item_pai';

    protected $fillable = [
        'cd_pagamento_correspondente_pag',
        'cd_processo_pro',
        'cd_processo_taxa_honorario_pth',
        'cd_processo_despesa_pde',
        'ds_descricao_pai',
        'vl_honorario_pai',
        'vl_despesa_pai',
        'fl_excluido_pai',
    ];

    public function pagamento()
    {
        return $this->belongsTo(PagamentoCorrespondente::class, 'cd_pagamento_correspondente_pag');
    }

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'cd_processo_pro', 'cd_processo_pro');
    }

    public function baixas()
    {
        return $this->hasMany(PagamentoCorrespondenteBaixa::class, 'cd_pagamento_correspondente_item_pai')
            ->orderBy('dt_baixa_pcb')
            ->orderBy('cd_pagamento_correspondente_baixa_pcb');
    }

    public function getVlTotalAttribute(): float
    {
        if ($this->isExcluido()) {
            return 0.0;
        }

        return (float) $this->vl_honorario_pai + (float) $this->vl_despesa_pai;
    }

    public function getVlPagoHonorarioAttribute(): float
    {
        return round((float) $this->baixas
            ->where('cd_tipo_baixa_pcb', TipoBaixaHonorario::HONORARIO)
            ->sum('vl_baixa_pcb'), 2);
    }

    public function getVlPagoDespesaAttribute(): float
    {
        return round((float) $this->baixas
            ->where('cd_tipo_baixa_pcb', TipoBaixaHonorario::DESPESA)
            ->sum('vl_baixa_pcb'), 2);
    }

    public function getVlPagoTotalAttribute(): float
    {
        return round($this->vl_pago_honorario + $this->vl_pago_despesa, 2);
    }

    public function getVlSaldoHonorarioAttribute(): float
    {
        if ($this->isExcluido()) {
            return 0.0;
        }

        return max(0, round((float) $this->vl_honorario_pai - $this->vl_pago_honorario, 2));
    }

    public function getVlSaldoDespesaAttribute(): float
    {
        if ($this->isExcluido()) {
            return 0.0;
        }

        return max(0, round((float) $this->vl_despesa_pai - $this->vl_pago_despesa, 2));
    }

    public function getVlSaldoTotalAttribute(): float
    {
        return round($this->vl_saldo_honorario + $this->vl_saldo_despesa, 2);
    }

    public function getNmStatusPagamentoAttribute(): string
    {
        if ($this->isExcluido()) {
            return 'Excluído';
        }

        $pago = $this->vl_pago_total;

        if ($pago <= 0) {
            return 'Em aberto';
        }

        if ($this->vl_saldo_total <= 0) {
            return 'Pago';
        }

        return 'Parcial';
    }

    public function isExcluido(): bool
    {
        return strtoupper((string) ($this->fl_excluido_pai ?? 'N')) === 'S';
    }

    public function isPago(): bool
    {
        return ! $this->isExcluido() && $this->vl_pago_total > 0 && $this->vl_saldo_total <= 0;
    }

    public function isParcialmentePago(): bool
    {
        return ! $this->isExcluido() && $this->vl_pago_total > 0 && $this->vl_saldo_total > 0;
    }

    /**
     * Flag N/P/S usada em financeiro/saídas para o honorário do processo.
     */
    public function flagPagoCorrespondente(): string
    {
        if ($this->isExcluido() || $this->vl_pago_total <= 0) {
            return 'N';
        }

        if ($this->vl_saldo_total <= 0) {
            return 'S';
        }

        return 'P';
    }
}
