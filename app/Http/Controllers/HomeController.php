<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Conta;
use App\Processo;
use App\ProcessoMensagem;
use App\Correspondente;
use App\LogAcesso;
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
                    
                    return view('home', [
                        'conta' => $conta, 
                        'processos' => $processos
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

    public function acessosRecentes()
    {
        $acessos = LogAcesso::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.partes.acessos-recentes', compact('acessos'));
    }

    // -----------------------------------------------------------------------
    // Dashboard do Escritório — endpoints de API
    // -----------------------------------------------------------------------

    public function escritorioContadores()
    {
        $conta  = $this->conta;
        $hoje   = date('Y-m-d');
        $em7    = date('Y-m-d', strtotime('+7 days'));

        $excluidos = [6, 7, 19]; // FINALIZADO, CANCELADO, CANCELADO_PELO_ESCRITORIO

        $total_ativos = DB::table('processo_pro')
            ->where('cd_conta_con', $conta)
            ->whereNotIn('cd_status_processo_stp', $excluidos)
            ->whereNull('deleted_at')
            ->count();

        $audiencias_hoje = DB::table('processo_pro')
            ->where('cd_conta_con', $conta)
            ->whereNotIn('cd_status_processo_stp', $excluidos)
            ->whereDate('dt_prazo_fatal_pro', $hoje)
            ->whereNull('deleted_at')
            ->count();

        $proximos_7_dias = DB::table('processo_pro')
            ->where('cd_conta_con', $conta)
            ->whereNotIn('cd_status_processo_stp', $excluidos)
            ->whereDate('dt_prazo_fatal_pro', '>', $hoje)
            ->whereDate('dt_prazo_fatal_pro', '<=', $em7)
            ->whereNull('deleted_at')
            ->count();

        $mensagens_nao_lidas = DB::table('processo_mensagem_prm')
            ->join('processo_pro', 'processo_pro.cd_processo_pro', '=', 'processo_mensagem_prm.cd_processo_pro')
            ->where('processo_pro.cd_conta_con', $conta)
            ->where('processo_mensagem_prm.destinatario_prm', $conta)
            ->whereNull('processo_mensagem_prm.fl_leitura_prm')
            ->whereNull('processo_pro.deleted_at')
            ->count();

        $pendentes_analise = DB::table('processo_pro')
            ->where('cd_conta_con', $conta)
            ->where('cd_status_processo_stp', 14) // PENDENTE_ANALISE
            ->whereNull('deleted_at')
            ->count();

        $correspondentes_ativos = DB::table('processo_pro')
            ->where('cd_conta_con', $conta)
            ->whereNotIn('cd_status_processo_stp', $excluidos)
            ->whereNotNull('cd_correspondente_cor')
            ->whereNull('deleted_at')
            ->distinct('cd_correspondente_cor')
            ->count('cd_correspondente_cor');

        return response()->json(compact(
            'total_ativos',
            'audiencias_hoje',
            'proximos_7_dias',
            'mensagens_nao_lidas',
            'pendentes_analise',
            'correspondentes_ativos'
        ));
    }

    public function escritorioPautaHoje()
    {
        $conta = $this->conta;

        $processos = DB::select("
            SELECT t1.cd_processo_pro, t1.nu_processo_pro, t1.hr_audiencia_pro,
                   t1.dt_prazo_fatal_pro,
                   t1.nm_autor_pro, t1.nm_reu_pro,
                   t2.nm_status_processo_conta_stp, t2.ds_color_stp,
                   t3.nm_razao_social_cli,
                   t5.nm_conta_correspondente_ccr,
                   t7.nm_cidade_cde, t8.sg_estado_est,
                   t10.nm_tipo_servico_tse,
                   t11.nm_tipo_processo_tpo,
                   t6.name as nm_responsavel
            FROM processo_pro t1
            JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
            JOIN cliente_cli t3 ON t1.cd_cliente_cli = t3.cd_cliente_cli
            JOIN cidade_cde t7 ON t1.cd_cidade_cde = t7.cd_cidade_cde
            JOIN estado_est t8 ON t7.cd_estado_est = t8.cd_estado_est
            LEFT JOIN conta_correspondente_ccr t5 ON t1.cd_conta_con = t5.cd_conta_con AND t1.cd_correspondente_cor = t5.cd_correspondente_cor
            LEFT JOIN users t6 ON t1.cd_responsavel_pro = t6.id
            LEFT JOIN processo_taxa_honorario_pth t9 ON t1.cd_processo_pro = t9.cd_processo_pro
            LEFT JOIN tipo_servico_tse t10 ON t9.cd_tipo_servico_tse = t10.cd_tipo_servico_tse
            LEFT JOIN tipo_processo_tpo t11 ON t11.cd_tipo_processo_tpo = t1.cd_tipo_processo_tpo
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.dt_prazo_fatal_pro = current_date
              AND t1.deleted_at IS NULL
            ORDER BY t1.hr_audiencia_pro
        ", ['conta' => $conta]);

        $total = count($processos);
        $lista = array_slice($processos, 0, 10);
        $lista = array_map(function ($p) {
            $p->hash = \Crypt::encrypt($p->cd_processo_pro);
            return $p;
        }, $lista);

        return response()->json(['total' => $total, 'processos' => $lista]);
    }

    public function escritorioProximas()
    {
        $conta = $this->conta;

        $processos = DB::select("
            SELECT t1.cd_processo_pro, t1.nu_processo_pro, t1.dt_prazo_fatal_pro, t1.hr_audiencia_pro,
                   t2.nm_status_processo_conta_stp, t2.ds_color_stp,
                   t3.nm_razao_social_cli,
                   t5.nm_conta_correspondente_ccr,
                   t7.nm_cidade_cde, t8.sg_estado_est,
                   t10.nm_tipo_servico_tse,
                   t11.nm_tipo_processo_tpo,
                   t6.name as nm_responsavel
            FROM processo_pro t1
            JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
            JOIN cliente_cli t3 ON t1.cd_cliente_cli = t3.cd_cliente_cli
            JOIN cidade_cde t7 ON t1.cd_cidade_cde = t7.cd_cidade_cde
            JOIN estado_est t8 ON t7.cd_estado_est = t8.cd_estado_est
            LEFT JOIN conta_correspondente_ccr t5 ON t1.cd_conta_con = t5.cd_conta_con AND t1.cd_correspondente_cor = t5.cd_correspondente_cor
            LEFT JOIN users t6 ON t1.cd_responsavel_pro = t6.id
            LEFT JOIN processo_taxa_honorario_pth t9 ON t1.cd_processo_pro = t9.cd_processo_pro
            LEFT JOIN tipo_servico_tse t10 ON t9.cd_tipo_servico_tse = t10.cd_tipo_servico_tse
            LEFT JOIN tipo_processo_tpo t11 ON t11.cd_tipo_processo_tpo = t1.cd_tipo_processo_tpo
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.dt_prazo_fatal_pro > current_date
              AND t1.deleted_at IS NULL
            ORDER BY t1.dt_prazo_fatal_pro, t1.hr_audiencia_pro
            LIMIT 10
        ", ['conta' => $conta]);

        $processos = array_map(function ($p) {
            $p->hash = \Crypt::encrypt($p->cd_processo_pro);
            return $p;
        }, $processos);

        return response()->json($processos);
    }

    public function escritorioStatus()
    {
        $conta = $this->conta;

        $resultado = DB::select("
            SELECT t2.nm_status_processo_conta_stp, t2.ds_color_stp, COUNT(t1.cd_processo_pro) as total
            FROM processo_pro t1
            JOIN status_processo_stp t2 ON t1.cd_status_processo_stp = t2.cd_status_processo_stp
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.deleted_at IS NULL
            GROUP BY t2.nm_status_processo_conta_stp, t2.ds_color_stp
            ORDER BY total DESC
        ", ['conta' => $conta]);

        return response()->json($resultado);
    }

    public function escritorioPorArea()
    {
        $conta = $this->conta;

        $resultado = DB::select("
            SELECT COALESCE(t2.dc_area_direito_ado, 'Não informada') as dc_area_direito_ado,
                   COUNT(t1.cd_processo_pro) as total
            FROM processo_pro t1
            LEFT JOIN area_direito_ado t2 ON t1.cd_area_direito_ado = t2.cd_area_direito_ado
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.deleted_at IS NULL
            GROUP BY t2.dc_area_direito_ado
            ORDER BY total DESC
        ", ['conta' => $conta]);

        return response()->json($resultado);
    }

    public function escritorioPorTipoProcesso()
    {
        $conta = $this->conta;

        $resultado = DB::select("
            SELECT COALESCE(t2.nm_tipo_processo_tpo, 'Não informado') as nm_tipo_processo_tpo,
                   COUNT(t1.cd_processo_pro) as total
            FROM processo_pro t1
            LEFT JOIN tipo_processo_tpo t2 ON t1.cd_tipo_processo_tpo = t2.cd_tipo_processo_tpo
            WHERE t1.cd_conta_con = :conta
              AND t1.cd_status_processo_stp NOT IN (6, 7, 19)
              AND t1.deleted_at IS NULL
            GROUP BY t2.nm_tipo_processo_tpo
            ORDER BY total DESC
        ", ['conta' => $conta]);

        return response()->json($resultado);
    }

    public function espacoPasta()
    {
        $infoEspaco = $this->calcularEspacoPasta();
        return response()->json($infoEspaco);
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
