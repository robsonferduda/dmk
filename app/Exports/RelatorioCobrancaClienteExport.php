<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class RelatorioCobrancaClienteExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function view(): View
    {
        return view('exports.cobranca-cliente', ['dados' => $this->dados]);
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'F') {
            $cell->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
