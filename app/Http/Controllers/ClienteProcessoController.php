<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Excel;
use App\User;
use App\Fone;
use App\Cidade;
use App\Cliente;
use App\Contato;
use App\Endereco;
use App\Entidade;
use App\EnderecoEletronico;
use App\Identificacao;
use App\TipoContato;
use App\TipoServico;
use App\TipoDespesa;
use App\GrupoCidade;
use App\TaxaHonorario;
use App\ReembolsoTipoDespesa;
use App\GrupoCidadeRelacionamento;
use App\Enums\Nivel;
use App\Imports\ClientesImport;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Exports\Clientes\RelacaoClientesEscritorioExport;

class ClienteProcessoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function processos()
    {
        $processos = array();

        return view('cliente/processo/listar', ['clientes' => $clientes]);
    }  

    public function novo()
    {
        Session::put('item_pai','processo.novo');

        if (!\Cache::has('estados')) {
            $estados = Estado::orderBy('nm_estado_est')->get();
            \Cache::put('estados', $estados, now()->addMinutes(1440));
        } else {
            $estados =  \Cache::get('estados');
        }
        
        $sub = \DB::table('vara_var')->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->whereRaw("cd_conta_con = $this->cdContaCon")->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
        ->get();

        $tiposProcesso  = TipoProcesso::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_tipo_processo_tpo')->get();
        $tiposDeServico = TipoServico::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_tipo_servico_tse')->get();

        return view('cliente/processo/novo', ['estados' => $estados,'varas' => $varas, 'tiposProcesso' => $tiposProcesso, 'tiposDeServico' => $tiposDeServico]);
    }    
}