@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('financeiro/dashboard') }}">Financeiro</a></li>
        <li>Dashboard</li>
    </ol>
</div>
<div id="content">
    <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-bar-chart-o"></i> Dashboard</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="row">
                
                <div class="col-md-4 col-xl-3">
                    <div class="card bg-c-green order-card">
                        <div class="card-block">
                            <h1 class="m-b-20">Entradas</h1>
                            <h1 class="text-right"><i class="fa fa-arrow-circle-down f-left"></i><span>5800,00</span></h1>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-xl-3">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h1 class="m-b-20">Saídas</h1>
                            <h1 class="text-right"><i class="fa fa-arrow-circle-up f-left"></i><span>1800,00</span></h1>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card bg-c-blue order-card">
                        <div class="card-block">
                            <h1 class="m-b-20">Saldo</h1>
                            <h1 class="text-right"><i class="fa fa-dollar f-left"></i><span>3000,00</span></h1>
                        </div>
                    </div>
                </div>

            </div>

        </article>

        <article class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div id="box-grafico-clientes" class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #a90329; margin: 0px;"><i class="fa fa-group"></i> Clientes</h2>
                <div id="donut-graph-clientes" class="chart no-padding"></div>
                <a class="center" style="position: absolute; bottom: 20px;" href="{{ url('correspondente/processos') }}">Ver todos</a>
            </div>
        </article>

        <article class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div id="box-grafico-correspondentes" class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #009688; margin: 0px;"><i class="fa fa-legal"></i> Correspondentes</h2>
                <div id="donut-graph-correspondentes" class="chart no-padding"></div>
                <a class="center" style="position: absolute; bottom: 20px;" href="{{ url('correspondente/processos') }}">Ver todos</a>
            </div>
        </article>

        <article class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div id="box-grafico-despesas" class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #3276b1; margin: 0px;"><i class="fa fa-dollar"></i> Despesas</h2>
                <div id="donut-graph-despesas" class="chart no-padding"></div>
                <a class="center" style="position: absolute; bottom: 20px;" href="{{ url('despesas/lancamentos') }}">Ver todos</a>
            </div>
        </article>

    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        $.ajax({
            url: "../../api/processo/situacao/prazo",
            type: 'GET',
            dataType: "JSON",
            beforeSend: function()
            {
                $('#box-grafico-clientes').loader('show');
            },
            success: function(response)
            {                  
                $('#box-grafico-clientes').loader('hide');  

                if ($('#donut-graph-clientes').length) {
                    Morris.Donut({
                        element : 'donut-graph-clientes',
                        data : response,
                        colors: ['#009688', '#dfb56c', '#953b39'],
                            formatter : function(x) {
                                return x 
                            }
                    });
                }                
            },
            error: function(response)
            {
                $('#box-grafico-clientes').loader('hide');
                $('#donut-graph-clientes').html('<h1 class="center" style="font-size: 60px; margin-top: 50px; color: #d84e44;"><i class="fa fa-times"></i></h1><h4 class="center">Erro ao carregar dados</h4>'); 
            }
        });
    
    });
</script>
@endsection