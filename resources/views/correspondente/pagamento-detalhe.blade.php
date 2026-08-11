@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente/pagamentos?mes='.$pagamento->nu_mes_pag.'&ano='.$pagamento->nu_ano_pag) }}">Pagamentos</a></li>
        <li>Detalhe</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i>
                Pagamento –
                {{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}
                <span> – {{ $pagamento->nm_mes_ano }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 boxBtnTopo">
            <a href="{{ url('correspondente/pagamentos?mes='.$pagamento->nu_mes_pag.'&ano='.$pagamento->nu_ano_pag) }}"
               class="btn btn-default pull-right header-btn">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>

    @php
        $cores  = [1=>'default',2=>'warning',3=>'info',4=>'success'];
        $cor    = $cores[$pagamento->cd_status_pag] ?? 'default';
    @endphp

    {{-- Cabeçalho do pagamento --}}
    <div class="row">
        <div class="col-md-4">
            {{-- Resumo do pagamento --}}
            <div class="well" style="margin-bottom:15px;">
                <h5 style="margin-top:0; font-weight:700; border-bottom:1px solid #ddd; padding-bottom:6px;">
                    <i class="fa fa-file-text-o"></i> Resumo
                </h5>
                <dl class="dl-horizontal" style="margin-bottom:0;">
                    <dt>Correspondente</dt>
                    <dd>{{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}</dd>
                    <dt>Competência</dt>
                    <dd>{{ $pagamento->nm_mes_ano }}</dd>
                    <dt>Valor Total</dt>
                    <dd><strong>R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</strong></dd>
                    <dt>Honorários</dt>
                    <dd>R$ {{ number_format($pagamento->vl_honorario_total, 2, ',', '.') }}</dd>
                    <dt>Despesas</dt>
                    <dd>R$ {{ number_format($pagamento->vl_despesa_total, 2, ',', '.') }}</dd>
                    @if($pagamento->vl_pago_total > 0)
                    <dt>Já pago</dt>
                    <dd style="color:#27ae60;"><strong>R$ {{ number_format($pagamento->vl_pago_total, 2, ',', '.') }}</strong></dd>
                    <dt>Saldo</dt>
                    <dd style="color:#d68910;"><strong>R$ {{ number_format($pagamento->vl_saldo_total, 2, ',', '.') }}</strong></dd>
                    @endif
                    <dt>Status</dt>
                    <dd>
                        @php $corStatus = $pagamento->isParcialmentePago() ? 'warning' : $cor; @endphp
                        <span class="label label-{{ $corStatus }}">{{ $pagamento->nm_status }}</span>
                    </dd>
                    @if($pagamento->dt_envio_aprovacao_pag)
                    <dt>Enviado em</dt>
                    <dd>{{ $pagamento->dt_envio_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->dt_aprovacao_pag)
                    <dt>Aprovado em</dt>
                    <dd>{{ $pagamento->dt_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->dt_pagamento_pag)
                    <dt>Pago em</dt>
                    <dd>{{ $pagamento->dt_pagamento_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->cd_status_pag == 5 && $pagamento->ds_observacao_pag)
                    <dt style="color:#e74c3c;">Motivo Recusa</dt>
                    <dd>
                        <span style="color:#e74c3c; font-weight:600;">
                            <i class="fa fa-times-circle"></i>
                            {{ $pagamento->ds_observacao_pag }}
                        </span>
                    </dd>
                    @elseif($pagamento->ds_observacao_pag)
                    <dt>Obs.</dt>
                    <dd>{{ $pagamento->ds_observacao_pag }}</dd>
                    @endif
                </dl>
            </div>

            {{-- Dados bancários --}}
            <div class="well" style="margin-bottom:15px;">
                <h5 style="margin-top:0; font-weight:700; border-bottom:1px solid #ddd; padding-bottom:6px;">
                    <i class="fa fa-bank"></i> Dados Bancários
                    @if($pagamento->cd_status_pag == 5)
                    <small class="text-danger"> <i class="fa fa-pencil"></i> editável</small>
                    @endif
                </h5>
                @if($pagamento->cd_status_pag == 5)
                {{-- Formulário de edição de dados bancários (só quando recusado) --}}
                <form method="POST" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/atualizar-dados-bancarios') }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:8px;">
                        <label style="font-size:11px;margin-bottom:2px;">Titular</label>
                        <input type="text" name="nm_titular_dba" class="form-control input-sm"
                               value="{{ $banco->nm_titular_dba ?? '' }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom:8px;">
                        <label style="font-size:11px;margin-bottom:2px;">CPF/CNPJ</label>
                        <input type="text" name="nu_cpf_cnpj_dba" class="form-control input-sm"
                               value="{{ $banco->nu_cpf_cnpj_dba ?? '' }}">
                    </div>
                    <div class="form-group" style="margin-bottom:8px;">
                        <label style="font-size:11px;margin-bottom:2px;">Banco (código)</label>
                        <input type="text" name="cd_banco_ban" class="form-control input-sm"
                               value="{{ $banco->cd_banco_ban ?? '' }}" placeholder="Ex: 001">
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group" style="margin-bottom:8px;">
                                <label style="font-size:11px;margin-bottom:2px;">Agência</label>
                                <input type="text" name="nu_agencia_dba" class="form-control input-sm"
                                       value="{{ $banco->nu_agencia_dba ?? '' }}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group" style="margin-bottom:8px;">
                                <label style="font-size:11px;margin-bottom:2px;">Conta</label>
                                <input type="text" name="nu_conta_dba" class="form-control input-sm"
                                       value="{{ $banco->nu_conta_dba ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:8px;">
                        <label style="font-size:11px;margin-bottom:2px;">Tipo de Conta</label>
                        <select name="cd_tipo_conta_tcb" class="form-control input-sm">
                            <option value="">— Selecione —</option>
                            @foreach(\DB::table('tipo_conta_banco_tcb')->orderBy('nm_tipo_conta_tcb')->get() as $tipo)
                            <option value="{{ $tipo->cd_tipo_conta_tcb }}"
                                {{ ($banco->cd_tipo_conta_tcb ?? '') == $tipo->cd_tipo_conta_tcb ? 'selected' : '' }}>
                                {{ $tipo->nm_tipo_conta_tcb }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:10px;">
                        <label style="font-size:11px;margin-bottom:2px;">Chave PIX</label>
                        <input type="text" name="dc_pix_dba" class="form-control input-sm"
                               value="{{ $banco->dc_pix_dba ?? '' }}" placeholder="CPF, e-mail, telefone ou chave aleatória">
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm btn-block">
                        <i class="fa fa-save"></i> Salvar Dados Bancários
                    </button>
                </form>
                @else
                @if($banco && $banco->nm_titular_dba)
                <dl class="dl-horizontal" style="margin-bottom:0;">
                    <dt>Titular</dt>
                    <dd>{{ $banco->nm_titular_dba }}</dd>
                    @if($banco->nu_cpf_cnpj_dba)
                    <dt>CPF/CNPJ</dt>
                    <dd>{{ $banco->nu_cpf_cnpj_dba }}</dd>
                    @endif
                    @if($banco->nm_banco_ban)
                    <dt>Banco</dt>
                    <dd>{{ $banco->cd_banco_ban }} – {{ $banco->nm_banco_ban }}</dd>
                    @endif
                    @if($banco->nu_agencia_dba)
                    <dt>Agência</dt>
                    <dd>{{ $banco->nu_agencia_dba }}</dd>
                    @endif
                    @if($banco->nu_conta_dba)
                    <dt>Conta</dt>
                    <dd>
                        {{ $banco->nu_conta_dba }}
                        @if($banco->nm_tipo_conta_tcb)
                        <span class="text-muted">({{ $banco->nm_tipo_conta_tcb }})</span>
                        @endif
                    </dd>
                    @endif
                    @if($banco->dc_pix_dba)
                    <dt>PIX</dt>
                    <dd>{{ $banco->dc_pix_dba }}</dd>
                    @endif
                </dl>
                @else
                <p class="text-muted" style="margin:0;"><i class="fa fa-exclamation-triangle"></i> Dados bancários não cadastrados.</p>
                @endif
                @endif
            </div>

            {{-- Ações conforme status --}}
            @if($pagamento->podeAtualizarValores())
            <form method="POST" style="margin-bottom:8px;" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/atualizar-valores') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-block"
                        onclick="return confirm('Atualizar valores deste pagamento com base nos processos atuais?\n\nIsso adiciona processos novos, remove os que mudaram de correspondente e atualiza honorários/despesas.')">
                    <i class="fa fa-refresh"></i> Atualizar Valores
                </button>
            </form>
            @endif

            @if($pagamento->podeNotificarAprovacao())
            <form method="POST" style="margin-bottom:8px;" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/enviar-aprovacao') }}">
                @csrf
                <button type="submit" class="btn btn-warning btn-block"
                        onclick="return confirm('{{ $pagamento->podeReenviarAprovacao()
                            ? 'Reenviar a notificação de aprovação para o correspondente (e-mail e WhatsApp)? Um novo link será gerado.'
                            : 'Enviar para aprovação do correspondente via e-mail e WhatsApp?' }}')">
                    <i class="fa fa-{{ $pagamento->podeReenviarAprovacao() ? 'repeat' : 'paper-plane' }}"></i>
                    {{ $pagamento->podeReenviarAprovacao() ? 'Reenviar para Aprovação' : 'Enviar para Aprovação' }}
                </button>
            </form>
            @endif

            @if($pagamento->podeAprovar())
            <form method="POST" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/aprovar') }}">
                @csrf
                <button type="submit" class="btn btn-info btn-block"
                        onclick="return confirm('Confirmar aprovação deste pagamento?')">
                    <i class="fa fa-check"></i> Aprovar Pagamento
                </button>
            </form>
            @endif

            @if($pagamento->podePagar())
            <button type="button" class="btn btn-success btn-block"
                    data-toggle="modal" data-target="#modalPagar"
                    data-id="{{ $pagamento->cd_pagamento_correspondente_pag }}"
                    data-nome="{{ $pagamento->correspondente->nm_razao_social_con ?? '' }}"
                    data-saldo="R$ {{ number_format($pagamento->vl_saldo_total, 2, ',', '.') }}"
                    data-saldo-honorario="R$ {{ number_format($pagamento->vl_saldo_honorario, 2, ',', '.') }}"
                    data-saldo-despesa="R$ {{ number_format($pagamento->vl_saldo_despesa, 2, ',', '.') }}">
                <i class="fa fa-dollar"></i>
                {{ $pagamento->isParcialmentePago() ? 'Quitar Saldo Restante' : 'Registrar Pagamento' }}
            </button>
            @endif
        </div>

        {{-- Lista de itens --}}
        <div class="col-md-8">
            @if($pagamento->podeGerenciarBaixas())
            <div class="jarviswidget jarviswidget-color-blueDark" id="lancamentos">
                <header>
                    <span class="widget-icon"><i class="fa fa-credit-card"></i></span>
                    <h2>Lançamentos de Pagamento</h2>
                </header>
                <div>
                    <div class="widget-body">
                        <div class="row" style="margin-bottom:15px;">
                            <div class="col-sm-3">
                                <div class="well well-sm text-center" style="margin:0;">
                                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;">Saldo Honorários</div>
                                    <strong>R$ {{ number_format($pagamento->vl_saldo_honorario, 2, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="well well-sm text-center" style="margin:0;">
                                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;">Saldo Despesas</div>
                                    <strong>R$ {{ number_format($pagamento->vl_saldo_despesa, 2, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="well well-sm text-center" style="margin:0;">
                                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;">Já Pago</div>
                                    <strong style="color:#27ae60;">R$ {{ number_format($pagamento->vl_pago_total, 2, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="well well-sm text-center" style="margin:0;">
                                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;">Saldo Total</div>
                                    <strong style="color:#d68910;">R$ {{ number_format($pagamento->vl_saldo_total, 2, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-bottom:15px;">
                            Pague <strong>honorário</strong> e <strong>despesa</strong> individualmente em cada processo na tabela abaixo.
                            O total do agrupamento continua sendo o saldo do correspondente nesta competência.
                            Use <strong>Quitar Saldo Restante</strong> para baixar todos os processos de uma vez.
                        </div>

                        @if($pagamento->baixas->isEmpty())
                            <div class="alert alert-warning" style="margin:0;">
                                Nenhum lançamento registrado ainda.
                            </div>
                        @else
                        <table class="table table-striped table-hover" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Processo</th>
                                    <th>Tipo</th>
                                    <th class="text-right">Valor</th>
                                    <th>Observação</th>
                                    <th class="text-center">Comprovante</th>
                                    <th class="text-center" style="width:70px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamento->baixas as $baixa)
                                <tr>
                                    <td>{{ $baixa->dt_baixa_pcb ? $baixa->dt_baixa_pcb->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if($baixa->item && $baixa->item->cd_processo_pro)
                                            {{ $baixa->item->processo->nu_processo_pro ?? '#'.$baixa->item->cd_processo_pro }}
                                        @elseif($baixa->item)
                                            Item #{{ $baixa->cd_pagamento_correspondente_item_pai }}
                                        @else
                                            <span class="text-muted">Sem processo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="label label-{{ $baixa->isDespesa() ? 'warning' : 'primary' }}">
                                            {{ $baixa->nm_tipo }}
                                        </span>
                                    </td>
                                    <td class="text-right"><strong>R$ {{ number_format($baixa->vl_baixa_pcb, 2, ',', '.') }}</strong></td>
                                    <td>{{ $baixa->ds_observacao_pcb ?: '—' }}</td>
                                    <td class="text-center">
                                        @if($baixa->dc_comprovante_pcb)
                                        <a href="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/baixas/'.$baixa->cd_pagamento_correspondente_baixa_pcb.'/comprovante') }}"
                                           target="_blank" class="btn btn-xs btn-default" title="Abrir comprovante">
                                            <i class="fa fa-paperclip"></i>
                                        </a>
                                        @else —
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form method="POST"
                                              action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/baixas/'.$baixa->cd_pagamento_correspondente_baixa_pcb) }}"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                    title="Excluir lançamento"
                                                    onclick="return confirm('Excluir este lançamento? O status do pagamento e do processo serão recalculados.')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total lançado</th>
                                    <th class="text-right">R$ {{ number_format($pagamento->vl_pago_total, 2, ',', '.') }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="jarviswidget jarviswidget-color-blueDark">
                <header>
                    <span class="widget-icon"><i class="fa fa-list"></i></span>
                    <h2>Processos ({{ $pagamento->qtd_itens_ativos }} ativo{{ $pagamento->qtd_itens_ativos !== 1 ? 's' : '' }}
                        @if($pagamento->itens->count() !== $pagamento->qtd_itens_ativos)
                        / {{ $pagamento->itens->count() }} total
                        @endif
                        )
                        @if($pagamento->cd_status_pag == 5)
                        <small style="color:#f39c12;"> <i class="fa fa-pencil"></i> editável</small>
                        @endif
                    </h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @php
                            $qtdComDespesa = $pagamento->itens->filter(fn($i) => (float) $i->vl_despesa_pai > 0)->count();
                            $qtdSoHonorario = $pagamento->itens->filter(fn($i) => (float) $i->vl_despesa_pai <= 0 && (float) $i->vl_honorario_pai > 0)->count();
                        @endphp
                        <div style="padding:10px 12px; border-bottom:1px solid #eee; background:#fafafa;">
                            <span class="text-muted" style="margin-right:8px;">Filtrar:</span>
                            <div class="btn-group btn-group-sm" id="filtroProcessosPagamento" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" name="filtroProcesso" value="todos" checked> Todos
                                    <span class="badge">{{ $pagamento->itens->count() }}</span>
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="filtroProcesso" value="com_despesa"> Com despesa
                                    <span class="badge">{{ $qtdComDespesa }}</span>
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="filtroProcesso" value="somente_honorario"> Somente honorário
                                    <span class="badge">{{ $qtdSoHonorario }}</span>
                                </label>
                            </div>
                        </div>

                        @if($pagamento->cd_status_pag == 5)
                        {{-- Tabela editável (só quando recusado) --}}
                        <form id="formItens" method="POST"
                              action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/atualizar-itens') }}">
                            @csrf
                            <table class="table table-striped table-hover" id="tabelaProcessosPagamento" style="margin:0;">
                                <thead>
                                    <tr>
                                        <th>Processo</th>
                                        <th>Descrição</th>
                                        <th class="text-right" style="width:130px;">Honorário (R$)</th>
                                        <th class="text-right" style="width:130px;">Despesa (R$)</th>
                                        <th class="text-right" style="width:110px;">Total</th>
                                        <th class="text-center" style="width:90px;">Incluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pagamento->itens as $item)
                                    @php
                                        $itemId = $item->cd_pagamento_correspondente_item_pai;
                                        $excluido = $item->isExcluido();
                                    @endphp
                                    <tr class="{{ $excluido ? 'item-excluido' : '' }} linha-processo-pag"
                                        data-excluido="{{ $excluido ? '1' : '0' }}"
                                        data-honorario="{{ number_format((float) $item->vl_honorario_pai, 2, '.', '') }}"
                                        data-despesa="{{ number_format((float) $item->vl_despesa_pai, 2, '.', '') }}">
                                        <td>
                                            @if($item->cd_processo_pro)
                                            <a href="{{ url('processos/editar/'.\Crypt::encrypt($item->cd_processo_pro)) }}" target="_blank">
                                                {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                                            </a>
                                            @else —
                                            @endif
                                        </td>
                                        <td>{{ $item->ds_descricao_pai }}</td>
                                        <td>
                                            <input type="number" step="0.01" min="0"
                                                   name="itens[{{ $itemId }}][vl_honorario]"
                                                   class="form-control input-sm item-honorario text-right"
                                                   value="{{ number_format($item->vl_honorario_pai, 2, '.', '') }}"
                                                   style="width:110px; margin-left:auto;">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0"
                                                   name="itens[{{ $itemId }}][vl_despesa]"
                                                   class="form-control input-sm item-despesa text-right"
                                                   value="{{ number_format($item->vl_despesa_pai, 2, '.', '') }}"
                                                   style="width:110px; margin-left:auto;">
                                        </td>
                                        <td class="text-right item-total" style="vertical-align:middle;">
                                            <strong>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-center" style="vertical-align:middle;">
                                            <input type="checkbox" name="itens[{{ $itemId }}][incluir]" value="1"
                                                   class="item-incluir" {{ $excluido ? '' : 'checked' }}
                                                   title="Desmarque para excluir este processo do total do pagamento">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="linha-vazia-processos"><td colspan="6" class="text-center text-muted">Nenhum item.</td></tr>
                                    @endforelse
                                    <tr class="linha-filtro-vazio" style="display:none;">
                                        <td colspan="6" class="text-center text-muted">Nenhum processo neste filtro.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="active">
                                        <th colspan="4" class="text-right">Total Geral</th>
                                        <th class="text-right" id="totalGeral">
                                            R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right" style="padding:10px;">
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Salvar alterações nos honorários e despesas?')">
                                                <i class="fa fa-save"></i> Salvar Alterações
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                        @else
                        {{-- Tabela somente leitura / pagamento por processo --}}
                        <table class="table table-striped table-hover" id="tabelaProcessosPagamento" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Descrição</th>
                                    <th class="text-right">Honorário</th>
                                    <th class="text-right">Despesa</th>
                                    <th class="text-right">Total</th>
                                    @if($pagamento->podeGerenciarBaixas())
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width:160px;">Pagar</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagamento->itens as $item)
                                @php
                                    $saldoHonItem = $item->vl_saldo_honorario;
                                    $saldoDesItem = $item->vl_saldo_despesa;
                                    $statusItem = $item->nm_status_pagamento;
                                    $statusClass = $item->isPago() ? 'success' : ($item->isParcialmentePago() ? 'warning' : 'default');
                                @endphp
                                <tr class="{{ $item->isExcluido() ? 'item-excluido' : '' }} linha-processo-pag"
                                    data-honorario="{{ number_format((float) $item->vl_honorario_pai, 2, '.', '') }}"
                                    data-despesa="{{ number_format((float) $item->vl_despesa_pai, 2, '.', '') }}">
                                    <td>
                                        @if($item->cd_processo_pro)
                                        <a href="{{ url('processos/editar/'.\Crypt::encrypt($item->cd_processo_pro)) }}" target="_blank">
                                            {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                                        </a>
                                        @else —
                                        @endif
                                        @if($item->isExcluido())
                                        <span class="label label-default" style="font-size:10px; margin-left:4px;">Excluído</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->ds_descricao_pai }}</td>
                                    <td class="text-right">
                                        R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}
                                        @if($pagamento->podeGerenciarBaixas() && $item->vl_pago_honorario > 0)
                                        <div style="font-size:11px;color:#27ae60;">pago R$ {{ number_format($item->vl_pago_honorario, 2, ',', '.') }}</div>
                                        @endif
                                        @if($pagamento->podeGerenciarBaixas() && $saldoHonItem > 0 && $item->vl_pago_honorario > 0)
                                        <div style="font-size:11px;color:#d68910;">saldo R$ {{ number_format($saldoHonItem, 2, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        R$ {{ number_format($item->vl_despesa_pai, 2, ',', '.') }}
                                        @if($pagamento->podeGerenciarBaixas() && $item->vl_pago_despesa > 0)
                                        <div style="font-size:11px;color:#27ae60;">pago R$ {{ number_format($item->vl_pago_despesa, 2, ',', '.') }}</div>
                                        @endif
                                        @if($pagamento->podeGerenciarBaixas() && $saldoDesItem > 0 && $item->vl_pago_despesa > 0)
                                        <div style="font-size:11px;color:#d68910;">saldo R$ {{ number_format($saldoDesItem, 2, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right"><strong>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</strong></td>
                                    @if($pagamento->podeGerenciarBaixas())
                                    <td class="text-center">
                                        <span class="label label-{{ $statusClass }}">{{ $statusItem }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(! $item->isExcluido() && $pagamento->podePagar())
                                            @if($saldoHonItem > 0)
                                            <button type="button" class="btn btn-xs btn-primary"
                                                    data-toggle="modal" data-target="#modalBaixaProcesso"
                                                    data-item="{{ $item->cd_pagamento_correspondente_item_pai }}"
                                                    data-processo="{{ $item->processo->nu_processo_pro ?? ('#'.$item->cd_processo_pro) }}"
                                                    data-tipo="1"
                                                    data-tipo-label="Honorário"
                                                    data-saldo="{{ number_format($saldoHonItem, 2, '.', '') }}"
                                                    data-saldo-label="R$ {{ number_format($saldoHonItem, 2, ',', '.') }}">
                                                Hon.
                                            </button>
                                            @endif
                                            @if($saldoDesItem > 0)
                                            <button type="button" class="btn btn-xs btn-warning"
                                                    data-toggle="modal" data-target="#modalBaixaProcesso"
                                                    data-item="{{ $item->cd_pagamento_correspondente_item_pai }}"
                                                    data-processo="{{ $item->processo->nu_processo_pro ?? ('#'.$item->cd_processo_pro) }}"
                                                    data-tipo="2"
                                                    data-tipo-label="Despesa"
                                                    data-saldo="{{ number_format($saldoDesItem, 2, '.', '') }}"
                                                    data-saldo-label="R$ {{ number_format($saldoDesItem, 2, ',', '.') }}">
                                                Desp.
                                            </button>
                                            @endif
                                            @if($saldoHonItem <= 0 && $saldoDesItem <= 0)
                                            <span class="text-muted">—</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="linha-vazia-processos"><td colspan="{{ $pagamento->podeGerenciarBaixas() ? 7 : 5 }}" class="text-center text-muted">Nenhum item.</td></tr>
                                @endforelse
                                <tr class="linha-filtro-vazio" style="display:none;">
                                    <td colspan="{{ $pagamento->podeGerenciarBaixas() ? 7 : 5 }}" class="text-center text-muted">Nenhum processo neste filtro.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="4" class="text-right">Total Geral</th>
                                    <th class="text-right">R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</th>
                                    @if($pagamento->podeGerenciarBaixas())
                                    <th colspan="2"></th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Quitar Saldo --}}
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPagar" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-dollar"></i> Quitar Pagamento</h4>
                </div>
                <div class="modal-body">
                    <p>Correspondente: <strong id="modalPagarNome"></strong></p>
                    <p>Saldo total: <strong id="modalPagarValor"></strong></p>
                    <div class="alert alert-warning" style="margin-bottom:10px;">
                        Será gerado um lançamento por processo (honorário e/ou despesa), dando baixa individualmente em cada um.
                    </div>
                    <div class="form-group">
                        <label>O que quitar</label>
                        <select name="escopo" class="form-control" id="modalPagarEscopo">
                            <option value="total">Saldo total (honorários + despesas de todos os processos)</option>
                            <option value="honorario">Somente honorários restantes</option>
                            <option value="despesa">Somente despesas restantes</option>
                        </select>
                        <p class="help-block" style="margin-bottom:0;">
                            Honorários: <span id="modalSaldoHonorario"></span> ·
                            Despesas: <span id="modalSaldoDespesa"></span>
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Data do pagamento</label>
                        <input type="date" name="dt_baixa_pcb" id="modalPagarData" class="form-control" required
                               value="{{ now()->format('Y-m-d') }}">
                        <p class="help-block" style="margin-bottom:0;">
                            Pode ser retroativa — usada em todos os lançamentos gerados neste quitamento.
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Comprovante <small class="text-muted">(PDF ou imagem)</small></label>
                        <input type="file" name="comprovante" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="form-group">
                        <label>Observação <small class="text-muted">(opcional)</small></label>
                        <textarea name="observacao" class="form-control" rows="2"
                                  placeholder="Banco, data, número do comprovante, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal pagar honorário/despesa de um processo --}}
<div class="modal fade" id="modalBaixaProcesso" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST"
                  action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/baixas') }}"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="cd_pagamento_correspondente_item_pai" id="baixaItemId" value="">
                <input type="hidden" name="cd_tipo_baixa_pcb" id="baixaTipoId" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-check-circle"></i> Pagar por processo</h4>
                </div>
                <div class="modal-body">
                    <p>Processo: <strong id="baixaProcessoLabel"></strong></p>
                    <p>Tipo: <strong id="baixaTipoLabel"></strong> · Saldo:
                        <strong id="baixaSaldoLabel"></strong></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Valor (R$)</label>
                                <input type="number" step="0.01" min="0.01" name="vl_baixa_pcb" id="baixaValor"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Data</label>
                                <input type="date" name="dt_baixa_pcb" class="form-control" required
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Comprovante <small class="text-muted">(PDF ou imagem)</small></label>
                        <input type="file" name="comprovante" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Observação</label>
                        <input type="text" name="ds_observacao_pcb" class="form-control"
                               placeholder="Opcional — banco, NF, referência...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
    tr.item-excluido td { color: #999; text-decoration: line-through; }
    tr.item-excluido input.form-control { text-decoration: none; color: #333; }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $('#modalPagar').on('show.bs.modal', function (e) {
            var btn = $(e.relatedTarget);
            $('#modalPagarNome').text(btn.data('nome'));
            $('#modalPagarValor').text(btn.data('saldo'));
            $('#modalSaldoHonorario').text(btn.data('saldo-honorario'));
            $('#modalSaldoDespesa').text(btn.data('saldo-despesa'));
            $('#modalPagarData').val('{{ now()->format('Y-m-d') }}');
            $('#formPagar').attr('action', '{{ url("correspondente/pagamentos") }}/' + btn.data('id') + '/pagar');
        });

        $('#modalBaixaProcesso').on('show.bs.modal', function (e) {
            var btn = $(e.relatedTarget);
            $('#baixaItemId').val(btn.data('item'));
            $('#baixaTipoId').val(btn.data('tipo'));
            $('#baixaProcessoLabel').text(btn.data('processo'));
            $('#baixaTipoLabel').text(btn.data('tipo-label'));
            $('#baixaSaldoLabel').text(btn.data('saldo-label'));
            $('#baixaValor').attr('max', btn.data('saldo')).val(btn.data('saldo'));
        });

        function recalcularTotal() {
            var total = 0;
            $('#formItens tbody tr.linha-processo-pag').each(function () {
                var incluir = $(this).find('.item-incluir').is(':checked');
                var hon  = parseFloat($(this).find('.item-honorario').val()) || 0;
                var des  = parseFloat($(this).find('.item-despesa').val())   || 0;
                var sub  = incluir ? (hon + des) : 0;

                total += sub;
                $(this).attr('data-excluido', incluir ? '0' : '1');
                $(this).toggleClass('item-excluido', !incluir);
                $(this).find('.item-total strong').text('R$ ' + sub.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            });
            $('#totalGeral').text('R$ ' + total.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }

        $(document).on('input', '.item-honorario, .item-despesa', function () {
            var $tr = $(this).closest('tr');
            $tr.attr('data-honorario', parseFloat($tr.find('.item-honorario').val()) || 0);
            $tr.attr('data-despesa', parseFloat($tr.find('.item-despesa').val()) || 0);
            recalcularTotal();
            aplicarFiltroProcessos();
        });
        $(document).on('change', '.item-incluir', recalcularTotal);

        function aplicarFiltroProcessos() {
            var filtro = $('#filtroProcessosPagamento input[name="filtroProcesso"]:checked').val() || 'todos';
            var visiveis = 0;

            $('#tabelaProcessosPagamento tbody tr.linha-processo-pag').each(function () {
                var hon = parseFloat($(this).attr('data-honorario')) || 0;
                var des = parseFloat($(this).attr('data-despesa')) || 0;
                var mostrar = true;

                if (filtro === 'com_despesa') {
                    mostrar = des > 0;
                } else if (filtro === 'somente_honorario') {
                    mostrar = des <= 0 && hon > 0;
                }

                $(this).toggle(mostrar);
                if (mostrar) {
                    visiveis++;
                }
            });

            $('#tabelaProcessosPagamento tbody tr.linha-filtro-vazio').toggle(visiveis === 0 && $('#tabelaProcessosPagamento tbody tr.linha-processo-pag').length > 0);
        }

        $('#filtroProcessosPagamento').on('change', 'input[name="filtroProcesso"]', aplicarFiltroProcessos);
        aplicarFiltroProcessos();
    });
</script>
@endsection
