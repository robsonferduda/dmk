<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TipoBaixaHonorario;

class PagamentoCorrespondenteBaixa extends Model
{
    protected $table      = 'pagamento_correspondente_baixa_pcb';
    protected $primaryKey = 'cd_pagamento_correspondente_baixa_pcb';

    protected $fillable = [
        'cd_pagamento_correspondente_pag',
        'cd_tipo_baixa_pcb',
        'vl_baixa_pcb',
        'dt_baixa_pcb',
        'ds_observacao_pcb',
        'dc_comprovante_pcb',
    ];

    protected $dates = ['dt_baixa_pcb'];

    public function pagamento()
    {
        return $this->belongsTo(PagamentoCorrespondente::class, 'cd_pagamento_correspondente_pag');
    }

    public function getNmTipoAttribute(): string
    {
        return ((int) $this->cd_tipo_baixa_pcb === TipoBaixaHonorario::DESPESA)
            ? 'Despesa'
            : 'Honorário';
    }

    public function isHonorario(): bool
    {
        return (int) $this->cd_tipo_baixa_pcb === TipoBaixaHonorario::HONORARIO;
    }

    public function isDespesa(): bool
    {
        return (int) $this->cd_tipo_baixa_pcb === TipoBaixaHonorario::DESPESA;
    }
}
