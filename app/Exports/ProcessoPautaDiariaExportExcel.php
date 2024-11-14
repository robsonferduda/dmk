<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ProcessoPautaDiariaExportExcel implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(array $dados, $dt_inicio, $dt_fim)
    {
        $this->dados = $dados;
        $this->dt_inicio = $dt_inicio;
        $this->dt_fim = $dt_fim;
    }
    
    public function view(): View
    {
        return view('exports.pauta-diaria', ['dados' => $this->dados, 'dt_inicio' => $this->dt_inicio, 'dt_fim' => $this->dt_fim]);
    }

    public function title(): string
    {
        return 'Pauta Diária  '.now()->format('d-m-Y');
    }
}
