@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Pauta Online</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i>Processos <span> > Status Processo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 box-button-xs" >
            <div class="sub-box-button-xs">
                <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
                <a title="Pauta Online" href="{{ url('processos/pauta/online') }}" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-globe fa-lg"></i> Pauta Online</a>
            </div>
           
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-0" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Status Processo</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="center">Escritório</th>
                                    <th class="center">Correspondente</th>
                                    <th class="center">Cliente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($status as $s)
                                    <tr>
                                        <td>
                                            <span style="background-color: {{ $s->ds_color_stp }}; padding: 5px; color: #fff">{{ $s->nm_status_processo_conta_stp }}</span>
                                        </td>
                                        <td>{{ $s->ds_status }}</td>
                                        <td class="center">
                                            @if($s->fl_visivel_escritorio_stp == 'S' && $s->fl_visivel_escritorio_stp)
                                                <span class="label label-primary">SIM</span>
                                            @else
                                                <span class="label label-default">NÃO</span>
                                            @endif
                                        </td>
                                        <td class="center">
                                            @if($s->fl_visivel_correspondente_stp == 'S' && $s->fl_visivel_correspondente_stp)
                                                <span class="label label-primary">SIM</span>
                                            @else
                                                <span class="label label-default">NÃO</span>
                                            @endif
                                        </td>
                                        <td class="center">
                                            @if($s->fl_visivel_cliente_stp == 'S' && $s->fl_visivel_cliente_stp)
                                                <span class="label label-primary">SIM</span>
                                            @else
                                                <span class="label label-default">NÃO</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">

        $(document).ready(function() {

            
        });

    </script>
@endsection