<?php

namespace App\Exports\Layout;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Vara;

class LayoutProcessoVaras implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct($varas)
    {
        $this->varas = $varas;
    }

    public function view(): View
    {
        return view('exports.layout.processo.varas', ['varas' => $this->varas]);
    }

    public function title(): string
    {
        return 'Varas';
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
