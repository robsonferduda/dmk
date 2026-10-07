<?php

namespace App\Http\Controllers\V2;

use App\CampanhaCadastro;
use App\CampanhaCadastroEnvio;
use App\Http\Controllers\Controller;
use App\Services\Cadastro\CampanhaAtualizacaoService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class AtualizacaoCadastralController extends Controller
{
    const POR_PAGINA = 50;
    const PERIODOS = [6, 12, 24];

    /** situação => rótulo */
    const SITUACOES = [
        'aguardando'     => 'Aguardando envio',
        'sem_reacao'     => 'Enviado, sem abertura',
        'abriu'          => 'Abriu, não clicou',
        'clicou'         => 'Clicou, não salvou',
        'confirmou'      => 'Salvou o cadastro',
        'com_pendencias' => 'Salvou, com pendências',
        'prontos'        => 'Pronto para contrato',
        'vencidos'       => 'Prazo vencido',
        'falha'          => 'Falha no envio',
    ];

    /** @var CampanhaAtualizacaoService */
    private $service;

    public function __construct(CampanhaAtualizacaoService $service)
    {
        $this->middleware('auth');
        $this->middleware('can:correspondente.meus-correspondentes');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $campanha = CampanhaCadastro::where('cd_conta_con', $this->conta())
                ->orderByDesc('cd_campanha_cadastro_cmc')
                ->first();
        } catch (QueryException $e) {
            return view('v2.atualizacao-cadastral.index', ['tabelasAusentes' => true]);
        }

        if (! $campanha) {
            $meses = in_array((int) $request->input('meses'), self::PERIODOS, true) ? (int) $request->input('meses') : 24;

            return view('v2.atualizacao-cadastral.index', [
                'campanha'   => null,
                'meses'      => $meses,
                'periodos'   => self::PERIODOS,
                'candidatos' => count($this->service->candidatos($this->conta(), $meses)),
                'limite'     => (int) config('cadastro.limite_diario'),
            ]);
        }

        $situacao = array_key_exists($request->input('situacao'), self::SITUACOES) ? $request->input('situacao') : null;
        $busca = trim((string) $request->input('busca'));

        $consulta = CampanhaCadastroEnvio::where('cd_campanha_cadastro_cmc', $campanha->cd_campanha_cadastro_cmc);
        if ($situacao) {
            $this->filtrarSituacao($consulta, $situacao);
        }
        if ($busca !== '') {
            $consulta->where(function ($q) use ($busca) {
                $q->where('nm_destinatario_cme', 'ilike', '%' . $busca . '%')
                    ->orWhere('dc_email_cme', 'ilike', '%' . $busca . '%');
            });
        }

        $envios = $consulta->with('vinculo:cd_conta_correspondente_ccr,cd_correspondente_cor')
            ->orderByRaw('dt_confirmacao_cme DESC NULLS LAST, dt_clique_cme DESC NULLS LAST, dt_envio_cme DESC NULLS LAST, nu_ordem_cme')
            ->paginate(self::POR_PAGINA)
            ->appends(array_filter(['situacao' => $situacao, 'busca' => $busca]));

        return view('v2.atualizacao-cadastral.index', [
            'campanha'  => $campanha,
            'resumo'    => $this->service->resumo($campanha),
            'serie'     => $this->service->serieDiaria($campanha),
            'envios'    => $envios,
            'situacao'  => $situacao,
            'busca'     => $busca,
            'situacoes' => self::SITUACOES,
        ]);
    }

    public function preparar(Request $request)
    {
        $meses = in_array((int) $request->input('meses'), self::PERIODOS, true) ? (int) $request->input('meses') : 24;

        try {
            $campanha = $this->service->preparar($this->conta(), $meses, auth()->id());
            Flash::success('Lista montada com ' . $campanha->envios()->count() . ' correspondentes. Confira e clique em "Iniciar envio" quando quiser começar.');
        } catch (\RuntimeException $e) {
            Flash::error($e->getMessage());
        }

        return redirect('v2/atualizacao-cadastral');
    }

    public function iniciar()
    {
        $campanha = $this->campanhaAberta();
        $this->service->iniciar($campanha);
        Flash::success('Envio iniciado. Os e-mails saem em lotes ao longo do dia útil, até ' . config('cadastro.limite_diario') . ' por dia.');

        return redirect('v2/atualizacao-cadastral');
    }

    public function pausar()
    {
        $this->service->pausar($this->campanhaAberta());
        Flash::success('Envio pausado. Nenhum novo e-mail sai até retomar.');

        return redirect('v2/atualizacao-cadastral');
    }

    public function reenviarVencidos()
    {
        $campanha = CampanhaCadastro::where('cd_conta_con', $this->conta())->orderByDesc('cd_campanha_cadastro_cmc')->firstOrFail();
        $total = $this->service->reenviarVencidos($campanha);
        if ($total && $campanha->dc_status_cmc === CampanhaCadastro::CONCLUIDA) {
            $this->service->iniciar($campanha);
        }

        Flash::success($total
            ? "{$total} correspondente(s) com prazo vencido voltaram para o início da fila e recebem o e-mail novamente."
            : 'Nenhum correspondente com prazo vencido.');

        return redirect('v2/atualizacao-cadastral');
    }

    public function reenviar($id)
    {
        $envio = CampanhaCadastroEnvio::whereHas('campanha', function ($q) {
            $q->where('cd_conta_con', $this->conta());
        })->findOrFail((int) $id);

        $this->service->reenviar($envio);
        Flash::success('E-mail de ' . $envio->dc_email_cme . ' voltou para o início da fila.');

        return redirect()->back();
    }

    private function filtrarSituacao($consulta, string $situacao): void
    {
        switch ($situacao) {
            case 'aguardando':
                $consulta->where('dc_status_cme', CampanhaCadastroEnvio::PENDENTE)->whereNull('dt_confirmacao_cme');
                break;
            case 'sem_reacao':
                $consulta->whereNotNull('dt_envio_cme')->whereNull('dt_abertura_cme')->whereNull('dt_clique_cme')->whereNull('dt_confirmacao_cme');
                break;
            case 'abriu':
                $consulta->whereNotNull('dt_abertura_cme')->whereNull('dt_clique_cme')->whereNull('dt_confirmacao_cme');
                break;
            case 'clicou':
                $consulta->whereNotNull('dt_clique_cme')->whereNull('dt_confirmacao_cme');
                break;
            case 'confirmou':
                $consulta->whereNotNull('dt_confirmacao_cme');
                break;
            case 'com_pendencias':
                $consulta->whereNotNull('dt_confirmacao_cme')->where('dc_pendencias_cme', '<>', '[]');
                break;
            case 'prontos':
                $consulta->whereNotNull('dt_confirmacao_cme')->where('dc_pendencias_cme', '[]');
                break;
            case 'vencidos':
                $consulta->where('dc_status_cme', CampanhaCadastroEnvio::ENVIADO)->whereNull('dt_confirmacao_cme')->where('dt_prazo_cme', '<', Carbon::today());
                break;
            case 'falha':
                $consulta->where('dc_status_cme', CampanhaCadastroEnvio::FALHA);
                break;
        }
    }

    private function campanhaAberta(): CampanhaCadastro
    {
        $campanha = $this->service->campanhaAberta($this->conta());
        abort_unless($campanha, 404);

        return $campanha;
    }

    private function conta(): int
    {
        return (int) session('SESSION_CD_CONTA');
    }
}
