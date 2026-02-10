<?php

namespace App\Exports\Layout;

use App\Vara;
use App\TipoServico;
use App\Cidade;
use App\Estado;
use App\TipoProcesso;
use App\Contato;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class LayoutProcesso implements WithMultipleSheets
{
    
    public function __construct($dados)
    {
        $this->cliente = $dados['cliente'];
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sub = \DB::table('vara_var')->selectRaw("nu_vara_var, cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->whereRaw("cd_conta_con = ".\Session::get('SESSION_CD_CONTA'))->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("nu_vara_var, cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
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

        $advogados = Contato::whereHas('tipoContato', function ($query) {
            $query->where('fl_tipo_padrao_tct', 'S');
        })->where('cd_conta_con', \Session::get('SESSION_CD_CONTA'))
          ->where('cd_entidade_ete', $this->cliente->cd_entidade_ete)
          ->select('nu_contato_cot', 'cd_contato_cot', 'nm_contato_cot')->get();

        $sheets = [];

        $sheets[0] = new LayoutInstrucoes();
        $sheets[1] = new LayoutPrincipal($varas, $ts, $estatos, $tp, $this->cliente, $advogados);
        $sheets[2] = new LayoutProcessoVaras($varas);
        $sheets[3] = new LayoutTipoServico($ts);
        $sheets[4] = new LayoutCidade($estatos, $cidades);
        $sheets[5] = new LayoutEstado($estatos);
        $sheets[6] = new LayoutTipoProcesso($tp);
        $sheets[7] = new LayoutAdvogadoSolicitante($advogados);
        
        return $sheets;
    }
}
