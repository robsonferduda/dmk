<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class ProcessoParaClienteExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{

	public function __construct(Array $dados)
    {
        $this->dados = $dados;
    }

    
    public function view(): View
    {
        return view('exports.para-cliente', ['dados' => $this->dados]);
    }

    public function bindValue(Cell $cell, $value)
    {

    	if($cell->getColumn() == 'F'){
    	
        	$cell->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        	return true;
    	}

    	return parent::bindValue($cell, $value);
        
    }
}