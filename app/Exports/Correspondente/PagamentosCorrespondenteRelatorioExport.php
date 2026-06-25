<?php

namespace App\Exports\Correspondente;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class PagamentosCorrespondenteRelatorioExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    protected $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function view(): View
    {
        return view('exports.correspondentes.pagamentos-relatorio-xls', $this->dados);
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
