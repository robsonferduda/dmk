@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-home"></i>Painel Administrativo 
            </h1>
        </div>
        @role('administrator')
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 folder_settings">
                <ul id="sparks" class="">
                    <li class="sparks-info">
                        <h5>TAMANHO DA PASTA
                            <span class="txt-color-purple driver_tamanho"> </span>
                        </h5>
                    </li>
                    <li class="sparks-info">
                        <h5>ESPAÇO EM DISCO
                            <span class="txt-color-blue driver_percentual"> </span>
                        </h5>
                    </li>
                </ul>
            </div>
        @endrole
    </div>

    @role('cliente') 

    @endrole

    @role('administrator')
    <div class="row" id="filtro-periodo" style="margin-bottom: 8px;">
        <div class="col-md-12 col-sm-12 mb-3">
            <form id="formFiltroPeriodo" class="form-inline" style="text-align: right;" method="GET" action="{{ url()->current() }}">
                <div class="form-group">
                    <input type="date" class="form-control" name="data_inicio" id="data_inicio" value="{{ date('d/m/Y') }}">
                </div>
                <div class="form-group">
                    <input type="date" class="form-control" name="data_fim" id="data_fim" value="{{ date('d/m/Y') }}">
                </div>

                <div class="btn-group" role="group" aria-label="Períodos Rápidos">
                    <button type="button" class="btn btn-default periodo-btn" data-dias="7">Última semana</button>
                    <button type="button" class="btn btn-default periodo-btn" data-dias="15">Últimos 15 dias</button>
                    <button type="button" class="btn btn-default periodo-btn" data-dias="30">Últimos 30 dias</button>
                </div>
            </form>
        </div>
    </div>

        <div class="row">           



            <!--
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="alert alert-warning fade in">
                    <button class="close" data-dismiss="alert">×</button>
                    <i class="fa-fw fa fa-warning"></i>
                    <strong>Atenção</strong> Sua conta não foi ativada. Acesse seu email e ative sua conta. Não recebeu o email? <a href="{{ url("/") }}">Clique aqui</a>!
                </div>
            </div>
            -->

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center connect box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @else
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @endif
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4><span>Olá <b>{{ (Auth::user()) ? Auth::user()->name : "Usuário não logado!" }}</b>!</span></h4>
                        <h5>
                            @if(Auth::user()->cd_nivel_niv == 2)
                                <a href="{{ url("usuarios/".\Crypt::encrypt(Auth::user()->id)) }}" class="margin-top-5 margin-bottom-5"> <span>Meu Perfil</span></a>
                            @endif

                            @if(Auth::user()->cd_nivel_niv == 1) 
                                <a href="{{ url("conta/detalhes/".\Crypt::encrypt(Auth::user()->cd_conta_con)) }}"> Minha Conta</a>  
                            @endif
                        </h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>  

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <a href="{{ url('processos') }}"><img src="{{ asset('img/processo.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4>
                            <span><b>Processos</b></span>
                        </h4>
                        
                        <h5>
                            @if(count($processos) > 0)
                                <span>({{ count($processos) }})</span>
                            @endif
                            <a href="{{ url('processos') }}">Meus Processos</a>
                        </h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div> 

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <a href="{{ url('processos') }}"><img src="{{ asset('img/legal.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4><span><b>Correspondentes</b></span></h4>                    
                        <h5><a href="{{ url('correspondentes') }}">Meus Correspondentes</a></h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4 " id="top5-correspondentes">
            
            </div>
        </div>
    @endrole
</div>
@endsection
@section('script')
    <script type="text/javascript">

        var host =  $('meta[name="base-url"]').attr('content');

        function carregarTop5Correspondentes(data_inicio, data_fim) {
            $.ajax({
                url: host+"/dashboard/correspondentes",
                type: 'GET',
                data: {
                    data_inicio: data_inicio,
                    data_fim: data_fim
                },
                beforeSend: function () {
                    $("#top5-correspondentes").html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>');
                },
                success: function (html) {
                    $("#top5-correspondentes").html(html);
                },
                error: function () {
                    $("#top5-correspondentes").html('<p class="text-danger text-center">Erro ao carregar os dados.</p>');
                }
            });
        }

        $(document).ready(function() {

            // Função para formatar data como yyyy-mm-dd
            function formatarData(data) {
                return data.toISOString().split('T')[0];
            }

            // Define datas iniciais: últimos 7 dias
            const hoje = new Date();
            const fim = formatarData(hoje);
            const inicio = formatarData(new Date(hoje.setDate(hoje.getDate() - 7)));

            $('#data_inicio').val(inicio);
            $('#data_fim').val(fim);

            if (inicio && fim) {
                carregarTop5Correspondentes(inicio, fim);
            }

            // Trigger on date change
            $('#data_inicio, #data_fim').on('change', function () {
                const di = $('#data_inicio').val();
                const df = $('#data_fim').val();
                if (di && df) {
                    carregarTop5Correspondentes(di, df);
                }
            });

            $(function() {
                $('.periodo-btn').on('click', function() {
                    var dias = parseInt($(this).data('dias'));
                    var hoje = new Date();
                    var dataFim = hoje.toISOString().split('T')[0];

                    var dataInicio = new Date();
                    dataInicio.setDate(dataInicio.getDate() - dias);
                    var dataInicioStr = dataInicio.toISOString().split('T')[0];

                    $('#data_inicio').val(dataInicioStr);
                    $('#data_fim').val(dataFim);

                    carregarTop5Correspondentes(dataInicioStr, dataFim);
                });
            });

        });
    </script>
@endsection