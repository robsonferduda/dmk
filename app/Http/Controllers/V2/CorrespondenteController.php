<?php

namespace App\Http\Controllers\V2;

use App\Banco;
use App\CategoriaCorrespondente;
use App\CidadeAtuacao;
use App\ContaCorrespondente;
use App\Endereco;
use App\EnderecoEletronico;
use App\Estado;
use App\Exports\Correspondente\RelacaoCorrespondentesEscritorioExport;
use App\Fone;
use App\Http\Controllers\Controller;
use App\Processo;
use App\RegistroBancario;
use App\ReembolsoTipoDespesa;
use App\Services\Contrato\ContratoCorrespondenteGenerator;
use App\Services\Autentique\ContratoAssinaturaService;
use App\Services\Correspondente\AnaliseProcessos;
use App\Services\Correspondente\CorrespondenteBusca;
use App\Services\Correspondente\FotoCorrespondente;
use App\TipoConta;
use App\TipoEnderecoEletronico;
use App\TipoFone;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CorrespondenteController extends Controller
{
    const FILTROS = ['nome', 'cd_categoria_correspondente_cac', 'identificacao', 'cd_estado_est', 'cd_cidade_cde', 'foto'];
    const POR_PAGINA = 50;
    const LIMITE_COMARCAS_LISTADAS = 60;
    const PERIODOS_ANALISE = [12, 24, 36, 60];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:correspondente.meus-correspondentes');
    }

    public function index(Request $request, CorrespondenteBusca $busca, FotoCorrespondente $fotos)
    {
        $filtros = array_filter($request->only(self::FILTROS), function ($valor) {
            return $valor !== null && $valor !== '';
        });
        if (isset($filtros['foto']) && !in_array($filtros['foto'], ['com', 'sem'], true)) {
            unset($filtros['foto']);
        }

        $correspondentes = $busca->buscar($this->conta(), $filtros);

        $totalComFoto = 0;
        foreach ($correspondentes as $correspondente) {
            $correspondente->foto = $fotos->urlPrimeira((string) $correspondente->entidades_usuario);
            $totalComFoto += $correspondente->foto ? 1 : 0;
        }

        if (isset($filtros['foto'])) {
            $comFoto = $filtros['foto'] === 'com';
            $correspondentes = array_values(array_filter($correspondentes, function ($correspondente) use ($comFoto) {
                return (bool) $correspondente->foto === $comFoto;
            }));
        }

        if ($request->filled('exportar')) {
            $dados = ['correspondentes' => $busca->semInativos($correspondentes)];
            return \Excel::download(new RelacaoCorrespondentesEscritorioExport($dados), 'correspondentes.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        $totais = ['todos' => count($correspondentes), 'advogados' => 0, 'prepostos' => 0, 'sem_atuacao' => 0];
        foreach ($correspondentes as $correspondente) {
            $flag = $this->flagAdvogado($correspondente->fl_advogado_con);
            $correspondente->atuacao = $flag;
            $correspondente->grupo = $flag === true ? 'advogados' : ($flag === false ? 'prepostos' : 'sem_atuacao');
            $totais[$correspondente->grupo]++;
        }

        $grupo = array_key_exists($request->get('atuacao'), $totais) ? $request->get('atuacao') : 'todos';
        if ($grupo !== 'todos') {
            $correspondentes = array_values(array_filter($correspondentes, function ($correspondente) use ($grupo) {
                return $correspondente->grupo === $grupo;
            }));
        }

        $porPagina = self::POR_PAGINA;
        $pagina = LengthAwarePaginator::resolveCurrentPage();
        $itens = array_slice($correspondentes, ($pagina - 1) * $porPagina, $porPagina);
        foreach ($itens as $correspondente) {
            $correspondente->id_safe = safe_encrypt($correspondente->cd_correspondente_cor);
            $correspondente->id_crypt = \Crypt::encrypt($correspondente->cd_correspondente_cor);
        }

        $paginacao = new LengthAwarePaginator($itens, count($correspondentes), $porPagina, $pagina, [
            'path'  => url('v2/correspondentes'),
            'query' => array_merge($filtros, $grupo !== 'todos' ? ['atuacao' => $grupo] : []),
        ]);

        return view('v2.correspondente.index', [
            'correspondentes' => $paginacao,
            'filtros'         => $filtros,
            'grupo'           => $grupo,
            'totais'          => $totais,
            'totalComFoto'    => $totalComFoto,
            'categorias'      => $this->categorias(),
            'estados'         => Estado::orderBy('nm_estado_est')->get(),
        ]);
    }

    public function show($id, FotoCorrespondente $fotos)
    {
        $vinculo = $this->vinculo($id);
        $entidade = $vinculo->entidade;
        $cdEntidade = $vinculo->cd_entidade_ete;

        $comarcasPorUf = DB::table('cidade_atuacao_cat as cat')
            ->join('cidade_cde as cde', 'cde.cd_cidade_cde', '=', 'cat.cd_cidade_cde')
            ->join('estado_est as est', 'est.cd_estado_est', '=', 'cde.cd_estado_est')
            ->where('cat.cd_entidade_ete', $cdEntidade)
            ->whereNull('cat.deleted_at')
            ->groupBy('est.sg_estado_est')
            ->orderBy('est.sg_estado_est')
            ->selectRaw('est.sg_estado_est, count(*) AS total')
            ->get();

        $totalComarcas = $comarcasPorUf->sum('total');

        $comarcas = $totalComarcas <= self::LIMITE_COMARCAS_LISTADAS
            ? CidadeAtuacao::with('cidade.estado')->where('cd_entidade_ete', $cdEntidade)->get()
                ->sortBy(function ($atuacao) {
                    return ($atuacao->fl_origem_cat === 'S' ? '0' : '1') . optional($atuacao->cidade)->nm_cidade_cde;
                })
            : collect();

        $origem = CidadeAtuacao::with('cidade.estado')
            ->where('cd_entidade_ete', $cdEntidade)
            ->where('fl_origem_cat', 'S')
            ->first();

        return view('v2.correspondente.show', [
            'vinculo'            => $vinculo,
            'entidade'           => $entidade,
            'endereco'           => Endereco::with('cidade.estado')->where('cd_entidade_ete', $cdEntidade)->first(),
            'fones'              => Fone::with('tipo')->where('cd_entidade_ete', $cdEntidade)->orderBy('cd_fone_fon')->get(),
            'emails'             => EnderecoEletronico::with('tipo')->where('cd_entidade_ete', $cdEntidade)->get(),
            'bancos'             => RegistroBancario::with(['banco', 'tipoConta'])->where('cd_entidade_ete', $cdEntidade)->get(),
            'despesas'           => ReembolsoTipoDespesa::with('tipoDespesa')->where('cd_entidade_ete', $cdEntidade)->get(),
            'comarcas'           => $comarcas,
            'comarcasPorUf'      => $comarcasPorUf,
            'totalComarcas'      => $totalComarcas,
            'origem'             => $origem,
            'flAdvogado'         => $this->flagAdvogado(optional($vinculo->correspondente)->fl_advogado_con),
            'contratoPendencias' => app(ContratoCorrespondenteGenerator::class)->pendenciasGeracao($vinculo),
            'assinaturaPainel'   => app(ContratoAssinaturaService::class)->painel($vinculo),
            'idCrypt'            => \Crypt::encrypt($vinculo->cd_correspondente_cor),
            'idSafe'             => safe_encrypt($vinculo->cd_correspondente_cor),
            'foto'               => $fotos->urlPorConta($vinculo->cd_correspondente_cor),
        ]);
    }

    public function edit($id, FotoCorrespondente $fotos)
    {
        $vinculo = $this->vinculo($id);
        $cdEntidade = $vinculo->cd_entidade_ete;

        return view('v2.correspondente.edit', [
            'vinculo'    => $vinculo,
            'entidade'   => $vinculo->entidade,
            'endereco'   => Endereco::with('cidade')->where('cd_entidade_ete', $cdEntidade)->first(),
            'fones'      => Fone::with('tipo')->where('cd_entidade_ete', $cdEntidade)->orderBy('cd_fone_fon')->get(),
            'emails'     => EnderecoEletronico::with('tipo')->where('cd_entidade_ete', $cdEntidade)->get(),
            'bancos'     => RegistroBancario::with(['banco', 'tipoConta'])->where('cd_entidade_ete', $cdEntidade)->get(),
            'flAdvogado' => $this->flagAdvogado(old('fl_advogado_con', optional($vinculo->correspondente)->fl_advogado_con)),
            'categorias' => $this->categorias(),
            'estados'    => Estado::orderBy('nm_estado_est')->get(),
            'tiposFone'  => TipoFone::all(),
            'tiposEmail' => TipoEnderecoEletronico::all(),
            'tiposConta' => TipoConta::all(),
            'listaBancos'=> Banco::orderBy('nm_banco_ban')->get(),
            'idSafe'     => safe_encrypt($vinculo->cd_correspondente_cor),
            'foto'       => $fotos->urlPorConta($vinculo->cd_correspondente_cor),
        ]);
    }

    public function processos(Request $request, $id, FotoCorrespondente $fotos)
    {
        $vinculo = $this->vinculo($id);
        $meses = in_array((int) $request->get('meses'), self::PERIODOS_ANALISE, true) ? (int) $request->get('meses') : 24;

        $analise = new AnaliseProcessos($this->conta(), $vinculo->cd_correspondente_cor);
        $serie = $analise->serieMensal($meses);
        $tendencia = $analise->tendencia($serie);

        $filtros = array_filter($request->only(['situacao', 'de', 'ate', 'busca']), function ($valor) {
            return $valor !== null && $valor !== '';
        });

        $processos = Processo::with(['status', 'cidade.estado', 'cliente', 'honorario.tipoServicoCorrespondente'])
            ->where('cd_conta_con', $this->conta())
            ->where('cd_correspondente_cor', $vinculo->cd_correspondente_cor);

        $situacao = $filtros['situacao'] ?? null;
        if ($situacao === 'finalizado') {
            $processos->whereIn('cd_status_processo_stp', AnaliseProcessos::FINALIZADOS);
        } elseif ($situacao === 'cancelado') {
            $processos->whereIn('cd_status_processo_stp', AnaliseProcessos::CANCELADOS);
        } elseif ($situacao === 'andamento') {
            $processos->whereNotIn('cd_status_processo_stp', array_merge(AnaliseProcessos::FINALIZADOS, AnaliseProcessos::CANCELADOS));
        }

        foreach (['de' => '>=', 'ate' => '<='] as $campo => $operador) {
            if (!empty($filtros[$campo]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filtros[$campo])) {
                $processos->where('dt_prazo_fatal_pro', $operador, $filtros[$campo]);
            }
        }

        if (!empty($filtros['busca'])) {
            $termo = '%' . $filtros['busca'] . '%';
            $processos->where(function ($q) use ($termo) {
                $q->where('nu_processo_pro', 'ilike', $termo)
                    ->orWhere('nu_acompanhamento_pro', 'ilike', $termo)
                    ->orWhere('nm_autor_pro', 'ilike', $termo)
                    ->orWhere('nm_reu_pro', 'ilike', $termo);
            });
        }

        $processos = $processos->orderBy('dt_prazo_fatal_pro', 'desc')
            ->orderBy('cd_processo_pro', 'desc')
            ->paginate(25)
            ->appends(array_merge($filtros, ['meses' => $meses]));

        return view('v2.correspondente.processos', [
            'vinculo'    => $vinculo,
            'idSafe'     => safe_encrypt($vinculo->cd_correspondente_cor),
            'idCrypt'    => \Crypt::encrypt($vinculo->cd_correspondente_cor),
            'foto'       => $fotos->urlPorConta($vinculo->cd_correspondente_cor),
            'meses'      => $meses,
            'periodos'   => self::PERIODOS_ANALISE,
            'serie'      => $serie,
            'tendencia'  => $tendencia,
            'leitura'    => $analise->leitura($tendencia, $serie),
            'resumo'     => $analise->resumo(),
            'servicos'   => $analise->ranking('servico', $meses),
            'comarcas'   => $analise->ranking('comarca', $meses),
            'clientes'   => $analise->ranking('cliente', $meses, 5),
            'processos'  => $processos,
            'filtros'    => $filtros,
        ]);
    }

    private function conta()
    {
        return (int) session('SESSION_CD_CONTA');
    }

    private function vinculo($id)
    {
        try {
            $cdCorrespondente = safe_decrypt($id);
        } catch (\Throwable $e) {
            abort(404);
        }

        return ContaCorrespondente::with(['entidade', 'correspondente', 'categoria', 'tipoPessoa'])
            ->where('cd_conta_con', $this->conta())
            ->where('cd_correspondente_cor', $cdCorrespondente)
            ->firstOrFail();
    }

    private function categorias()
    {
        return CategoriaCorrespondente::where('cd_conta_con', $this->conta())
            ->orderBy('dc_categoria_correspondente_cac')
            ->get();
    }

    /**
     * fl_advogado_con pode vir como bool (Eloquent) ou 't'/'f' (DB::select no PostgreSQL).
     */
    private function flagAdvogado($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_string($valor)) {
            return in_array(strtolower($valor), ['t', 'true', '1', 's'], true);
        }

        return (bool) $valor;
    }
}
