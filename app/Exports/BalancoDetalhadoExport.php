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

        $sheets[0] = new BalancoEntradasDetalhadoExport($this->dados);
        $sheets[1] = new BalancoSaidasDetalhadoExport($this->dados);
        
        return $sheets;
    }
}