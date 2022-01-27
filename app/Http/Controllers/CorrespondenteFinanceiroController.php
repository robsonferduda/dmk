<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Conta;
use App\Processo;
use App\AnexoFinanceiro;

class CorrespondenteFinanceiroController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    private function meses()
    {
        $meses = [];
        
        $meses["1"]= 'Janeiro';
        $meses["2"]= 'Fevereiro';
        $meses["3"]= 'Março';
        $meses["4"]= 'Abril';
        $meses["5"]= 'Maio';
        $meses["6"]= 'Junho';
        $meses["7"]= 'Julho';
        $meses["8"]= 'Agosto';
        $meses["9"]= 'Setembro';
        $meses["10"]= 'Outubro';
        $meses["11"]= 'Novembro';
        $meses["12"]= 'Dezembro';

        return $meses;
    }

    public function comprovantes()
    {
        return view('correspondente/financeiro/comprovantes-pagamento/index', ['comprovantes' => [], 'meses' => $this->meses()]);
    }

    public function buscar(Request $request)
    {
        $cliente = $request->cd_conta_con;
        $mes = $request->mes;
        $pro = $request->processo;

        $processos = Processo::when($cliente, function ($query) use ($cliente) {
            $query->where('cd_conta_con', $cliente);
        })
        ->when($mes, function ($query) use ($mes) {
            $query->whereMonth('dt_prazo_fatal_pro', $mes);
        })
        ->when($pro, function ($query) use ($pro) {
            $query->where('nu_processo_pro', 'ilike', "%$pro%");
        })
        ->where('cd_correspondente_cor', $this->conta)
        ->get();

        $arquivos = [];
        foreach ($processos as $processo) {
            if (!$processo->honorario->anexoFinanceiro->isEmpty()) {
                foreach ($processo->honorario->anexoFinanceiro as $anexo) {
                    if ($anexo->cd_tipo_financeiro_tfn == \TipoFinanceiro::SAIDA) {
                        
                        //dd(md5_file(storage_path().'/arquivos/'.$processo->cd_conta_con.'/saidas/anexos/'.$processo->honorario->cd_processo_taxa_honorario_pth.'/'.$anexo->nm_anexo_financeiro_afn));
                        if (file_exists(storage_path().'/arquivos/'.$processo->cd_conta_con.'/saidas/anexos/'.$processo->honorario->cd_processo_taxa_honorario_pth.'/'.$anexo->nm_anexo_financeiro_afn)) {
                            $time = $anexo->updated_at->getTimestamp();
                            $hash = md5_file(storage_path().'/arquivos/'.$processo->cd_conta_con.'/saidas/anexos/'.$processo->honorario->cd_processo_taxa_honorario_pth.'/'.$anexo->nm_anexo_financeiro_afn);
                            $arquivos[$hash] = ['cliente' => $processo->conta->nm_razao_social_con,
                                           'nome' => $anexo->nm_anexo_financeiro_afn,
                                           'path' => $anexo->nm_local_anexo_financeiro_afn,
                                           'id'   => $anexo->cd_processo_taxa_honorario_pth,
                                           'conta' => $processo->cd_conta_con
                                          ];
                        }
                    }
                }
            }
        }
       
        return view('correspondente/financeiro/comprovantes-pagamento/index', ['comprovantes' => $arquivos, 'meses' => $this->meses(), 'mesParam' => $mes, 'processo' => $pro]);
    }
}
