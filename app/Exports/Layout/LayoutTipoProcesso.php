<?php

namespace App\Exports\Layout;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Vara;

class LayoutTipoProcesso implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    
    public function __construct($tp)
    {
        $this->tp = $tp;
    }

    public function view(): View
    {
        return view('exports.layout.processo.tipo_processo', ['tp' => $this->tp]);
    }

    public function title(): string
    {
        return 'Tipos_de_Processo';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
            }
        ];
    }
}
