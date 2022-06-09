<?php

namespace App\Exports\Layout;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LayoutCidade implements FromView, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{
    public function __construct($estados, $cidades)
    {
        $this->estados = $estados;
        $this->cidades = $cidades;
        $this->cidadesMemory = [];
    }

    public function view(): View
    {
        $ie = 0;

        $cidades = [];
        foreach ($this->estados as $estado) {
            $cidades[$estado->cd_estado_est] = [];
        }

        foreach ($this->cidades as $cidade) {
            array_push($cidades[$cidade->cd_estado_est], $cidade->nm_cidade_cde);
        }

        $this->cidadesMemory = $cidades;

        return view('exports.layout.processo.cidades', ['estados' => $this->estados, 'cidades' => $cidades]);
    }

    public function title(): string
    {
        return 'Cidades';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                $letras = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O',
                           'P','Q','R','S','T','U','V','W','X','Y','Z','AA'];
                $i = 0;

                $sheet = $event->sheet;

                foreach ($this->estados as $estado) {
                    $range = '$'.$letras[$i].'$2:$'.$letras[$i].'$'.(count($this->cidadesMemory[$estado->cd_estado_est])+1);
                    $sheet->getParent()->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange($estado->sg_estado_est, $sheet->getDelegate(), $range));
        
                    //  print_r($letras[$i].'2:'.$letras[$i].(count($this->cidadesMemory[$estado->cd_estado_est])+1)); echo '<br>';
                    $i++;
                }
                // exit;
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
            'W' => NumberFormat::FORMAT_TEXT,
            'X' => NumberFormat::FORMAT_TEXT,
            'Y' => NumberFormat::FORMAT_TEXT,
            'Z' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT
        ];
    }
}
