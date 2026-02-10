<?php

namespace App\Exports\Layout;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Vara;

class LayoutPrincipal implements FromView, WithTitle, WithEvents, WithColumnWidths
{
    public function __construct($varas, $tiposSevico, $estados, $tiposProcesso, $cliente, $advogados)
    {
        $this->varas = $varas;
        $this->tiposSevico = $tiposSevico;
        $this->estados = $estados;
        $this->tiposProcesso = $tiposProcesso;
        $this->cliente = $cliente;
        $this->advogados = $advogados;
    }

    public function view(): View
    {
        return view('exports.layout.processo.principal');
    }

    public function title(): string
    {
        return 'Processos';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 12,
            'I' => 12,
            'J' => 30,
            'K' => 30,
            'L' => 30,
            'M' => 30,
            'N' => 30,
            'O' => 30,

        ];
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Cliente
                $drop_column = 'A';

                $nomeCliente = $this->cliente->nm_razao_social_cli.' ---'.$this->cliente->nu_cliente_cli.'---';

                $validation = $event->sheet->getCell("{$drop_column}2");
                $validation->setValue($nomeCliente);
            
                // Advogado Solicitante
                $drop_column = 'B';

                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Advogado Solicitante');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('Advogados!A$2:A$'.($this->advogados->count()+1));

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }


                // Vara
                $drop_column = 'K';

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Vara');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('Varas!A$2:A$'.($this->varas->count()+1));

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }

                // TipoServiço
                $drop_column = 'L';

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Tipo de Serviço');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('Tipos_de_Serviço!A$2:A$'.($this->tiposSevico->count()+1));

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }

                // Estado
                $drop_column = 'I';

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Estado');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('Estados!A$2:A$28');

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }

                // Comarca - Lista simples padronizada para todos os formatos
                $drop_column = 'J';

                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Comarca');
                $validation->setError('Selecione uma comarca da lista.');
                $validation->setPromptTitle('Comarca');
                $validation->setPrompt('Selecione uma comarca no formato "Cidade (UF)" (ex: Florianópolis (SC)). A lista está organizada alfabeticamente.');
                $validation->setFormula1('Cidades!$A$2:$A$10000');

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }


                // Tipo de Processo
                $drop_column = 'M';

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Tipo de Processo');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('Tipos_de_PROCESSO!A$2:A$'.($this->tiposProcesso->count()+1));

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }

                // Área do Direito
                $drop_column = 'N';

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Erro de entrada de dados.');
                $validation->setError('O valor não está na lista.');
                $validation->setPromptTitle('Área do Direito');
                $validation->setPrompt('Selecione um valor da lista.');
                $validation->setFormula1('"53 - CÍVEL,55 - TRABALHISTA"');

                // clone validation to remaining rows
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }
            },
        ];
    }
}
