@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Mural</a></li>
        <li>Processos</li>
        <li>Acompanhamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i>Processos <span> > Acompanhamento</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 box-button-xs">
            <div class="sub-box-button-xs">
                 <a href="{{ url('home') }}" class="btn btn-default pull-right header-btn" ><i class="fa fa-desktop"></i> Mural</a>
            </div>
        </div>
    </div>
    <div class="row">
    
        <div class="col-md-12">
            @include('layouts/messages')
        </div>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('processos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <input type="hidden" name="acompanhamento" value="S">
                    <div class="row" style="margin-bottom: 10px;">
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Prazo Fatal</label><br />
                            <input style="width: 100%" class="form-control date-mask" type="text" id="dt_prazo_fatal_pro" id="dt_prazo_fatal_pro" placeholder="___/___/____" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Número do Processo</label><br />
                            <input style="width: 100%" class="form-control" type="text" id="nu_processo_pro" placeholder="Nº Processo" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Réu</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="reu" class="form-control" id="reu" placeholder="Réu" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Autor</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="autor" class="form-control" id="autor" placeholder="Autor" value="{{ !empty($autor) ? $autor : '' }}" >                            
                        </section>

                        <section class="col col-md-4 col-lg-3 box-select2"> 
                            <select name="cd_cliente" id="cd_cliente" class="select2">
                                <option value="">Cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->cd_conta_con }}">{{ $cliente->conta->nm_razao_social_con }}</option>
                                @endforeach
                            </select>
                        </section> 
                        
                        <section class="col col-md-4 col-lg-3 box-select2"> 
                            <select name="status" id="status" class="select2">
                                <option value="">Status do Acompanhamento</option>
                                <option value="dentro-prazo">Dentro do Prazo</option>
                                <option value="data-limite">Data Limite</option>
                                <option value="atrasado">Atrasado</option>
                            </select>
                        </section> 

                        <section class="col col-md-4 col-lg-3 box-select2">         
                            <select  id="estado" name="cd_estado_est" class="select2">
                                <option selected value="">Estado</option>
                                    @foreach(App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                    @endforeach
                            </select> 
                        </section>

                        <section class="col col-md-4 col-lg-3 box-select2">         
                            <select  id="cidade"  name="cd_cidade_cde" class="select2" required>
                                <option selected value="">Comarca</option>
                            </select> 
                        </section>  
                    </div><hr>
                    <div class="row center">
                        <button class="btn btn-primary btn-sm" type="button" id="btnBuscarProcessosAndamento"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </article>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: -15px;">
            <div class="col-sm-12 col-md-3" style="padding: 5px 0px;">
                <div class="input-group input-group-md">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-filter"></i></span>
                    <div>
                        <input type="text" class="form-control" id="filter" name="filter" class="filter"/>                                          
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-3" style="padding: 5px 8px; margin-top: 12px;">
                <h4 style="font-size: 13px;" id="label-total-processos"></h4>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6" style=" margin-top: 12px;">
                <section class="pull-right">
                    <select id="filtro-pagination">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>  <label class="hidden-xs">Registros por página</label>
                </section>
            </div>
        </article>

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 body-acompanhamento">

        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        function formataNulo(valor){
            return valor != null ? valor : "";
        }

        function formataNuloResposta(valor){
            return valor != null ? valor : "Não informado";
        }

        if (!RegExp.escape) {
            RegExp.escape = function (value) {
                return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
            };
        }

        var $medias = $('.box-acompanhamento'), $h4s = $medias.find('> .box-processo > .box-content > h6');

        $('#filter').keyup(function () {
            var filter = this.value,
                regex;

            if (filter && $medias) {

                regex = new RegExp(RegExp.escape(this.value), 'i')

                var $found = $h4s.filter(function () {
                    return regex.test($(this).text())
                }).closest('.box-acompanhamento').show();

                $medias.not($found).hide()
            } else {
                $medias.show();
            }
        });

        $.ajax({
                
                url: '../../api/processo/correspondente/andamento',
                type: 'GET',
                dataType: "JSON",
                beforeSend: function(){
                    $("#label-total-processos").html("");
                    $('.container-acompanhamento').loader('show'); 
                    $('.pagination').empty();                      
                },
                success: function(response){ 

                    $("#label-total-processos").html("<strong>TOTAL DE PROCESSOS</strong>: "+response.length);   

                    $.each(response,function(index,data){

                        $(".body-acompanhamento")
                        .append('<div class="well box-acompanhamento" style="padding: 10px 15px; border: none; background: '+data.background+';">'+
                            
                            '<div class="row box-processo">'+
                                '<div class="hidden-xs hidden-sm hidden-md col-lg-12 box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+' <strong>'+data.nm_status_processo_conta_stp+'</strong> <strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+

                                '<div class="col-xs-12 col-sm-12 col-md-12 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+'</h6></div>'+
                                '<div class="col-xs-12 col-sm-8 col-md-8 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong>'+data.nm_status_processo_conta_stp+'</strong></h6></div>'+
                                '<div class="col-xs-12  col-sm-4 col-md-4 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Escritório</strong>: '+data.nm_razao_social_con+'</h6>'+ 
                                    '<h6><strong>Prazo Fatal</strong>: '+data.dt_prazo_fatal_pro+' '+formataNulo(data.hr_audiencia_pro)+'</h6>'+ 
                                    '<h6><strong>Status</strong>: '+data.nm_status_processo_conta_stp+'</h6>'+                                   
                                    '<h6><strong>Tipo de Serviço</strong>: '+data.nm_tipo_servico_tse+'</h6>'+                                                                  
                                '</div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Vara/Cidade</strong>: '+formataNuloResposta(data.nm_vara_var)+'/'+data.nm_cidade_cde+'-'+data.sg_estado_est+'</h6>'+ 
                                    '<h6><strong>Autor</strong>: '+formataNuloResposta(data.nm_autor_pro)+'</h6>'+
                                    '<h6><strong>Réu</strong>: '+formataNuloResposta(data.nm_reu_pro)+'</h6>'+ 
                                '</div>'+
                                '<div class="hidden-xs col-sm-12 col-md-12 pull-right">'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> Acompanhamento</a> '+
                                '</div>'+
                                '<div class="hidden-md hidden-sm hidden-lg col-md-6">'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> </a>'+
                                '</div>'+
                            '</div>'+
                        '</div>');

                    });

                    $('.container-acompanhamento').loader('hide'); 
                    $(".body-acompanhamento").pagify(10, ".box-acompanhamento");
                    $medias = $('.box-acompanhamento');
                    $h4s = $medias.find('> .box-processo > .box-content > h6');
                                                      
                },
                error: function(response)
                {
                    alert("Erro ao processar requisição");
                    $('.container-acompanhamento').loader('hide'); 
                }
        });

        $("#btnBuscarProcessosAndamento").click(function(){

            processo = $("#nu_processo_pro").val();
            status = $("#status").val();
            reu = $("#reu").val();
            autor = $("#autor").val();
            data = $("#dt_prazo_fatal_pro").val();
            comarca = $("#cidade").val();
            cliente = $("#cd_cliente").val();

            $.ajax({
                
                url: '../../processos/buscar/andamento',
                type: 'POST',
                data: {"processo": processo, "responsavel": null, "tipo": null, "servico": null, "status": status, "reu": reu, "autor": autor, "data": data, "comarca": comarca, "flag": true, "cliente": cliente },
                dataType: "JSON",
                beforeSend: function(){
                    $("#label-total-processos").html("");
                    $('.container-acompanhamento').loader('show');  
                    $('.pagination').empty();                     
                },
                success: function(response){ 

                    $(".body-acompanhamento").empty();
                    $("#label-total-processos").html("<strong>TOTAL DE PROCESSOS</strong>: "+response.length);    

                    $.each(response,function(index,data){

                        $(".body-acompanhamento")
                        .append('<div class="well box-acompanhamento" style="padding: 10px 15px; border: none; background: '+data.background+';">'+
                            
                            '<div class="row box-processo">'+
                                '<div class="hidden-xs hidden-sm hidden-md col-lg-12 box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+' <strong>'+data.nm_status_processo_conta_stp+'</strong> <strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+

                                '<div class="col-xs-12 col-sm-12 col-md-12 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+'</h6></div>'+
                                '<div class="col-xs-12 col-sm-8 col-md-8 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong>'+data.nm_status_processo_conta_stp+'</strong></h6></div>'+
                                '<div class="col-xs-12  col-sm-4 col-md-4 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Escritório</strong>: '+data.nm_razao_social_con+'</h6>'+ 
                                    '<h6><strong>Prazo Fatal</strong>: '+data.dt_prazo_fatal_pro+' '+formataNulo(data.hr_audiencia_pro)+'</h6>'+ 
                                    '<h6><strong>Status</strong>: '+data.nm_status_processo_conta_stp+'</h6>'+                                   
                                    '<h6><strong>Tipo de Serviço</strong>: '+data.nm_tipo_servico_tse+'</h6>'+                                                                  
                                '</div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Vara/Cidade</strong>: '+formataNuloResposta(data.nm_vara_var)+'/'+data.nm_cidade_cde+'-'+data.sg_estado_est+'</h6>'+ 
                                    '<h6><strong>Autor</strong>: '+formataNuloResposta(data.nm_autor_pro)+'</h6>'+
                                    '<h6><strong>Réu</strong>: '+formataNuloResposta(data.nm_reu_pro)+'</h6>'+ 
                                '</div>'+
                                '<div class="hidden-xs col-sm-12 col-md-12 pull-right">'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> Acompanhamento</a> '+
                                '</div>'+
                                '<div class="hidden-md hidden-sm hidden-lg col-md-6">'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> </a>'+
                                '</div>'+
                            '</div>'+
                        '</div>');

                    });

                    $('.container-acompanhamento').loader('hide'); 
                    //$(".body-acompanhamento").pagify(10, ".box-acompanhamento");
                                                    
                },
                error: function(response)
                {
                    alert("Erro ao processar requisição");
                    $('.container-acompanhamento').loader('hide'); 
                }
            });

        });  

        $("#filtro-pagination").change(function(){
        $(".body-acompanhamento").pagify($(this).val(), ".box-acompanhamento");
    });

        var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Carregando...</option>');
                            $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Selecione</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('#cidade').trigger('change');     
                            $('#cidade').prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                });
            }
        }

        $("#estado").change(function(){
            buscaCidade(); 
        });

    });

    (function($) {
    var pagify = {
        items: {},
        container: null,
        totalPages: 1,
        perPage: 3,
        currentPage: 0,
        createNavigation: function() {
            this.totalPages = Math.ceil(this.items.length / this.perPage);

            $('.pagination', this.container.parent()).remove();
            var pagination = $('<ul class="pagination"></ul>').append('<li><a class="nav prev disabled" data-next="false"><</a></li>');

            for (var i = 0; i < this.totalPages; i++) {
                var pageElClass = "page";
                if (!i)
                    pageElClass = "page current";
                var pageEl = '<li><a class="' + pageElClass + '" data-page="' + (
                i + 1) + '">' + (
                i + 1) + "</a></li>";
                pagination.append(pageEl);
            }
            pagination.append('<li><a class="nav next" data-next="true">></a></li>');

            this.container.after(pagination);

            var that = this;
            $("body").off("click", ".nav");
            this.navigator = $("body").on("click", ".nav", function() {
                var el = $(this);
                that.navigate(el.data("next"));
            });

            $("body").off("click", ".page");
            this.pageNavigator = $("body").on("click", ".page", function() {
                var el = $(this);
                that.goToPage(el.data("page"));
            });
        },
        navigate: function(next) {
            // default perPage to 5
            if (isNaN(next) || next === undefined) {
                next = true;
            }
            $(".pagination .nav").removeClass("disabled");
            if (next) {
                this.currentPage++;
                if (this.currentPage > (this.totalPages - 1))
                    this.currentPage = (this.totalPages - 1);
                if (this.currentPage == (this.totalPages - 1))
                    $(".pagination .nav.next").addClass("disabled");
                }
            else {
                this.currentPage--;
                if (this.currentPage < 0)
                    this.currentPage = 0;
                if (this.currentPage == 0)
                    $(".pagination .nav.prev").addClass("disabled");
                }

            this.showItems();
        },
        updateNavigation: function() {

            var pages = $(".pagination .page");
            pages.removeClass("current");
            $('.pagination .page[data-page="' + (
            this.currentPage + 1) + '"]').addClass("current");
        },
        goToPage: function(page) {

            this.currentPage = page - 1;

            $(".pagination .nav").removeClass("disabled");
            if (this.currentPage == (this.totalPages - 1))
                $(".pagination .nav.next").addClass("disabled");

            if (this.currentPage == 0)
                $(".pagination .nav.prev").addClass("disabled");
            this.showItems();
        },
        showItems: function() {
            this.items.hide();
            var base = this.perPage * this.currentPage;
            this.items.slice(base, base + this.perPage).show();

            this.updateNavigation();
        },
        init: function(container, items, perPage) {
            this.container = container;
            this.currentPage = 0;
            this.totalPages = 1;
            this.perPage = perPage;
            this.items = items;
            this.createNavigation();
            this.showItems();
        }
    };

    // stuff it all into a jQuery method!
    $.fn.pagify = function(perPage, itemSelector) {
        var el = $(this);
        var items = $(itemSelector, el);

        // default perPage to 5
        if (isNaN(perPage) || perPage === undefined) {
            perPage = 3;
        }

        // don't fire if fewer items than perPage
        if (items.length <= perPage) {
            return true;
        }

        pagify.init(el, items, perPage);
    };
})(jQuery);

</script>
@endsection