<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Session;
use App\Conta;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\ProcessoTaxaHonorario;
use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\TipoEnderecoEletronico;
use App\Services\WhatsappDispatcher;
use App\Services\Pagamento\PagamentoCorrespondenteRefreshService;
use Illuminate\Support\Facades\Mail;
use App\Exports\Correspondente\PagamentosCorrespondenteRelatorioExport;

class CorrespondentePagamentoController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth')->except(['confirmarPorToken']);
        $this->conta = \Session::get('SESSION_CD_CONTA');
        Session::put('menu_pai','correspondente');
        Session::forget('item_pai');
    }

    // ─── Listagem ─────────────────────────────────────────────────────────────

    public function pagamentos(Request $request)
    {
        Session::put('item_pai','correspondente.pagamentos');
        
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $competencia = ($mes >= 1 && $mes <= 12 && $ano >= 2000)
            ? Carbon::createFromDate($ano, $mes, 1)->startOfMonth()
            : null;

        if (! $competencia || $competencia->gt(Carbon::now()->startOfMonth())) {
            $mes = Carbon::now()->month;
            $ano = Carbon::now()->year;
        }

        $pagamentos = PagamentoCorrespondente::with('correspondente')
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->where('vl_total_pag', '>', 0)
            ->get()
            ->sortBy(function ($pag) {
                return $pag->correspondente->nm_razao_social_con
                    ?? $pag->correspondente->nm_fantasia_con
                    ?? 'zzz';
            })
            ->values();

        $statusLabels   = StatusPagamentoCorrespondente::labels();
        $mesAnoAtual    = Carbon::now()->format('m/Y');
        $mesesNavegacao = $this->mesesDisponiveis();

        $bancoPorPag = [];
        foreach ($pagamentos as $pag) {
            $bancoPorPag[$pag->cd_pagamento_correspondente_pag] =
                $this->buscarDadosBancarios($pag->cd_correspondente_cor);
        }

        return view('correspondente/pagamentos', compact(
            'pagamentos', 'mes', 'ano', 'statusLabels', 'mesAnoAtual', 'mesesNavegacao', 'bancoPorPag'
        ));
    }

    // ─── Detalhe ──────────────────────────────────────────────────────────────

    public function detalhe($id)
    {
        $pagamento = PagamentoCorrespondente::with(['itens.processo', 'correspondente'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        $statusLabels = StatusPagamentoCorrespondente::labels();

        $banco = $this->buscarDadosBancarios($pagamento->cd_correspondente_cor);

        return view('correspondente/pagamento-detalhe', compact('pagamento', 'statusLabels', 'banco'));
    }

    // ─── Consolidar manualmente ───────────────────────────────────────────────

    public function consolidar(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $competencia = ($mes >= 1 && $mes <= 12 && $ano >= 2000)
            ? Carbon::createFromDate($ano, $mes, 1)->startOfMonth()
            : null;

        if (! $competencia || $competencia->gt(Carbon::now()->startOfMonth())) {
            Flash::error('Não é permitido consolidar pagamentos de competências inválidas ou futuras.');

            return redirect(url('correspondente/pagamentos'));
        }

        \Artisan::call('pagamentos:consolidar', [
            '--mes'   => $mes,
            '--ano'   => $ano,
            '--conta' => $this->conta,
        ]);

        $output = trim(\Artisan::output());

        Flash::success('Pagamentos consolidados com sucesso. ' . $output);

        return redirect(url("correspondente/pagamentos?mes={$mes}&ano={$ano}"));
    }

    // ─── Enviar para Aprovação ────────────────────────────────────────────────

    public function enviarAprovacao($id)
    {
        $pagamento = PagamentoCorrespondente::with(['correspondente', 'itens.processo'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        if (! $pagamento->podeNotificarAprovacao()) {
            Flash::error('Este pagamento não pode ser enviado para aprovação no status atual.');
            return redirect()->back();
        }

        if ((float) $pagamento->vl_total_pag <= 0) {
            Flash::error('Este pagamento está sem valor a pagar e não pode ser enviado para aprovação.');
            return redirect()->back();
        }

        $isReenvio = $pagamento->podeReenviarAprovacao();

        DB::transaction(function () use ($pagamento, $isReenvio) {
            // Gera (ou regenera) o token de confirmação por link
            $pagamento->tk_confirmacao_pag      = Str::random(64);
            if (! $isReenvio) {
                $pagamento->cd_status_pag       = StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
            }
            $pagamento->dt_envio_aprovacao_pag  = Carbon::now();
            $pagamento->save();
        });

        // Notifica via e-mail e WhatsApp (com PDF e link de confirmação)
        $this->notificarAprovacao($pagamento);

        Flash::success($isReenvio
            ? 'Notificação reenviada para o correspondente (e-mail e WhatsApp).'
            : 'Pagamento enviado para aprovação do correspondente.');
        return redirect()->back();
    }

    // ─── Enviar para Aprovação em Massa ───────────────────────────────────────

    public function enviarAprovacaoTodos(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $pagamentos = PagamentoCorrespondente::with(['correspondente', 'itens.processo'])
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->where('vl_total_pag', '>', 0)
            ->get()
            ->filter(fn($p) => $p->podeEnviarAprovacao());

        if ($pagamentos->isEmpty()) {
            Flash::warning('Nenhum pagamento elegível para envio neste mês.');
            return redirect()->back();
        }

        $enviados = 0;
        foreach ($pagamentos as $pagamento) {
            DB::transaction(function () use ($pagamento) {
                $pagamento->tk_confirmacao_pag     = Str::random(64);
                $pagamento->cd_status_pag          = StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
                $pagamento->dt_envio_aprovacao_pag = Carbon::now();
                $pagamento->save();
            });

            $this->notificarAprovacao($pagamento);
            $enviados++;
        }

        Flash::success("Notificação enviada para {$enviados} correspondente(s).");
        return redirect(url("correspondente/pagamentos?mes={$mes}&ano={$ano}"));
    }

    // ─── Reenviar para Aprovação em Massa ─────────────────────────────────────

    public function reenviarAprovacaoTodos(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $pagamentos = PagamentoCorrespondente::with(['correspondente', 'itens.processo'])
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->where('vl_total_pag', '>', 0)
            ->get()
            ->filter(fn($p) => $p->podeReenviarAprovacao());

        if ($pagamentos->isEmpty()) {
            Flash::warning('Nenhum pagamento pendente de aprovação para reenvio neste mês.');
            return redirect()->back();
        }

        $reenviados = 0;
        foreach ($pagamentos as $pagamento) {
            DB::transaction(function () use ($pagamento) {
                $pagamento->tk_confirmacao_pag     = Str::random(64);
                $pagamento->dt_envio_aprovacao_pag = Carbon::now();
                $pagamento->save();
            });

            $this->notificarAprovacao($pagamento);
            $reenviados++;
        }

        Flash::success("Notificação reenviada para {$reenviados} correspondente(s).");
        return redirect(url("correspondente/pagamentos?mes={$mes}&ano={$ano}"));
    }

    // ─── Confirmação por Token (pública, sem login) ───────────────────────────

    public function confirmarPorToken($token)
    {
        $pagamento = PagamentoCorrespondente::with('correspondente')
            ->where('tk_confirmacao_pag', $token)
            ->first();

        if (! $pagamento) {
            return view('correspondente/pagamento-confirmado', ['status' => 'nao_encontrado']);
        }

        if ($pagamento->cd_status_pag == StatusPagamentoCorrespondente::PAGO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'ja_pago', 'pagamento' => $pagamento]);
        }

        if ($pagamento->cd_status_pag == StatusPagamentoCorrespondente::APROVADO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'ja_aprovado', 'pagamento' => $pagamento]);
        }

        if ($pagamento->cd_status_pag != StatusPagamentoCorrespondente::ENVIADO_APROVACAO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'invalido']);
        }

        DB::transaction(function () use ($pagamento) {
            $pagamento->cd_status_pag    = StatusPagamentoCorrespondente::APROVADO;
            $pagamento->dt_aprovacao_pag = Carbon::now();
            $pagamento->save();
        });

        return view('correspondente/pagamento-confirmado', ['status' => 'ok', 'pagamento' => $pagamento->fresh('correspondente')]);
    }

    // ─── Revisar por Token (página pública com listagem de itens) ─────────────

    public function revisarPorToken($token)
    {
        $pagamento = PagamentoCorrespondente::with(['correspondente', 'itens'])
            ->where('tk_confirmacao_pag', $token)
            ->first();

        if (! $pagamento) {
            return view('correspondente/pagamento-confirmado', ['status' => 'nao_encontrado']);
        }

        if ($pagamento->cd_status_pag == StatusPagamentoCorrespondente::PAGO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'ja_pago', 'pagamento' => $pagamento]);
        }

        if ($pagamento->cd_status_pag == StatusPagamentoCorrespondente::APROVADO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'ja_aprovado', 'pagamento' => $pagamento]);
        }

        if ($pagamento->cd_status_pag == StatusPagamentoCorrespondente::RECUSADO) {
            return view('correspondente/pagamento-confirmado', ['status' => 'ja_recusado', 'pagamento' => $pagamento]);
        }

        // Aceita ENVIADO_APROVACAO (fluxo normal) ou GERADO (link de teste sem alterar status)
        $statusPermitidos = [
            StatusPagamentoCorrespondente::ENVIADO_APROVACAO,
            StatusPagamentoCorrespondente::GERADO,
        ];

        if (! in_array($pagamento->cd_status_pag, $statusPermitidos)) {
            return view('correspondente/pagamento-confirmado', ['status' => 'invalido']);
        }

        return view('correspondente/pagamento-revisao', compact('pagamento', 'token'));
    }

    // ─── Processar Revisão (aprovar ou recusar via token público) ─────────────

    public function processarRevisao(Request $request, $token)
    {
        $pagamento = PagamentoCorrespondente::with('correspondente')
            ->where('tk_confirmacao_pag', $token)
            ->first();

        if (! $pagamento) {
            return view('correspondente/pagamento-confirmado', ['status' => 'nao_encontrado']);
        }

        $statusPermitidos = [
            StatusPagamentoCorrespondente::ENVIADO_APROVACAO,
            StatusPagamentoCorrespondente::GERADO,
        ];

        if (! in_array($pagamento->cd_status_pag, $statusPermitidos)) {
            return view('correspondente/pagamento-confirmado', ['status' => 'invalido']);
        }

        $acao = $request->input('acao');

        if ($acao === 'aprovar') {
            DB::transaction(function () use ($pagamento) {
                $pagamento->cd_status_pag    = StatusPagamentoCorrespondente::APROVADO;
                $pagamento->dt_aprovacao_pag = Carbon::now();
                $pagamento->save();
            });

            return view('correspondente/pagamento-confirmado', ['status' => 'ok', 'pagamento' => $pagamento->fresh('correspondente')]);
        }

        if ($acao === 'recusar') {
            $motivo = trim($request->input('motivo', ''));

            DB::transaction(function () use ($pagamento, $motivo) {
                $pagamento->cd_status_pag     = StatusPagamentoCorrespondente::RECUSADO;
                $pagamento->ds_observacao_pag = $motivo;
                $pagamento->save();
            });

            return view('correspondente/pagamento-confirmado', ['status' => 'recusado', 'pagamento' => $pagamento->fresh('correspondente')]);
        }

        return view('correspondente/pagamento-confirmado', ['status' => 'invalido']);
    }

    // ─── Aprovar (pelo escritório em nome do correspondente) ──────────────────

    public function aprovar($id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if (! $pagamento->podeAprovar()) {
            Flash::error('Este pagamento não pode ser aprovado no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento) {
            $pagamento->cd_status_pag     = StatusPagamentoCorrespondente::APROVADO;
            $pagamento->dt_aprovacao_pag  = Carbon::now();
            $pagamento->save();
        });

        Flash::success('Pagamento aprovado. Disponível para efetuar o pagamento.');
        return redirect()->back();
    }

    // ─── Atualizar Valores (reconciliar com processos) ────────────────────────

    public function atualizarValores($id)
    {
        $pagamento = PagamentoCorrespondente::with('itens')
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        if (! $pagamento->podeAtualizarValores()) {
            Flash::error('Pagamentos já efetuados não podem ser atualizados.');
            return redirect()->back();
        }

        $stats = app(PagamentoCorrespondenteRefreshService::class)->refreshPagamento($pagamento);

        if (isset($stats['erro'])) {
            Flash::error($stats['erro']);
            return redirect()->back();
        }

        if (! empty($stats['descartado'])) {
            Flash::warning('Este pagamento ficou sem valor a pagar após a atualização e foi removido da competência.');

            return redirect(url(
                'correspondente/pagamentos?mes=' . $pagamento->nu_mes_pag . '&ano=' . $pagamento->nu_ano_pag
            ));
        }

        $mensagem = sprintf(
            'Valores atualizados: %d adicionado(s), %d atualizado(s), %d removido(s), %d excluído(s) por cancelamento, %d duplicado(s) removido(s).',
            $stats['adicionados'],
            $stats['atualizados'],
            $stats['removidos'],
            $stats['excluidos'],
            $stats['duplicados'] ?? 0
        );

        Flash::success($mensagem);
        return redirect()->back();
    }

    // ─── Atualizar Itens (quando Recusado) ────────────────────────────────────

    public function atualizarItens(Request $request, $id)
    {
        $pagamento = PagamentoCorrespondente::with('itens')
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        if ($pagamento->cd_status_pag !== StatusPagamentoCorrespondente::RECUSADO) {
            Flash::error('Edição de itens só é permitida para pagamentos recusados.');
            return redirect()->back();
        }

        $request->validate([
            'itens'                  => 'required|array',
            'itens.*.vl_honorario'   => 'required|numeric|min:0',
            'itens.*.vl_despesa'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($pagamento, $request) {
            $total = 0;

            foreach ($pagamento->itens as $item) {
                $key = $item->cd_pagamento_correspondente_item_pai;
                if (! isset($request->itens[$key])) {
                    continue;
                }

                $dadosItem   = $request->itens[$key];
                $honorario   = (float) $dadosItem['vl_honorario'];
                $despesa     = (float) $dadosItem['vl_despesa'];
                $incluir     = ! empty($dadosItem['incluir']);

                $item->vl_honorario_pai = $honorario;
                $item->vl_despesa_pai   = $despesa;
                $item->fl_excluido_pai  = $incluir ? 'N' : 'S';
                $item->save();

                if ($incluir) {
                    // Sincroniza o honorário de volta à tabela de origem
                    if ($item->cd_processo_taxa_honorario_pth) {
                        ProcessoTaxaHonorario::where('cd_processo_taxa_honorario_pth', $item->cd_processo_taxa_honorario_pth)
                            ->update(['vl_taxa_honorario_correspondente_pth' => $honorario]);
                    }

                    $total += $honorario + $despesa;
                }
            }

            $pagamento->vl_total_pag = $total;
            $pagamento->save();
        });

        Flash::success('Itens atualizados com sucesso. Valor total recalculado.');
        return redirect()->back();
    }

    // ─── Atualizar Dados Bancários (quando Recusado) ──────────────────────────

    public function atualizarDadosBancarios(Request $request, $id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if ($pagamento->cd_status_pag !== StatusPagamentoCorrespondente::RECUSADO) {
            Flash::error('Edição de dados bancários só é permitida para pagamentos recusados.');
            return redirect()->back();
        }

        $request->validate([
            'nm_titular_dba' => 'required|string|max:255',
            'nu_cpf_cnpj_dba'=> 'nullable|string|max:20',
            'cd_banco_ban'   => 'nullable|string|max:10',
            'nu_agencia_dba' => 'nullable|string|max:20',
            'nu_conta_dba'   => 'nullable|string|max:30',
            'cd_tipo_conta_tcb' => 'nullable|string|max:5',
            'dc_pix_dba'     => 'nullable|string|max:255',
        ]);

        $banco = $this->buscarDadosBancarios($pagamento->cd_correspondente_cor);

        if ($banco && $banco->cd_dados_bancarios_dba) {
            \App\RegistroBancario::where('cd_dados_bancarios_dba', $banco->cd_dados_bancarios_dba)
                ->update([
                    'nm_titular_dba'    => $request->nm_titular_dba,
                    'nu_cpf_cnpj_dba'   => \Helper::limparCnpjCpf($request->nu_cpf_cnpj_dba ?? ''),
                    'cd_banco_ban'      => $request->cd_banco_ban,
                    'nu_agencia_dba'    => $request->nu_agencia_dba,
                    'nu_conta_dba'      => $request->nu_conta_dba,
                    'cd_tipo_conta_tcb' => $request->cd_tipo_conta_tcb ?: null,
                    'dc_pix_dba'        => $request->dc_pix_dba,
                ]);
        } else {
            // Busca a entidade do correspondente para criar um novo registro bancário
            $entidade = DB::table('conta_correspondente_ccr')
                ->where('cd_correspondente_cor', $pagamento->cd_correspondente_cor)
                ->value('cd_entidade_ete');

            \App\RegistroBancario::create([
                'cd_entidade_ete'   => $entidade,
                'cd_conta_con'      => $this->conta,
                'nm_titular_dba'    => $request->nm_titular_dba,
                'nu_cpf_cnpj_dba'   => \Helper::limparCnpjCpf($request->nu_cpf_cnpj_dba ?? ''),
                'cd_banco_ban'      => $request->cd_banco_ban,
                'nu_agencia_dba'    => $request->nu_agencia_dba,
                'nu_conta_dba'      => $request->nu_conta_dba,
                'cd_tipo_conta_tcb' => $request->cd_tipo_conta_tcb ?: null,
                'dc_pix_dba'        => $request->dc_pix_dba,
            ]);
        }

        Flash::success('Dados bancários atualizados com sucesso.');
        return redirect()->back();
    }

    // ─── Marcar como Pago ─────────────────────────────────────────────────────

    public function pagar(Request $request, $id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if (! $pagamento->podePagar()) {
            Flash::error('Este pagamento não pode ser marcado como pago no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento, $request) {
            $pagamento->cd_status_pag     = StatusPagamentoCorrespondente::PAGO;
            $pagamento->dt_pagamento_pag  = Carbon::now();
            $pagamento->ds_observacao_pag = $request->observacao;

            if ($request->hasFile('comprovante') && $request->file('comprovante')->isValid()) {
                $dir  = 'comprovantes-pagamento';
                $path = $request->file('comprovante')->store($dir, 'public');
                $pagamento->dc_comprovante_pag = $path;
            }

            $pagamento->save();
        });

        Flash::success('Pagamento registrado com sucesso.');
        return redirect()->back();
    }

    // ─── Teste de Notificação ────────────────────────────────────────────────

    public function testarNotificacao($id)
    {
        $pagamento = PagamentoCorrespondente::with(['correspondente', 'itens.processo'])
            ->where('cd_conta_con', $this->conta)
            ->findOrFail($id);

        // Garante que há token (sem alterar status)
        if (! $pagamento->tk_confirmacao_pag) {
            $pagamento->tk_confirmacao_pag = Str::random(64);
            $pagamento->save();
        }

        $banco        = $this->buscarDadosBancarios($pagamento->cd_correspondente_cor);
        $escritorio   = Conta::find($this->conta);
        $token        = $pagamento->tk_confirmacao_pag;

        $emailTeste    = 'robsonferduda@gmail.com';
        $whatsappTeste = '48991030204';
        $msgs = [];

        // Gera PDF
        try {
            $pdfPath = $this->gerarPdfFatura($pagamento, $banco, $escritorio);
            $msgs[] = 'PDF gerado em: ' . $pdfPath;
        } catch (\Throwable $e) {
            $errDetail = $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine();
            \Log::error('[pagamento-teste] Falha ao gerar PDF: ' . $errDetail, ['trace' => $e->getTraceAsString()]);
            file_put_contents(storage_path('app/public/pdf-debug.txt'), date('Y-m-d H:i:s') . "\n" . $errDetail . "\n\n" . $e->getTraceAsString());
            $pdfPath = null;
            $msgs[] = 'ERRO PDF: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')';
        }

        // E-mail de teste
        try {
            $enviadoEmail = $this->enviarEmailAprovacaoPagamento(
                $pagamento,
                $escritorio,
                $banco,
                $token,
                $pdfPath ?? null,
                collect([$emailTeste]),
                true
            );
            $msgs[] = $enviadoEmail
                ? 'E-mail enviado para ' . $emailTeste . ($pdfPath ? ' (com PDF em anexo)' : ' (sem PDF — verifique geração)')
                : 'Falha ao enviar e-mail de teste';
        } catch (\Throwable $e) {
            \Log::warning('[pagamento-teste] Falha ao enviar e-mail: ' . $e->getMessage());
            $msgs[] = 'Falha no e-mail: ' . $e->getMessage();
        }

        // WhatsApp de teste: mensagem de texto com link de revisão
        try {
            $conta    = Conta::find($this->conta);
            $mensagem = $this->montarMensagemWhatsApp($pagamento, $banco, $token);
            $mensagem = "[TESTE]\n" . $mensagem;

            if ($this->enviarMensagemWhatsAppPagamento($conta, $whatsappTeste, $mensagem, 'pagamento-teste')) {
                $msgs[] = 'WhatsApp enviado para ' . $whatsappTeste;
            } else {
                $msgs[] = 'WhatsApp: integração não configurada para esta conta (Z-API ou ChatPro)';
            }
        } catch (\Throwable $e) {
            \Log::warning('[pagamento-teste] Falha ao enviar WhatsApp: ' . $e->getMessage());
            $msgs[] = 'Falha no WhatsApp: ' . $e->getMessage();
        }

        Flash::success('[TESTE] ' . implode('. ', $msgs) . '.');
        return redirect()->back();
    }

    // ─── Privados ─────────────────────────────────────────────────────────────

    private function notificarAprovacao(PagamentoCorrespondente $pagamento): void
    {
        $correspondente = $pagamento->correspondente;
        if (! $correspondente) {
            return;
        }

        $pagamento->loadMissing(['itens.processo']);

        $banco      = $this->buscarDadosBancarios($pagamento->cd_correspondente_cor);
        $escritorio = Conta::find($this->conta);
        $token      = $pagamento->tk_confirmacao_pag;

        $pdfPath = null;
        try {
            $pdfPath = $this->gerarPdfFatura($pagamento, $banco, $escritorio);
        } catch (\Throwable $e) {
            \Log::error('[pagamento] Falha ao gerar PDF da fatura: ' . $e->getMessage(), [
                'pagamento' => $pagamento->cd_pagamento_correspondente_pag,
            ]);
        }

        $emails = $this->buscarEmailsNotificacaoCorrespondente($pagamento->cd_correspondente_cor);
        if ($emails->isEmpty()) {
            \Log::warning('[pagamento] Nenhum e-mail de notificação encontrado para o correspondente ' . $pagamento->cd_correspondente_cor);
        } else {
            $this->enviarEmailAprovacaoPagamento($pagamento, $escritorio, $banco, $token, $pdfPath, $emails);
        }

        // ── WhatsApp ────────────────────────────────────────────────────────
        $whatsapp = $correspondente->nu_telefone_whatsapp_con ?? null;
        if ($whatsapp) {
            try {
                $conta    = Conta::find($this->conta);
                $mensagem = $this->montarMensagemWhatsApp($pagamento, $banco, $token);
                $this->enviarMensagemWhatsAppPagamento($conta, $whatsapp, $mensagem, 'pagamento');
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar WhatsApp: ' . $e->getMessage());
            }
        }
    }

    /**
     * E-mails de notificação do correspondente (tipo NOTIFICACAO), com fallback para demais cadastros.
     */
    private function buscarEmailsNotificacaoCorrespondente($cdCorrespondente)
    {
        $base = DB::table('endereco_eletronico_ele as e')
            ->join('conta_correspondente_ccr as ccr', 'ccr.cd_entidade_ete', '=', 'e.cd_entidade_ete')
            ->where('ccr.cd_correspondente_cor', $cdCorrespondente)
            ->whereNotNull('e.dc_endereco_eletronico_ede')
            ->where('e.dc_endereco_eletronico_ede', '!=', '');

        $notificacao = (clone $base)
            ->where('e.cd_tipo_endereco_eletronico_tee', TipoEnderecoEletronico::NOTIFICACAO)
            ->pluck('e.dc_endereco_eletronico_ede');

        if ($notificacao->isNotEmpty()) {
            return $notificacao->unique()->values();
        }

        return $base->pluck('e.dc_endereco_eletronico_ede')->unique()->values();
    }

    /**
     * Envia e-mail de aprovação com o demonstrativo PDF em anexo.
     */
    private function enviarEmailAprovacaoPagamento(
        PagamentoCorrespondente $pagamento,
        $escritorio,
        $banco,
        string $token,
        ?string $pdfPath,
        $destinos,
        bool $teste = false
    ): bool {
        $destinos = collect($destinos)->filter()->unique()->values();
        if ($destinos->isEmpty()) {
            return false;
        }

        $mesAno       = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
        $valorFmt     = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
        $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';
        $linkRevisao  = url("pagamentos/revisar/{$token}");
        $nomeAnexo    = 'Demonstrativo_' . str_replace('/', '_', $mesAno) . '.pdf';
        $temPdf       = $pdfPath && file_exists($pdfPath);

        $html = $this->montarHtmlEmailAprovacao($nmEscritorio, $mesAno, $valorFmt, $linkRevisao, $temPdf, $teste);
        $assunto = ($teste ? '[TESTE] ' : '') . "Aprovação de Pagamento - {$mesAno} - {$nmEscritorio}";

        $enviados = 0;

        foreach ($destinos as $email) {
            try {
                Mail::send([], [], function ($msg) use ($email, $assunto, $html, $pdfPath, $nomeAnexo, $temPdf) {
                    $msg->to($email)
                        ->subject($assunto)
                        ->setBody($html, 'text/html');

                    if ($temPdf) {
                        $msg->attach($pdfPath, [
                            'as'   => $nomeAnexo,
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
                $enviados++;
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar e-mail para ' . $email . ': ' . $e->getMessage());
            }
        }

        if ($enviados > 0 && ! $temPdf) {
            \Log::warning('[pagamento] E-mail enviado sem PDF em anexo', [
                'pagamento' => $pagamento->cd_pagamento_correspondente_pag,
                'destinos'  => $destinos->all(),
            ]);
        }

        return $enviados > 0;
    }

    private function montarHtmlEmailAprovacao(
        string $nmEscritorio,
        string $mesAno,
        string $valorFmt,
        string $linkRevisao,
        bool $temPdf,
        bool $teste = false
    ): string {
        $prefixoTeste = $teste ? '<p><strong>[TESTE]</strong></p>' : '';
        $anexo = $temPdf
            ? '<p>📎 <strong>Em anexo:</strong> demonstrativo de honorários em PDF com a relação de processos.</p>'
            : '<p style="color:#e67e22;"><strong>Atenção:</strong> o demonstrativo em PDF não pôde ser gerado neste envio. Utilize o link abaixo para revisar os valores online.</p>';

        return '<div style="font-family:Arial,sans-serif;font-size:14px;color:#333;max-width:600px;margin:0 auto;">'
            . $prefixoTeste
            . '<p>Olá!</p>'
            . '<p>O escritório <strong>' . e($nmEscritorio) . '</strong> encaminhou o demonstrativo de honorários referente ao mês <strong>' . e($mesAno) . '</strong>.</p>'
            . '<p><strong>Valor total: ' . e($valorFmt) . '</strong></p>'
            . $anexo
            . '<p>Acesse o link abaixo para revisar a listagem de processos e confirmar ou recusar o pagamento:</p>'
            . '<p><a href="' . e($linkRevisao) . '" style="color:#1a7bb9;">' . e($linkRevisao) . '</a></p>'
            . '<p style="color:#c0392b;font-weight:bold;border:2px solid #e74c3c;padding:12px;border-radius:4px;margin:20px 0;background:#fdf2f2;">'
            . 'PRAZO PARA ACEITE OU RECUSA DO RELATÓRIO DIA 05. PEDIMOS QUE CUMPRAM O PRAZO SOLICITADO, PARA QUE O PAGAMENTO SEJA FEITO NA DATA ACORDADA VIA PIX. SE O PIX FOI ALTERADO RECUSAR E ALTERAR O PIX NO SISTEMA.'
            . '</p>'
            . '<p style="color:#888;font-size:12px;">Atenciosamente,<br>' . e($nmEscritorio) . '</p>'
            . '</div>';
    }

    /**
     * Envia mensagem WhatsApp via Z-API (prioritário) ou ChatPro (fallback).
     */
    private function enviarMensagemWhatsAppPagamento($conta, string $destino, string $mensagem, string $contexto = 'pagamento'): bool
    {
        $client = WhatsappDispatcher::forConta($conta);
        if (! $client) {
            \Log::warning("[{$contexto}] Integração WhatsApp não configurada para a conta " . ($conta->cd_conta_con ?? '?'));

            return false;
        }

        $res = $client->sendText($destino, $mensagem);
        if (! $res['success']) {
            \Log::warning("[{$contexto}] Falha ao enviar WhatsApp: " . ($res['message'] ?? '?'), [
                'destino' => $destino,
                'status'  => $res['status'] ?? null,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Monta o texto enriquecido para WhatsApp com link de confirmação e dados bancários.
     */
    private function montarMensagemWhatsApp(PagamentoCorrespondente $pagamento, $banco, string $token): string
    {
        $escritorio   = Conta::find($this->conta);
        $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';
        $nmCorresp    = $pagamento->correspondente->nm_razao_social_con
                     ?? $pagamento->correspondente->nm_fantasia_con
                     ?? '';
        $mesAno   = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
        $valorFmt = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
        $link     = url("pagamentos/revisar/{$token}");

        $msg  = "Olá" . ($nmCorresp ? ", *{$nmCorresp}*" : "") . "!\n\n";
        $msg .= "O escritório *{$nmEscritorio}* encaminhou o demonstrativo de honorários referente a *{$mesAno}*.\n\n";
        $msg .= "💰 *Valor total: {$valorFmt}*\n\n";
        $msg .= "✅ *Revisar e confirmar o demonstrativo:*\n{$link}\n";
        $msg .= "\n⚠️ *PRAZO PARA ACEITE OU RECUSA DO RELATÓRIO DIA 05. PEDIMOS QUE CUMPRAM O PRAZO SOLICITADO, PARA QUE O PAGAMENTO SEJA FEITO NA DATA ACORDADA VIA PIX. SE O PIX FOI ALTERADO RECUSAR E ALTERAR O PIX NO SISTEMA.*\n";

        if ($banco && $banco->nm_titular_dba) {
            $msg .= "\n🏦 *Dados bancários para pagamento:*\n";
            if ($banco->nm_banco_ban) {
                $msg .= "Banco: {$banco->cd_banco_ban} – {$banco->nm_banco_ban}\n";
            }
            if ($banco->nu_agencia_dba) {
                $msg .= "Agência: {$banco->nu_agencia_dba}\n";
            }
            if ($banco->nu_conta_dba) {
                $msg .= "Conta: {$banco->nu_conta_dba}";
                if ($banco->nm_tipo_conta_tcb) $msg .= " ({$banco->nm_tipo_conta_tcb})";
                $msg .= "\n";
            }
            if ($banco->dc_pix_dba) {
                $msg .= "PIX: {$banco->dc_pix_dba}\n";
            }
            $msg .= "Titular: {$banco->nm_titular_dba}\n";
            if ($banco->nu_cpf_cnpj_dba) {
                $msg .= "CPF/CNPJ: {$banco->nu_cpf_cnpj_dba}\n";
            }
        }

        return $msg;
    }

    /**
     * Busca os dados bancários do correspondente via conta_correspondente_ccr.
     */
    private function buscarDadosBancarios($cdCorrespondente)
    {
        $rows = DB::select("
            SELECT
                COALESCE(main.cd_dados_bancarios_dba, pix.cd_dados_bancarios_dba)  AS cd_dados_bancarios_dba,
                COALESCE(main.nm_titular_dba,          pix.nm_titular_dba)          AS nm_titular_dba,
                COALESCE(main.nu_cpf_cnpj_dba,         pix.nu_cpf_cnpj_dba)         AS nu_cpf_cnpj_dba,
                main.cd_banco_ban,
                ban.nm_banco_ban,
                main.nu_agencia_dba,
                main.nu_conta_dba,
                COALESCE(main.dc_pix_dba,              pix.dc_pix_dba)              AS dc_pix_dba,
                COALESCE(main.cd_tipo_conta_tcb,       pix.cd_tipo_conta_tcb)       AS cd_tipo_conta_tcb,
                tcb.nm_tipo_conta_tcb
            FROM conta_correspondente_ccr ccr
            -- registro de conta corrente/poupança (tipo != 3)
            LEFT JOIN dados_bancarios_dba main ON (
                ccr.cd_entidade_ete = main.cd_entidade_ete
                AND main.deleted_at IS NULL
                AND main.cd_tipo_conta_tcb != 3
            )
            -- registro PIX (tipo = 3)
            LEFT JOIN dados_bancarios_dba pix ON (
                ccr.cd_entidade_ete = pix.cd_entidade_ete
                AND pix.deleted_at IS NULL
                AND pix.cd_tipo_conta_tcb = 3
            )
            LEFT JOIN banco_ban ban ON (main.cd_banco_ban = ban.cd_banco_ban)
            LEFT JOIN tipo_conta_banco_tcb tcb ON (
                COALESCE(main.cd_tipo_conta_tcb, pix.cd_tipo_conta_tcb) = tcb.cd_tipo_conta_tcb
            )
            WHERE ccr.cd_correspondente_cor = ?
            LIMIT 1
        ", [$cdCorrespondente]);

        return ! empty($rows) ? (object) $rows[0] : null;
    }

    /**
     * Gera o PDF da fatura e salva em storage/app/public/pagamentos/.
     * Retorna o caminho absoluto do arquivo gerado.
     */
    private function gerarPdfFatura(PagamentoCorrespondente $pagamento, $banco, $escritorio = null): string
    {
        if (! $escritorio) {
            $escritorio = Conta::find($pagamento->cd_conta_con);
        }

        $html = view('correspondente/pagamento-fatura-pdf', compact('pagamento', 'banco', 'escritorio'))->render();

        $tmpDir = storage_path('app/mpdf-tmp');
        $outDir = storage_path('app/public/pagamentos');
        foreach ([$tmpDir, $outDir] as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 18,
            'margin_bottom' => 18,
            'tempDir'       => $tmpDir,
        ]);

        $mpdf->SetTitle('Fatura ' . $pagamento->nm_mes_ano);
        $mpdf->WriteHTML($html);

        $path = $outDir . '/fatura_' . $pagamento->cd_pagamento_correspondente_pag . '.pdf';
        $mpdf->Output($path, 'F');

        return $path;
    }

    // ─── Relatório Mensal em PDF ───────────────────────────────────────────────

    private function buscarDadosRelatorioPagamentos(int $mes, int $ano): array
    {
        $pagamentos = PagamentoCorrespondente::with(['correspondente'])
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->where('vl_total_pag', '>', 0)
            ->get()
            ->sortBy(function ($pag) {
                return $pag->correspondente->nm_razao_social_con
                    ?? $pag->correspondente->nm_fantasia_con
                    ?? 'zzz';
            })
            ->values();

        $bancoPorPag = [];
        foreach ($pagamentos as $pag) {
            $bancoPorPag[$pag->cd_pagamento_correspondente_pag] =
                $this->buscarDadosBancarios($pag->cd_correspondente_cor);
        }

        $escritorio = Conta::find($this->conta);
        $mesPad     = str_pad($mes, 2, '0', STR_PAD_LEFT);

        return [
            'pagamentos'  => $pagamentos,
            'bancoPorPag' => $bancoPorPag,
            'escritorio'  => $escritorio,
            'mesAnoFmt'   => $mesPad . '/' . $ano,
            'mesAno'      => $mesPad . '_' . $ano,
        ];
    }

    public function relatorioPdf(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $dados = $this->buscarDadosRelatorioPagamentos($mes, $ano);
        extract($dados);

        $tmpDir = storage_path('app/mpdf-tmp');
        $outDir = storage_path('app/public/pagamentos');
        foreach ([$tmpDir, $outDir] as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 12,
            'margin_right'  => 12,
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'tempDir'       => $tmpDir,
        ]);

        $mpdf->SetTitle('Relatório de Pagamentos ' . $mesAnoFmt);

        // 1. CSS separado (mode=1) — evita o limite de pcre.backtrack_limit
        $css = view('correspondente/pagamentos-relatorio-pdf-css')->render();
        $mpdf->WriteHTML($css, 1);

        // 2. Cabeçalho + tabela de resumo (mode=2)
        $cabecalho = view('correspondente/pagamentos-relatorio-pdf-cabecalho', compact(
            'pagamentos', 'bancoPorPag', 'escritorio', 'mesAnoFmt'
        ))->render();
        $mpdf->WriteHTML($cabecalho, 2);

        // 3. Rodapé (mode=2)
        $nmEscritorio = e($escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? '');
        $mpdf->WriteHTML(
            '<div class="footer">Documento gerado automaticamente em ' . now()->format('d/m/Y \à\s H:i')
            . ' &mdash; ' . $nmEscritorio
            . ' &mdash; Uso interno. N&atilde;o possui validade como comprovante.</div>',
            2
        );

        $filename = 'relatorio_pagamentos_' . $mesAno . '.pdf';
        $mpdf->Output($filename, 'D');
    }

    public function relatorioExcel(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $dados    = $this->buscarDadosRelatorioPagamentos($mes, $ano);
        $filename = 'relatorio_pagamentos_' . $dados['mesAno'] . '.xlsx';

        return \Excel::download(
            new PagamentosCorrespondenteRelatorioExport($dados),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    private function mesesDisponiveis(): array
    {
        $atual = Carbon::now()->startOfMonth();

        $meses = PagamentoCorrespondente::where('cd_conta_con', $this->conta)
            ->whereBetween('nu_mes_pag', [1, 12])
            ->where('nu_ano_pag', '>=', 2000)
            ->where(function ($query) use ($atual) {
                $query->where('nu_ano_pag', '<', $atual->year)
                    ->orWhere(function ($query) use ($atual) {
                        $query->where('nu_ano_pag', $atual->year)
                            ->where('nu_mes_pag', '<=', $atual->month);
                    });
            })
            ->selectRaw('nu_mes_pag, nu_ano_pag')
            ->distinct()
            ->orderByRaw('nu_ano_pag DESC, nu_mes_pag DESC')
            ->get();

        // Garante que o mês atual esteja sempre na lista
        $competenciaAtual = ['mes' => $atual->month, 'ano' => $atual->year];
        $lista = [$competenciaAtual];

        foreach ($meses as $m) {
            $entry = ['mes' => (int) $m->nu_mes_pag, 'ano' => (int) $m->nu_ano_pag];
            if ($entry !== $competenciaAtual) {
                $lista[] = $entry;
            }
        }

        usort($lista, function ($a, $b) {
            if ($a['ano'] === $b['ano']) {
                return $b['mes'] <=> $a['mes'];
            }

            return $b['ano'] <=> $a['ano'];
        });

        return $lista;
    }
}