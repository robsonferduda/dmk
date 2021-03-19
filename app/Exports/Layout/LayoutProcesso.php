<?php

namespace App\Exports\Layout;

use App\Vara;
use App\TipoServico;
use App\Cidade;
use App\Estado;
use App\TipoProcesso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class LayoutProcesso extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements WithMultipleSheets
{
    
    public function __construct($dados)
    {
        $this->cliente = $dados['cliente'];
        $this->contato = $dados['contato'];
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $varas = Vara::where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                ->select('nu_vara_var', 'nm_vara_var')
                ->orderBy('nm_vara_var')
                ->get();

        $ts = TipoServico::where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                ->select('nu_tipo_servico_tse', 'nm_tipo_servico_tse')
                ->orderBy('nm_tipo_servico_tse')
                ->get();

        $estatos = Estado::select('cd_estado_est', 'sg_estado_est', 'nm_estado_est')
                ->orderBy('sg_estado_est')
                ->get();

        $cidades = Cidade::select('cd_cidade_cde', 'cd_estado_est', 'nm_cidade_cde')
                ->orderBy('nm_cidade_cde')
                ->get();

        $tp = TipoProcesso::where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
                ->select('nu_tipo_processo_tpo', 'nm_tipo_processo_tpo')
                ->orderBy('nm_tipo_processo_tpo')
                ->get();

        $sheets = [];

        $sheets[0] = new LayoutPrincipal($varas, $ts, $estatos, $tp, $this->cliente, $this->contato);
        $sheets[1] = new LayoutProcessoVaras($varas);
        $sheets[2] = new LayoutTipoServico($ts);
        $sheets[3] = new LayoutCidade($estatos, $cidades);
        $sheets[4] = new LayoutEstado($estatos);
        $sheets[5] = new LayoutTipoProcesso($tp);
        
        return $sheets;
    }
}
