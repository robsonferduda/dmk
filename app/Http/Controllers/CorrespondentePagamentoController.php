<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use App\Conta;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\Enums\StatusPagamentoCorrespondente;
use App\Services\ChatPro\ChatProClient;

class CorrespondentePagamentoController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth')->except(['confirmarPorToken']);
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    // ─── Listagem ─────────────────────────────────────────────────────────────

    public function pagamentos(Request $request)
    {
        $mes = (int) ($request->mes ?: Carbon::now()->month);
        $ano = (int) ($request->ano ?: Carbon::now()->year);

        $pagamentos = PagamentoCorrespondente::with('correspondente')
            ->where('cd_conta_con', $this->conta)
            ->where('nu_mes_pag', $mes)
            ->where('nu_ano_pag', $ano)
            ->get()
            ->sortBy(function ($pag) {
                return $pag->correspondente->nm_razao_social_con
                    ?? $pag->correspondente->nm_fantasia_con
                    ?? 'zzz';
            })
            ->values();

        $statusLabels  = StatusPagamentoCorrespondente::labels();
        $mesAnoAtual   = Carbon::now()->format('m/Y');
        $mesesNavegacao = $this->mesesDisponiveis();

        return view('correspondente/pagamentos', compact(
            'pagamentos', 'mes', 'ano', 'statusLabels', 'mesAnoAtual', 'mesesNavegacao'
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

        if (! $pagamento->podeEnviarAprovacao()) {
            Flash::error('Este pagamento não pode ser enviado para aprovação no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento) {
            // Gera (ou regenera) o token de confirmação por link
            $pagamento->tk_confirmacao_pag      = Str::random(64);
            $pagamento->cd_status_pag           = StatusPagamentoCorrespondente::ENVIADO_APROVACAO;
            $pagamento->dt_envio_aprovacao_pag  = Carbon::now();
            $pagamento->save();
        });

        // Notifica via e-mail e WhatsApp (com PDF e link de confirmação)
        $this->notificarAprovacao($pagamento);

        Flash::success('Pagamento enviado para aprovação do correspondente.');
        return redirect()->back();
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

        if ($pagamento->cd_status_pag != StatusPagamentoCorrespondente::ENVIADO_APROVACAO) {
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

        if ($pagamento->cd_status_pag != StatusPagamentoCorrespondente::ENVIADO_APROVACAO) {
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

    // ─── Marcar como Pago ─────────────────────────────────────────────────────

    public function pagar(Request $request, $id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_conta_con', $this->conta)->findOrFail($id);

        if (! $pagamento->podePagar()) {
            Flash::error('Este pagamento não pode ser marcado como pago no status atual.');
            return redirect()->back();
        }

        DB::transaction(function () use ($pagamento, $request) {
            $pagamento->cd_status_pag      = StatusPagamentoCorrespondente::PAGO;
            $pagamento->dt_pagamento_pag   = Carbon::now();
            $pagamento->ds_observacao_pag  = $request->observacao;
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
            $mesAno       = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
            $valorFmt     = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
            $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';

            \Mail::raw(
                "[TESTE] Olá!\n\nO escritório {$nmEscritorio} encaminhou o demonstrativo de honorários referente ao mês {$mesAno}.\n\nValor total: {$valorFmt}\n\nAcesse o link abaixo para revisar a listagem de processos e confirmar ou recusar o pagamento:\n" . url("pagamentos/revisar/{$token}") . "\n\nAtenciosamente,\n{$nmEscritorio}",
                function ($msg) use ($emailTeste, $mesAno, $nmEscritorio, $pdfPath) {
                    $msg->to($emailTeste)
                        ->subject("[TESTE] Aprovação de Pagamento - {$mesAno} -{$nmEscritorio}");
                    if ($pdfPath && file_exists($pdfPath)) {
                        $msg->attach($pdfPath, [
                            'as'   => 'Fatura_' . str_replace('/', '_', $mesAno) . '.pdf',
                            'mime' => 'application/pdf',
                        ]);
                    }
                }
            );
            $msgs[] = 'E-mail enviado para ' . $emailTeste . ($pdfPath ? ' (com PDF)' : '');
        } catch (\Throwable $e) {
            \Log::warning('[pagamento-teste] Falha ao enviar e-mail: ' . $e->getMessage());
            $msgs[] = 'Falha no e-mail: ' . $e->getMessage();
        }

        // WhatsApp de teste: documento PDF + mensagem de texto
        try {
            $conta   = Conta::find($this->conta);
            $chatpro = ChatProClient::forConta($conta);
            if ($chatpro) {
                // Envia o PDF como documento
                if ($pdfPath && file_exists($pdfPath)) {
                    $pdfUrl = url('storage/pagamentos/fatura_' . $pagamento->cd_pagamento_correspondente_pag . '.pdf');
                    $chatpro->sendDocument($whatsappTeste, $pdfUrl, '📄 Demonstrativo de Pagamento – ' . $mesAno);
                }
                // Envia a mensagem de texto enriquecida
                $mensagem = $this->montarMensagemWhatsApp($pagamento, $banco, $token);
                $mensagem = "[TESTE]\n" . $mensagem;
                $chatpro->sendText($whatsappTeste, $mensagem);
                $msgs[] = 'WhatsApp enviado para ' . $whatsappTeste;
            } else {
                $msgs[] = 'WhatsApp: ChatPro não configurado para esta conta';
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

        $banco        = $this->buscarDadosBancarios($pagamento->cd_correspondente_cor);
        $escritorio   = Conta::find($this->conta);
        $token        = $pagamento->tk_confirmacao_pag;
        $mesAno       = str_pad($pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag;
        $valorFmt     = 'R$ ' . number_format($pagamento->vl_total_pag, 2, ',', '.');
        $nmEscritorio = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';

        // Gera PDF
        $pdfPath = null;
        try {
            $pdfPath = $this->gerarPdfFatura($pagamento, $banco, $escritorio);
        } catch (\Throwable $e) {
            \Log::warning('[pagamento] Falha ao gerar PDF: ' . $e->getMessage());
        }

        // ── E-mail ──────────────────────────────────────────────────────────
        $emails = DB::table('endereco_eletronico_ele as e')
            ->join('conta_correspondente_ccr as ccr', 'ccr.cd_entidade_ete', '=', 'e.cd_entidade_ete')
            ->where('ccr.cd_correspondente_cor', $pagamento->cd_correspondente_cor)
            ->pluck('e.dc_endereco_eletronico_ede')
            ->filter()
            ->unique()
            ->values();

        foreach ($emails as $email) {
            try {
                \Mail::raw(
                    "Olá!\n\nO escritório {$nmEscritorio} encaminhou o demonstrativo de honorários referente ao mês {$mesAno}.\n\nValor total: {$valorFmt}\n\nAcesse o link abaixo para revisar a listagem de processos e confirmar ou recusar o pagamento:\n" . url("pagamentos/revisar/{$token}") . "\n\nO comprovante detalhado está em anexo.\n\nAtenciosamente,\n{$nmEscritorio}",
                    function ($msg) use ($email, $mesAno, $nmEscritorio, $pdfPath) {
                        $msg->to($email)
                            ->subject("Aprovação de Pagamento - {$mesAno} - {$nmEscritorio}");
                        if ($pdfPath && file_exists($pdfPath)) {
                            $msg->attach($pdfPath, [
                                'as'   => 'Fatura_' . str_replace('/', '_', $mesAno) . '.pdf',
                                'mime' => 'application/pdf',
                            ]);
                        }
                    }
                );
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar e-mail para ' . $email . ': ' . $e->getMessage());
            }
        }

        // ── WhatsApp ────────────────────────────────────────────────────────
        $whatsapp = $correspondente->nu_telefone_whatsapp_con ?? null;
        if ($whatsapp) {
            try {
                $conta   = Conta::find($this->conta);
                $chatpro = ChatProClient::forConta($conta);
                if ($chatpro) {
                    // Envia o PDF como documento anexo
                    if ($pdfPath && file_exists($pdfPath)) {
                        $pdfUrl = url('storage/pagamentos/fatura_' . $pagamento->cd_pagamento_correspondente_pag . '.pdf');
                        $chatpro->sendDocument($whatsapp, $pdfUrl, '📄 Demonstrativo de Pagamento - ' . $mesAno);
                    }
                    // Envia mensagem de texto enriquecida
                    $mensagem = $this->montarMensagemWhatsApp($pagamento, $banco, $token);
                    $chatpro->sendText($whatsapp, $mensagem);
                }
            } catch (\Throwable $e) {
                \Log::warning('[pagamento] Falha ao enviar WhatsApp: ' . $e->getMessage());
            }
        }
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
        $msg .= "📄 O comprovante detalhado está no arquivo em anexo.\n\n";
        $msg .= "✅ *Revisar e confirmar o demonstrativo:*\n{$link}\n";

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
                dba.nm_titular_dba,
                dba.nu_cpf_cnpj_dba,
                dba.cd_banco_ban,
                ban.nm_banco_ban,
                dba.nu_agencia_dba,
                dba.nu_conta_dba,
                dba.dc_pix_dba,
                dba.cd_tipo_conta_tcb,
                tcb.nm_tipo_conta_tcb
            FROM conta_correspondente_ccr ccr
            LEFT JOIN dados_bancarios_dba dba ON (ccr.cd_entidade_ete = dba.cd_entidade_ete AND dba.deleted_at IS NULL)
            LEFT JOIN banco_ban ban ON (dba.cd_banco_ban = ban.cd_banco_ban)
            LEFT JOIN tipo_conta_banco_tcb tcb ON (dba.cd_tipo_conta_tcb = tcb.cd_tipo_conta_tcb)
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

    private function mesesDisponiveis(): array
    {
        $meses = PagamentoCorrespondente::where('cd_conta_con', $this->conta)
            ->selectRaw('nu_mes_pag, nu_ano_pag')
            ->distinct()
            ->orderByRaw('nu_ano_pag DESC, nu_mes_pag DESC')
            ->get();

        // Garante que o mês atual esteja sempre na lista
        $atual = ['mes' => Carbon::now()->month, 'ano' => Carbon::now()->year];
        $lista = [['mes' => $atual['mes'], 'ano' => $atual['ano']]];

        foreach ($meses as $m) {
            $entry = ['mes' => $m->nu_mes_pag, 'ano' => $m->nu_ano_pag];
            if ($entry !== $lista[0]) {
                $lista[] = $entry;
            }
        }

        return $lista;
    }
}