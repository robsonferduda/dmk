<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;

class ProcessoImport implements WithMultipleSheets, WithEvents
{
    private $rows = 0;
    private $nomeArquivoPlanilha;

    public function __construct($nomeArquivoPlanilha = null)
    {
        $this->nomeArquivoPlanilha = $nomeArquivoPlanilha;
    }

    public function sheets(): array
    {
        return [
            0 => new ProcessosSheet($this, $this->nomeArquivoPlanilha),
        ];
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function (AfterImport $event) {
               // dd($event->reader);
            },
                                
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function setRowCount(): int
    {
        return $this->rows++;
    }
}
