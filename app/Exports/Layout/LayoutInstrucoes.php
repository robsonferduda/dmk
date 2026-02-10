<?php

namespace App\Exports\Layout;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LayoutInstrucoes implements FromView, WithTitle, WithEvents
{
    public function view(): View
    {
        return view('exports.layout.processo.instrucoes');
    }

    public function title(): string
    {
        return 'LEIA-ME';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                
                // Configurar largura das colunas
                $event->sheet->getColumnDimension('A')->setWidth(80);
                
                // Aplicar estilo ao título
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Aumentar altura da linha do título
                $event->sheet->getRowDimension('1')->setRowHeight(30);
                
                // Aplicar wrap text em todas as células
                $event->sheet->getStyle('A:A')->getAlignment()->setWrapText(true);
                
                // Estilo para subtítulos (linhas 3, 7, 12, 18)
                $subtituloStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '70AD47']
                    ]
                ];
                
                foreach ([3, 8, 14, 20] as $row) {
                    $event->sheet->getStyle("A{$row}")->applyFromArray($subtituloStyle);
                    $event->sheet->getRowDimension($row)->setRowHeight(25);
                }
                
                // Aplicar bordas
                $event->sheet->getStyle('A1:A25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
        ];
    }
}
