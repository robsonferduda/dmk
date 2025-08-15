<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Conta;
use App\Processo;
use App\ProcessoMensagem;
use App\Correspondente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $conta;
    
    public function __construct()
    {
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        if (!Auth::guest()) {
            $role = Auth::user()->role()->first();

            $role = ($role) ? $role->slug : null;

            switch ($role) {
                case 'correspondente':
                    return redirect('correspondente/dashboard/'.\Crypt::encrypt(Auth::user()->cd_entidade_ete));
                    break;
                
                default:
                    $conta = Conta::where('cd_conta_con', Auth::user()->cd_conta_con)->first();
                    $processos = Processo::where('cd_conta_con', $conta->cd_conta_con)->get();
                    return view('home', ['conta' => $conta, 'processos' => $processos]);
                    break;
            }
        } else {
            return view('inicio');
        }
    }

    public function menu(Request $request, $id)
    {
        if (session('menu_pai') == $id) {
            Session::put('menu_pai', "");
        } else {
            Session::put('menu_pai', $id);
        }

        return $request->url();
    }

    public function minify()
    {
        if (session('menu_minify') == 'on') {
            Session::put('menu_minify', 'off');
        } else {
            Session::put('menu_minify', 'on');
        }

        return back();
    }

    public function correspondentes(Request $request)
    {
        $inicio = $request->input('data_inicio'); // formato: yyyy-mm-dd
        $fim = $request->input('data_fim');       // formato: yyyy-mm-dd

        $sql = "SELECT t2.nm_razao_social_con, t5.nm_cidade_cde, t3.cd_entidade_ete, count(*) AS total_processos 
                FROM processo_pro t1
                JOIN conta_con t2 ON t2.cd_conta_con = t1.cd_correspondente_cor
                JOIN entidade_ete t3 On t3.cd_conta_con = t2.cd_conta_con 
                LEFT JOIN cidade_atuacao_cat t4 ON t4.cd_entidade_ete = t3.cd_entidade_ete AND fl_origem_cat = 'S'
                LEFT JOIN cidade_cde t5 ON t5.cd_cidade_cde = t4.cd_cidade_cde 
                WHERE t1.cd_conta_con = 64
                AND t1.cd_status_processo_stp IN(6)
                AND t1.dt_prazo_fatal_pro between '$inicio' AND '$fim'
                GROUP BY t2.nm_razao_social_con, t5.nm_cidade_cde, t3.cd_entidade_ete
                ORDER BY total_processos DESC
                LIMIT 5";

        $correspondentes = DB::select($sql);

        return view('dashboard/partes/correspondentes', compact('correspondentes'));
    }
}
