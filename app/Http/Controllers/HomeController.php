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
                    
                    // Calcular tamanho da pasta
                    $infoEspaco = $this->calcularEspacoPasta();
                    
                    return view('home', [
                        'conta' => $conta, 
                        'processos' => $processos,
                        'infoEspaco' => $infoEspaco
                    ]);
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
                WHERE t1.cd_conta_con = $this->conta
                AND t1.cd_status_processo_stp IN(6)
                AND t1.dt_prazo_fatal_pro between '$inicio' AND '$fim'
                GROUP BY t2.nm_razao_social_con, t5.nm_cidade_cde, t3.cd_entidade_ete
                ORDER BY total_processos DESC
                LIMIT 5";

        $correspondentes = DB::select($sql);

        return view('dashboard/partes/correspondentes', compact('correspondentes'));
    }

    private function calcularEspacoPasta()
    {
        $caminhoPasta = storage_path(env('APP_STORAGE_FOLDER', 'arquivos/1'));
        
        // Tamanho total ocupado pela pasta
        $tamanhoOcupado = 0;
        
        if (file_exists($caminhoPasta)) {
            $tamanhoOcupado = $this->getTamanhoRecursivo($caminhoPasta);
        }
        
        // Limite definido (padrão: 10 GB) - pode ser configurado no .env
        $limiteBytes = env('STORAGE_LIMIT_GB', 10) * 1024 * 1024 * 1024;
        $espacoDisponivel = $limiteBytes - $tamanhoOcupado;
        
        // Calcular percentual em relação ao limite
        $percentualUso = $limiteBytes > 0 ? ($tamanhoOcupado / $limiteBytes) * 100 : 0;
        
        return [
            'tamanho_pasta' => $this->formatarTamanho($tamanhoOcupado),
            'tamanho_pasta_bytes' => $tamanhoOcupado,
            'limite_definido' => $this->formatarTamanho($limiteBytes),
            'limite_bytes' => $limiteBytes,
            'espaco_disponivel' => $this->formatarTamanho($espacoDisponivel),
            'espaco_disponivel_bytes' => $espacoDisponivel,
            'percentual_uso' => round($percentualUso, 2)
        ];
    }

    private function getTamanhoRecursivo($caminho)
    {
        $tamanhoTotal = 0;
        
        if (is_file($caminho)) {
            return filesize($caminho);
        }
        
        if (is_dir($caminho)) {
            $arquivos = scandir($caminho);
            
            foreach ($arquivos as $arquivo) {
                if ($arquivo != '.' && $arquivo != '..') {
                    $caminhoCompleto = $caminho . DIRECTORY_SEPARATOR . $arquivo;
                    $tamanhoTotal += $this->getTamanhoRecursivo($caminhoCompleto);
                }
            }
        }
        
        return $tamanhoTotal;
    }

    private function formatarTamanho($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $indice = 0;
        
        while ($bytes >= 1024 && $indice < count($unidades) - 1) {
            $bytes /= 1024;
            $indice++;
        }
        
        return round($bytes, 2) . ' ' . $unidades[$indice];
    }
}
