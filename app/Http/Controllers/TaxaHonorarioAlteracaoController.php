<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Processo;
use App\TaxaHonorario;
use App\TaxaHonorarioAlteracao;
use App\ProcessoTaxaHonorario;
use App\Cliente;
use App\Notifications\HonorarioAlteracaoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class TaxaHonorarioAlteracaoController extends Controller
{
    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
        Session::put('menu_pai', 'processos');
        Session::put('item_pai', 'honorario.alteracao');
    }

    /**
     * Cliente solicita alteração do valor do honorário de um processo.
     */
    public function solicitar(Request $request)
    {
        $request->validate([
            'cd_processo_pro' => 'required|integer',
            'nu_valor_novo'   => 'required|numeric|min:0',
        ]);

        $processo = Processo::findOrFail($request->cd_processo_pro);

        if (!$processo->honorario) {
            Flash::error('Este processo não possui honorário cadastrado.');
            return back();
        }

        // Localiza o registro base de honorário (taxa_honorario_entidade_the)
        $clienteEntidade = null;
        if ($processo->cliente) {
            $clienteEntidade = $processo->cliente->cd_entidade_ete ?? null;
        }

        $taxaBase = TaxaHonorario::where('cd_conta_con', $processo->cd_conta_con)
            ->where('cd_tipo_servico_tse', $processo->honorario->cd_tipo_servico_tse)
            ->when($clienteEntidade, fn($q) => $q->where('cd_entidade_ete', $clienteEntidade))
            ->when($processo->cd_cidade_cde, fn($q) => $q->where('cd_cidade_cde', $processo->cd_cidade_cde))
            ->first();

        TaxaHonorarioAlteracao::create([
            'cd_taxa_honorario_entidade_the' => $taxaBase ? $taxaBase->cd_taxa_honorario_entidade_the : null,
            'cd_processo_pro'                => $processo->cd_processo_pro,
            'nu_valor_antigo_tha'            => $processo->honorario->vl_taxa_honorario_cliente_pth,
            'nu_valor_novo_tha'              => $request->nu_valor_novo,
            'fl_aceito_tha'                  => null,
        ]);

        // Notifica o escritório
        $usuario = User::where('cd_conta_con', $processo->cd_conta_con)
            ->where('cd_nivel_niv', 1)
            ->first();

        if ($usuario) {
            $alteracao = TaxaHonorarioAlteracao::where('cd_processo_pro', $processo->cd_processo_pro)
                ->whereNull('fl_aceito_tha')
                ->latest()
                ->first();

            $usuario->notify(new HonorarioAlteracaoNotification($alteracao));
        }

        Flash::success('Pedido de alteração enviado com sucesso. O escritório será notificado.');
        return back();
    }

    /**
     * Listagem de pedidos de alteração para o escritório.
     */
    public function index()
    {
        $alteracoes = TaxaHonorarioAlteracao::with(['processo', 'processo.honorario', 'processo.honorario.tipoServico'])
            ->whereHas('processo', fn($q) => $q->where('cd_conta_con', $this->cdContaCon))
            ->orderByRaw('fl_aceito_tha IS NOT NULL, created_at DESC')
            ->paginate(20);

        return view('processo.honorario-alteracao.index', compact('alteracoes'));
    }

    /**
     * Detalhe de um pedido — escritório aprova ou reprova.
     */
    public function show($id)
    {
        $alteracao = TaxaHonorarioAlteracao::with(['processo', 'processo.honorario', 'processo.honorario.tipoServico', 'taxaHonorario'])
            ->whereHas('processo', fn($q) => $q->where('cd_conta_con', $this->cdContaCon))
            ->findOrFail($id);

        return view('processo.honorario-alteracao.show', compact('alteracao'));
    }

    /**
     * Aprova o pedido e atualiza valores conforme opções escolhidas.
     */
    public function aprovar(Request $request, $id)
    {
        $alteracao = TaxaHonorarioAlteracao::whereHas('processo', fn($q) => $q->where('cd_conta_con', $this->cdContaCon))
            ->findOrFail($id);

        if (!$alteracao->isPendente()) {
            Flash::warning('Este pedido já foi processado.');
            return redirect('processos/honorario-alteracao');
        }

        // 1. Marca como aprovado
        $alteracao->fl_aceito_tha = true;
        $alteracao->save();

        // 2. Atualiza o valor base na taxa_honorario_entidade_the
        if ($alteracao->taxaHonorario) {
            $alteracao->taxaHonorario->nu_taxa_the = $alteracao->nu_valor_novo_tha;
            $alteracao->taxaHonorario->save();
        }

        // 3. Atualiza o processo originador, se solicitado
        if ($request->boolean('atualizar_processo_origem') && $alteracao->cd_processo_pro) {
            ProcessoTaxaHonorario::where('cd_processo_pro', $alteracao->cd_processo_pro)
                ->update(['vl_taxa_honorario_cliente_pth' => $alteracao->nu_valor_novo_tha]);
        }

        // 4. Atualiza processos futuros da mesma conta + tipo de serviço, se solicitado
        if ($request->boolean('atualizar_processos_futuros') && $alteracao->processo && $alteracao->processo->honorario) {
            $tipoServico = $alteracao->processo->honorario->cd_tipo_servico_tse;
            $contaCon    = $alteracao->processo->cd_conta_con;

            ProcessoTaxaHonorario::where('cd_conta_con', $contaCon)
                ->where('cd_tipo_servico_tse', $tipoServico)
                ->whereHas('processo', function ($q) {
                    $q->where('dt_prazo_fatal_pro', '>=', now()->toDateString())
                      ->whereNotIn('cd_status_processo_stp', [
                          \App\Enums\StatusProcesso::FINALIZADO,
                          \App\Enums\StatusProcesso::CANCELADO,
                      ]);
                })
                ->update(['vl_taxa_honorario_cliente_pth' => $alteracao->nu_valor_novo_tha]);
        }

        Flash::success('Pedido aprovado com sucesso.');
        return redirect('processos/honorario-alteracao');
    }

    /**
     * Reprova o pedido — nenhuma alteração de valor é feita.
     */
    public function reprovar($id)
    {
        $alteracao = TaxaHonorarioAlteracao::whereHas('processo', fn($q) => $q->where('cd_conta_con', $this->cdContaCon))
            ->findOrFail($id);

        if (!$alteracao->isPendente()) {
            Flash::warning('Este pedido já foi processado.');
            return redirect('processos/honorario-alteracao');
        }

        $alteracao->fl_aceito_tha = false;
        $alteracao->save();

        Flash::success('Pedido reprovado.');
        return redirect('processos/honorario-alteracao');
    }
}
