<?php

namespace App\Exports\Clientes;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RelacaoClientesEscritorioExport implements FromView, ShouldAutoSize
{

	public function __construct(Array $dados, Array $valores, Array $labels)
    {
        $this->dados = $dados;
        $this->valores = $valores;
        $this->labels = $labels;
    }
    
    public function view(): View
    {
        return view('exports.clientes.relacao-clientes-escritorio', ['dados' => $this->dados, 'valores' => $this->valores, 'labels' => $this->labels]);
    }
}