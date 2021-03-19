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
    public function __construct($clientes, $advogados)
    {
        $this->clientes = $clientes;
        $this->advogados = $advogados;
        $this->advogadosMemory = [];
    }

    public function view(): View
    {
        $ic = 0;

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
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
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

    }
}
