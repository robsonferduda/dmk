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

class ProcessoPautaDiariaExportPDF implements FromView, WithEvents, WithTitle
{
    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }
    
    public function view(): View
    {
        return view('exports.pauta-diaria', ['dados' => $this->dados]);
    }

    public function registerEvents(): array
    {
        return [
        
                    AfterSheet::class  => function (AfterSheet $event) {
                        $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                        $event->sheet->getDelegate()->getPageMargins()->setTop(0.1);
                        $event->sheet->getDelegate()->getPageMargins()->setLeft(0.1);
                        $event->sheet->getDelegate()->getPageMargins()->setRight(0.1);
                        $event->sheet->getDelegate()->getPageMargins()->setBottom(0.1);
                        $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(7);
                        $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(4);
                        $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(1);
                    }
            ];
    }

    public function title(): string
    {
        return 'Pauta Diária  '.now()->format('d-m-Y');
    }
}
