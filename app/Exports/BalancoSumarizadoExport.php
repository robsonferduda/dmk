<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BalancoSumarizadoExport implements WithMultipleSheets
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

        $sheets[0] = new BalancoTotalSumarizadoExport($this->dados);
        $sheets[1] = new BalancoEntradasSumarizadoExport($this->dados);
        $sheets[2] = new BalancoSaidasSumarizadoExport($this->dados);
        $sheets[3] = new BalancoDespesasSumarizadoExport($this->dados);
        
        return $sheets;
    }
}