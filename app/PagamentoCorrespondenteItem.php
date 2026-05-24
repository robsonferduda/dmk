<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function pagamento()
    {
        return $this->belongsTo(PagamentoCorrespondente::class, 'cd_pagamento_correspondente_pag');
    }

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'cd_processo_pro', 'cd_processo_pro');
    }

    public function getVlTotalAttribute(): float
    {
        return (float) $this->vl_honorario_pai + (float) $this->vl_despesa_pai;
    }
}
