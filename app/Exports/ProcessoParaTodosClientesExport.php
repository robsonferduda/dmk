<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ProcessoParaTodosClientesExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{

	public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    
    public function view(): View
    {
        return view('exports.para-todos-clientes', ['dados' => $this->dados]);
    }

    // public function columnFormats(): array
    // {

    //     return [
    //         'G' => NumberFormat::FORMAT_TEXT,            
    //     ];
    // }

    public function bindValue(Cell $cell, $value)
    {

    	if($cell->getColumn() == 'G'){
    	
        	$cell->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        	return true;
    	}

    	return parent::bindValue($cell, $value);
        
    }

}