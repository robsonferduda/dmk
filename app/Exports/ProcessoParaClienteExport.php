<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProcessoParaClienteExport implements FromView, ShouldAutoSize
{

	public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    
    public function view(): View
    {
        return view('exports.para-cliente', ['dados' => $this->dados]);
    }
}