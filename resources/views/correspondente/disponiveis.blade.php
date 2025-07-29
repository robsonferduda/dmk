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
            <article id="lista-correspondentes" class="row">
                {{-- Conteúdo será carregado aqui via JS --}}
            </article>
            <div id="pagination-container" class="text-center" style="margin-top: 20px;"></div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">

    function carregarCorrespondentes(pagina = 1) {

        var host =  $('meta[name="base-url"]').attr('content');

        $.ajax({
            url: host+'/correspondentes/ajax?page=' + pagina,
            type: 'GET',
            beforeSend: function() {
                $('#lista-correspondentes').html('<p class="text-center">Carregando...</p>');
            },
            success: function(data) {
                $('#lista-correspondentes').html(data);

                // Reaplica eventos de paginação
                $('#lista-correspondentes .pagination a').click(function(e) {
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    carregarCorrespondentes(page);
                });
            },
            error: function() {
                $('#lista-correspondentes').html('<p class="text-danger">Erro ao carregar correspondentes.</p>');
            }
        });
    }

    $(document).ready(function() {
        carregarCorrespondentes();
    });
</script>
@endsection