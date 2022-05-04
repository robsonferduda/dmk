<?php

namespace App\Exports\Correspondente;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class PagamentoCorrespondenteDetalhadoXlsExport implements FromView, ShouldAutoSize, WithEvents
{

	public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    public function registerEvents(): array
    {
        return [
        
                    AfterSheet::class  => function (AfterSheet $event) {               
                        $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);     
                        $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(35);
                        $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(35);
                        $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                        $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(10); 
                        $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(50);
                        $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);   
                        $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(25);   
                        $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(30);                        
                        $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(100);                        
                    }
            ];
    }

    
    public function view(): View
    {
        return view('exports.correspondentes.pagamento-corresponente-detalhado-xls', ['dados' => $this->dados]);
    }
}