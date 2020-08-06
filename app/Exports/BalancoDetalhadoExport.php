<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BalancoDetalhadoExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    
    public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        if(!empty($this->dados['flagBalanco'])){
            $sheets[0] = new BalancoTotalDetalhadoExport($this->dados);
        }

        if(!empty($this->dados['flagEntradas'])){
            $sheets[1] = new BalancoEntradasDetalhadoExport($this->dados);
        }
        
        if(!empty($this->dados['flagSaidas'])){
            $sheets[2] = new BalancoSaidasDetalhadoExport($this->dados);
        }

        if(!empty($this->dados['flagDespesas'])){
            $sheets[3] = new BalancoDespesasDetalhadoExport($this->dados);
        }
        return $sheets;
    }
}