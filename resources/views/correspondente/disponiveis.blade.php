@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Listar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <button class="btn btn-success pull-right header-btn" data-toggle="modal" data-target="#modalNovoCorrespondente"><i class="fa fa-plus"></i> Novo</button>   
            <button class="btn btn-default pull-right header-btn" data-toggle="modal" data-target="#modalConviteCorrespondente"><i class="fa fa-send"></i> Enviar Convite</button> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <div class="col-md-12">
            <div id="area-correspondentes">
            {{-- Loader inicial --}}
            <div class="text-center" style="padding: 30px;">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <p>Carregando correspondentes...</p>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">

    function carregarCorrespondentes(pagina = 1) {

        var host =  $('meta[name="base-url"]').attr('content');

        $('#area-correspondentes').html('<div class="text-center" style="padding: 30px;"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p>Carregando correspondentes...</p></div>');

        $.ajax({
            url: host+'/correspondentes/ajax?page=' + pagina,
            type: 'GET',
            success: function(response) {

             let tempDiv = $('<div>').html(response);

                    let novosCards = tempDiv.find('#correspondente-cards').html();
                    let novaPaginacaoTop = tempDiv.find('#paginacao-links-top').html();
                    let novaPaginacaoBottom = tempDiv.find('#paginacao-links-bottom').html();

                    $('#area-correspondentes').html(`
                        <div id="paginacao-links-top" class="text-center">${novaPaginacaoTop}</div>
                        <div id="correspondente-cards" class="row">${novosCards}</div>
                        <div id="paginacao-links-bottom" class="text-center">${novaPaginacaoBottom}</div>
                    `);

                    // Reatribui evento de clique para as novas âncoras
                    $('#paginacao-links-top a, #paginacao-links-bottom a').off('click').on('click', function (e) {
                        e.preventDefault();
                        let page = $(this).attr('href').split('page=')[1];
                        carregarCorrespondentes(page);
                    });
            
        },
        error: function() {
            $('#area-correspondentes').html('<p class="text-danger text-center">Erro ao carregar correspondentes.</p>');
        }
        });
    }

    $(document).ready(function() {
        carregarCorrespondentes();
    });
</script>
@endsection