@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Clientes</li>
        <li>Relatório de Cobrança</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Clientes <span> > Relatório de Cobrança</span>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form id="form-busca" action="{{ url('cliente/relatorio-cobranca/buscar') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-3">
                            <label class="label label-black">Data prazo fatal início <span class="text-danger">*</span></label><br />
                            <input style="width:100%" class="form-control dt_mask" placeholder="___ /___ /___" type="text"
                                   name="dtInicio" value="{{ isset($dados['dtInicio']) ? $dados['dtInicio'] : '' }}" required>
                        </section>
                        <section class="col col-md-3">
                            <label class="label label-black">Data prazo fatal fim <span class="text-danger">*</span></label><br />
                            <input style="width:100%" class="form-control dt_mask" placeholder="___ /___ /___" type="text"
                                   name="dtFim" value="{{ isset($dados['dtFim']) ? $dados['dtFim'] : '' }}" required>
                        </section>
                        <section class="col col-md-4">
                            <label class="label label-black">Cliente <span class="text-danger">*</span></label><br />
                            <div class="input-group" style="width:100%">
                                <input type="hidden" name="cd_cliente_cli" id="cd_cliente_cli"
                                       value="{{ isset($dados['cd_cliente_cli']) ? $dados['cd_cliente_cli'] : '' }}">
                                <input style="width:100%" class="form-control" id="cliente_auto_complete"
                                       name="nm_cliente_cli" placeholder="Digite 3 caracteres para busca" type="text"
                                       value="{{ isset($dados['nm_cliente_cli']) ? $dados['nm_cliente_cli'] : '' }}" required>
                                <span id="limpar-cliente" title="Limpar cliente" class="input-group-addon btn btn-warning">
                                    <i class="fa fa-eraser"></i>
                                </span>
                            </div>
                        </section>
                        <section class="col col-md-2">
                            <br />
                            <input type="checkbox" name="finalizado" id="finalizado" value="S"
                                {{ (isset($dados['finalizado']) && $dados['finalizado'] == 'S') ? 'checked' : '' }}>
                            <label class="label label-black">Apenas finalizados</label>
                        </section>
                        <section class="col col-md-12" style="margin-top:10px">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                        </section>
                    </div>
                </form>
            </div>

            @if($processos !== null)
                <div class="jarviswidget" id="wid-id-resultado" data-widget-editbutton="false">
                    <header>
                        <span class="widget-icon"><i class="fa fa-table"></i></span>
                        <h2>
                            Resultado
                            @if($processos->isNotEmpty())
                                &mdash; {{ $processos->first()->cliente->nm_razao_social_cli ?? '' }}
                                ({{ $dados['dtInicio'] }} a {{ $dados['dtFim'] }})
                            @endif
                        </h2>
                        @if($processos->isNotEmpty())
                            <div class="widget-toolbar">
                                {{-- Formulário Excel --}}
                                <form method="POST" action="{{ url('cliente/relatorio-cobranca/exportar-excel') }}" style="display:inline">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="dtInicio" value="{{ $dados['dtInicio'] }}">
                                    <input type="hidden" name="dtFim" value="{{ $dados['dtFim'] }}">
                                    <input type="hidden" name="cd_cliente_cli" value="{{ $dados['cd_cliente_cli'] }}">
                                    <input type="hidden" name="nm_cliente_cli" value="{{ $dados['nm_cliente_cli'] }}">
                                    <input type="hidden" name="finalizado" value="{{ $dados['finalizado'] ?? '' }}">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-file-excel-o"></i> Excel
                                    </button>
                                </form>
                                {{-- Formulário PDF --}}
                                <form method="POST" action="{{ url('cliente/relatorio-cobranca/exportar-pdf') }}" style="display:inline; margin-left:5px">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="dtInicio" value="{{ $dados['dtInicio'] }}">
                                    <input type="hidden" name="dtFim" value="{{ $dados['dtFim'] }}">
                                    <input type="hidden" name="cd_cliente_cli" value="{{ $dados['cd_cliente_cli'] }}">
                                    <input type="hidden" name="nm_cliente_cli" value="{{ $dados['nm_cliente_cli'] }}">
                                    <input type="hidden" name="finalizado" value="{{ $dados['finalizado'] ?? '' }}">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </form>
                            </div>
                        @endif
                    </header>
                    <div>
                        <div class="widget-body no-padding">
                            @if($processos->isEmpty())
                                <div class="alert alert-warning" style="margin:15px">
                                    <i class="fa fa-exclamation-triangle"></i> Nenhum processo encontrado para os filtros informados.
                                </div>
                            @else
                                @php
                                    $totalGeral = 0;
                                @endphp
                                <div style="overflow-x:auto">
                                <table id="dt_resultado" class="table table-striped table-bordered table-hover" width="100%" style="font-size:12px">
                                    <thead>
                                        <tr>
                                            <th>Adv. Solicitante</th>
                                            <th>Dt. Solicitação</th>
                                            <th>Dt. Serviço</th>
                                            <th>Autor</th>
                                            <th>Réu</th>
                                            <th>Nº Processo</th>
                                            <th>Vara</th>
                                            <th>Comarca</th>
                                            <th>Serviço</th>
                                            <th>Nº Externo</th>
                                            <th>Honorários</th>
                                            @foreach($despesas as $despesa)
                                                <th>{{ $despesa->nm_tipo_despesa_tds }}</th>
                                            @endforeach
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($processos as $dado)
                                            @php $totalDespesas = 0; @endphp
                                            <tr>
                                                <td>{{ $dado->advogadoSolicitante ? $dado->advogadoSolicitante->nm_contato_cot : '-' }}</td>
                                                <td>{{ $dado->dt_solicitacao_pro ? date('d/m/Y', strtotime($dado->dt_solicitacao_pro)) : '-' }}</td>
                                                <td>{{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : '-' }}</td>
                                                <td>{{ $dado->nm_autor_pro ?? '-' }}</td>
                                                <td>{{ $dado->nm_reu_pro ?? '-' }}</td>
                                                <td>{{ $dado->nu_processo_pro ?? '-' }}</td>
                                                <td>{{ $dado->vara ? $dado->vara->nm_vara_var : '-' }}</td>
                                                <td>{{ $dado->cidade ? $dado->cidade->nm_cidade_cde : '-' }}{{ $dado->cidade && $dado->cidade->estado ? '/' . $dado->cidade->estado->sg_estado_est : '' }}</td>
                                                <td>{{ $dado->honorario && $dado->honorario->tipoServico ? $dado->honorario->tipoServico->nm_tipo_servico_tse : '-' }}</td>
                                                <td>{{ $dado->nu_acompanhamento_pro ?? '-' }}</td>
                                                <td class="text-right">R$ {{ number_format($dado->honorario ? $dado->honorario->vl_taxa_honorario_cliente_pth : 0, 2, ',', '.') }}</td>
                                                @foreach($despesas as $despesa)
                                                    @php
                                                        $item = $dado->tiposDespesa->where('cd_tipo_despesa_tds', $despesa->cd_tipo_despesa_tds)->first();
                                                        $v = $item ? (float) $item->pivot->vl_processo_despesa_pde : 0;
                                                        $totalDespesas += $v;
                                                    @endphp
                                                    <td class="text-right">R$ {{ number_format($v, 2, ',', '.') }}</td>
                                                @endforeach
                                                <td class="text-right" style="font-weight:bold">
                                                    @php
                                                        $taxaHonorario = $dado->honorario ? (float) $dado->honorario->vl_taxa_honorario_cliente_pth : 0;
                                                        $totalLinha = $totalDespesas + $taxaHonorario;
                                                        $totalGeral += $totalLinha;
                                                    @endphp
                                                    R$ {{ number_format($totalLinha, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color:#4a4a4a; color:#fff; font-weight:bold">
                                            <td colspan="{{ 11 + count($despesas) }}" class="text-right">TOTAL GERAL</td>
                                            <td class="text-right">R$ {{ number_format($totalGeral, 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function () {

        $(".dt_mask").mask("99/99/9999");

        var pathCliente = "{{ url('autocompleteCliente') }}";

        $("#cliente_auto_complete").autocomplete({
            source: pathCliente,
            minLength: 3,
            select: function (event, ui) {
                $("#cd_cliente_cli").val(ui.item.id);
            }
        });

        $("#limpar-cliente").click(function () {
            $("#cliente_auto_complete").val('');
            $("#cd_cliente_cli").val('');
        });

        $("#cliente_auto_complete").focusout(function () {
            if ($("#cd_cliente_cli").val() == '') {
                $("#cliente_auto_complete").val('');
            }
        });
    });
</script>
@endsection
