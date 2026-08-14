@extends('layouts.admin')
@section('content')
<style>
    .cp-page { max-width: 980px; }
    .cp-filters {
        background: linear-gradient(135deg, #f7f9fc 0%, #eef3f8 100%);
        border: 1px solid #dce5ef;
        border-radius: 10px;
        padding: 18px 18px 8px;
        margin-bottom: 22px;
    }
    .cp-filters .form-control {
        border-radius: 6px;
        height: 36px;
    }
    .cp-filters label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: #5a6b7d;
        margin-bottom: 4px;
        display: block;
    }
    .cp-list-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin: 0 0 14px;
    }
    .cp-list-head h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1f2d3d;
    }
    .cp-list-head span {
        font-size: 12px;
        color: #7a8a9a;
    }
    .cp-grid {
        display: grid;
        gap: 12px;
    }
    .cp-item {
        display: grid;
        grid-template-columns: 48px 1fr auto;
        gap: 14px;
        align-items: center;
        background: #fff;
        border: 1px solid #e3ebf3;
        border-radius: 12px;
        padding: 14px 16px;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .cp-item:hover {
        border-color: #b8c9da;
        box-shadow: 0 6px 18px rgba(31, 45, 61, .06);
        transform: translateY(-1px);
    }
    .cp-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #e8f1f8;
        color: #2f6f9f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .cp-icon.is-missing {
        background: #f8eaea;
        color: #b54747;
    }
    .cp-main { min-width: 0; }
    .cp-processo {
        font-size: 15px;
        font-weight: 600;
        color: #1f2d3d;
        margin: 0 0 4px;
        word-break: break-all;
    }
    .cp-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 14px;
        font-size: 12px;
        color: #667788;
        margin: 0;
    }
    .cp-meta strong { color: #445566; font-weight: 600; }
    .cp-side {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }
    .cp-valor {
        font-size: 16px;
        font-weight: 700;
        color: #1a7a4c;
        white-space: nowrap;
    }
    .cp-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
        background: #eef3f8;
        color: #4a5d70;
    }
    .cp-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        font-weight: 600;
    }
    .cp-empty {
        text-align: center;
        padding: 42px 20px;
        border: 1px dashed #cfd9e4;
        border-radius: 12px;
        background: #fafcfe;
        color: #7a8a9a;
    }
    .cp-empty i { font-size: 28px; display: block; margin-bottom: 10px; color: #a0b0c0; }
    @media (max-width: 767px) {
        .cp-item {
            grid-template-columns: 40px 1fr;
        }
        .cp-side {
            grid-column: 1 / -1;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            text-align: left;
            padding-top: 4px;
            border-top: 1px solid #eef2f6;
            margin-top: 2px;
        }
    }
</style>

<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Financeiro</li>
        <li>Comprovantes de Pagamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 cp-page">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Financeiro <span> > Comprovantes de Pagamento</span>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 cp-page">
            @include('layouts/messages')

            <div class="cp-filters">
                <form action="{{ url('correspondente/financeiro/comprovantes-de-pagamento/buscar') }}" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <label>Número do processo</label>
                            <input class="form-control" type="text" name="processo" id="nu_processo_pro"
                                   placeholder="Ex.: 26.04.0292..."
                                   value="{{ $processo ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label>Mês</label>
                            <select name="mes" class="form-control">
                                <option value="">Todos</option>
                                @foreach($meses as $key => $mes)
                                    <option {{ !empty($mesParam) && (int) $key === (int) $mesParam ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-8">
                            <label>Escritório</label>
                            <div class="input-group" style="width:100%">
                                <input type="hidden" name="cd_conta_con" value="{{ $cdContaCon ?? '' }}">
                                <input class="form-control" name="nm_conta_con" type="text"
                                       id="conta_auto_complete" placeholder="Digite 3 caracteres"
                                       value="{{ $nmContaCon ?? '' }}">
                                <span id="limpar-conta" title="Limpar" class="input-group-addon btn btn-warning">
                                    <i class="fa fa-eraser"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="cp-list-head">
                <h2>{{ $tituloLista ?? 'Comprovantes' }}</h2>
                @if(!empty($listaLimitada))
                    <span>Exibindo os 10 mais recentes</span>
                @else
                    <span>{{ count($comprovantes) }} encontrado(s)</span>
                @endif
            </div>

            @if(count($comprovantes))
                <div class="cp-grid">
                    @foreach($comprovantes as $comprovante)
                        <div class="cp-item">
                            <div class="cp-icon {{ empty($comprovante['arquivo_existe']) ? 'is-missing' : '' }}">
                                <i class="fa {{ empty($comprovante['arquivo_existe']) ? 'fa-exclamation-triangle' : 'fa-file-pdf-o' }}"></i>
                            </div>
                            <div class="cp-main">
                                <p class="cp-processo">{{ $comprovante['processo'] }}</p>
                                <p class="cp-meta">
                                    <span><strong>Escritório:</strong> {{ $comprovante['cliente'] }}</span>
                                    <span><strong>Competência:</strong> {{ $comprovante['competencia'] }}</span>
                                    <span><strong>Pagamento:</strong> {{ $comprovante['data'] }}</span>
                                    <span class="cp-badge">{{ $comprovante['tipo'] }}</span>
                                </p>
                            </div>
                            <div class="cp-side">
                                <div class="cp-valor">R$ {{ number_format($comprovante['valor'], 2, ',', '.') }}</div>
                                @if(!empty($comprovante['arquivo_existe']))
                                    <a href="{{ url('correspondente/financeiro/comprovantes-de-pagamento/baixas/'.$comprovante['baixa_id']) }}"
                                       target="_blank" class="btn btn-sm btn-default cp-btn" title="{{ $comprovante['nome'] }}">
                                        <i class="fa fa-download"></i> Baixar
                                    </a>
                                @else
                                    <span class="text-danger" style="font-size:12px">
                                        <i class="fa fa-exclamation-triangle"></i> Indisponível
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cp-empty">
                    <i class="fa fa-folder-open-o"></i>
                    Nenhum comprovante encontrado{{ !empty($listaLimitada) ? ' ainda.' : ' para os filtros informados.' }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#conta_auto_complete").focusout(function () {
            if ($("input[name='cd_conta_con']").val() == '') {
                $("#conta_auto_complete").val('');
            }
        });

        $("#conta_auto_complete").autocomplete({
            source: "{{ url('autocompleteConta') }}",
            minLength: 3,
            select: function (event, ui) {
                $("input[name='cd_conta_con']").val(ui.item.id);
            }
        });

        $('#limpar-conta').click(function () {
            $("input[name='cd_conta_con']").val('');
            $("input[name='nm_conta_con']").val('');
        });
    });
</script>
@endsection
