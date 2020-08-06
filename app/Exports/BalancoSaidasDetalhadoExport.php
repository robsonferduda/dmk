<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalancoSaidasDetalhadoExport implements FromView, ShouldAutoSize, WithTitle 
{

    public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    
    public function view(): View
    {
        return view('exports.balanco-saidas-detalhado', ['dados' => $this->dados]);
    }

    public function title(): string
    {
        return 'Saídas';
    }

}