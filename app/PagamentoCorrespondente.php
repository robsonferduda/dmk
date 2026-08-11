<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\TipoBaixaHonorario;
use Carbon\Carbon;

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
        'dc_comprovante_pag',
    ];

    protected $dates = ['deleted_at', 'dt_envio_aprovacao_pag', 'dt_aprovacao_pag', 'dt_pagamento_pag'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function itens()
    {
        return $this->hasMany(PagamentoCorrespondenteItem::class, 'cd_pagamento_correspondente_pag');
    }

    public function baixas()
    {
        return $this->hasMany(PagamentoCorrespondenteBaixa::class, 'cd_pagamento_correspondente_pag')
            ->orderBy('dt_baixa_pcb')
            ->orderBy('cd_pagamento_correspondente_baixa_pcb');
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
        if ($this->isParcialmentePago() && $this->cd_status_pag === StatusPagamentoCorrespondente::APROVADO) {
            return 'Parcialmente Pago';
        }

        return StatusPagamentoCorrespondente::label($this->cd_status_pag);
    }

    public function getNmMesAnoAttribute(): string
    {
        return str_pad($this->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $this->nu_ano_pag;
    }

    public function getVlHonorarioTotalAttribute(): float
    {
        return (float) $this->itensAtivos()->sum(function ($item) {
            return (float) $item->vl_honorario_pai;
        });
    }

    public function getVlDespesaTotalAttribute(): float
    {
        return (float) $this->itensAtivos()->sum(function ($item) {
            return (float) $item->vl_despesa_pai;
        });
    }

    public function getVlPagoHonorarioAttribute(): float
    {
        return (float) $this->baixas
            ->where('cd_tipo_baixa_pcb', TipoBaixaHonorario::HONORARIO)
            ->sum('vl_baixa_pcb');
    }

    public function getVlPagoDespesaAttribute(): float
    {
        return (float) $this->baixas
            ->where('cd_tipo_baixa_pcb', TipoBaixaHonorario::DESPESA)
            ->sum('vl_baixa_pcb');
    }

    public function getVlPagoTotalAttribute(): float
    {
        return round($this->vl_pago_honorario + $this->vl_pago_despesa, 2);
    }

    public function getVlSaldoHonorarioAttribute(): float
    {
        return max(0, round($this->vl_honorario_total - $this->vl_pago_honorario, 2));
    }

    public function getVlSaldoDespesaAttribute(): float
    {
        return max(0, round($this->vl_despesa_total - $this->vl_pago_despesa, 2));
    }

    public function getVlSaldoTotalAttribute(): float
    {
        return round($this->vl_saldo_honorario + $this->vl_saldo_despesa, 2);
    }

    public function getQtdItensAtivosAttribute(): int
    {
        return $this->itensAtivos()->count();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function itensAtivos()
    {
        return $this->itens->filter(function ($item) {
            return ! $item->isExcluido();
        });
    }

    public function isParcialmentePago(): bool
    {
        $pago = $this->vl_pago_total;

        return $pago > 0 && $this->vl_saldo_total > 0;
    }

    public function podeEnviarAprovacao(): bool
    {
        return in_array($this->cd_status_pag, [
            StatusPagamentoCorrespondente::GERADO,
            StatusPagamentoCorrespondente::RECUSADO,
        ]);
    }

    public function podeReenviarAprovacao(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
    }

    public function podeNotificarAprovacao(): bool
    {
        return $this->podeEnviarAprovacao() || $this->podeReenviarAprovacao();
    }

    public function podeAprovar(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
    }

    public function podePagar(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::APROVADO
            && $this->vl_saldo_total > 0;
    }

    public function podeRecusar(): bool
    {
        return $this->cd_status_pag === StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
    }

    public function podeAtualizarValores(): bool
    {
        return $this->cd_status_pag !== StatusPagamentoCorrespondente::PAGO
            && $this->vl_pago_total <= 0;
    }

    public function podeGerenciarBaixas(): bool
    {
        return in_array($this->cd_status_pag, [
            StatusPagamentoCorrespondente::APROVADO,
            StatusPagamentoCorrespondente::PAGO,
        ], true);
    }

    /**
     * Atualiza o status conforme o saldo das baixas.
     *
     * @param  string|\DateTimeInterface|null  $dtPagamento  Data a usar ao marcar como Pago (ex.: baixa retroativa).
     */
    public function sincronizarStatusPagamento($dtPagamento = null): void
    {
        if (! in_array($this->cd_status_pag, [
            StatusPagamentoCorrespondente::APROVADO,
            StatusPagamentoCorrespondente::PAGO,
        ], true)) {
            return;
        }

        $this->unsetRelation('baixas');
        $this->unsetRelation('itens');
        $this->load(['baixas', 'itens.baixas']);

        if ($this->vl_saldo_total <= 0 && $this->vl_pago_total > 0) {
            $this->cd_status_pag = StatusPagamentoCorrespondente::PAGO;

            if (! $this->dt_pagamento_pag) {
                if ($dtPagamento) {
                    $this->dt_pagamento_pag = Carbon::parse($dtPagamento);
                } else {
                    $ultimaBaixa = $this->baixas->sortByDesc(function ($baixa) {
                        return ($baixa->dt_baixa_pcb ? $baixa->dt_baixa_pcb->format('Y-m-d') : '0000-00-00')
                            . '-' . $baixa->cd_pagamento_correspondente_baixa_pcb;
                    })->first();

                    $this->dt_pagamento_pag = $ultimaBaixa && $ultimaBaixa->dt_baixa_pcb
                        ? $ultimaBaixa->dt_baixa_pcb
                        : now();
                }
            }
        } else {
            $this->cd_status_pag    = StatusPagamentoCorrespondente::APROVADO;
            $this->dt_pagamento_pag = null;
        }

        $this->save();
        $this->sincronizarStatusProcessos();
    }

    /**
     * Propaga a baixa por processo para fl_pago_correspondente_pth (N/P/S).
     */
    public function sincronizarStatusProcessos(): void
    {
        if (! $this->relationLoaded('itens')) {
            $this->load('itens.baixas');
        }

        foreach ($this->itens as $item) {
            if (! $item->cd_processo_taxa_honorario_pth) {
                continue;
            }

            ProcessoTaxaHonorario::where('cd_processo_taxa_honorario_pth', $item->cd_processo_taxa_honorario_pth)
                ->where('cd_conta_con', $this->cd_conta_con)
                ->update([
                    'fl_pago_correspondente_pth' => $item->flagPagoCorrespondente(),
                ]);
        }
    }
}
