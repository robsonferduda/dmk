@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Financeiro</li>
        <li>Saídas</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Financeiro <span> > Saídas</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('financeiro/saida/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data de Início<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" required >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data Fim<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  required >                            
                        </section>

                         <section class="col col-md-4">                           
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                         <section class="col col-md-2">
                            <br />                                        
                            <label class="label label-black">Saídas verificadas?</label>  
                            <input type="checkbox" name="todas" id="todas"  {{ (!empty(\Session::get('todas')) ? 'checked' : '') }} > 
                        </section> 
                        <section class="col col-md-2">
                            <br />
                            <button class="btn btn-default" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar </button>
                        </section>    

                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Saídas</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th>Nº Processo</th>                    
                                    <th>Prazo Fatal</th>
                                    <th>Tipo de Serviço</th>    
                                    <th>Correspondente</th>
                                    <th>Valor</th>  
                                    <th><input type="checkbox" class="seleciona-todos" ></th> 
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($saidas as $saida)
                                <tr {{ ($saida->fl_pago_correspondente_pth == 'N') ? 'style=background-color:#fb8e7e' : 'style=background-color:#8ec9bb' }} >
                                    <td>{{ $saida->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($saida->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($saida->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $saida->tipoServico->nm_tipo_servico_tse }}</td>
                                    <td>
                                         @if(!empty($saida->processo->correspondente->contaCorrespondente))
                                            {{ $saida->processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr }} 
                                        @endif               
                                    </td>
                                    <td>{{ $saida->vl_taxa_honorario_correspondente_pth }}</td>
                                    <td style="text-align: center;"><input type="checkbox" class="check-pagamento-correspondente" data-id='{{ $saida->cd_processo_taxa_honorario_pth }}' {{ ($saida->fl_pago_correspondente_pth == 'N') ? '' : 'checked' }}  ></td>                              
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>

    </div>
    <div id="dialog_simple" title="Dialog Simple Title">
        <p>
            Essa ação irá alterar todos os itens em tela.
        </p>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {

         $('#dialog_simple').dialog({
                autoOpen : false,
                width : 600,
                resizable : false,
                modal : true,
                title : "Atenção!",
                buttons : [{
                    html : "<i class='fa fa-exchange'></i>&nbsp; Continuar",
                    "class" : "btn btn-success",
                    click : function() {

                        var ids = Array();
                        if ($(".seleciona-todos").is(':checked') ) {                
                            var checked = 'S';                
                            $(".check-pagamento-correspondente").each(function(index,element){
                                ids[index] = $(this).data('id');            
                            });   
                               
                        }else {
                            var checked = 'N';   
                            $(".check-pagamento-correspondente").each(function(index,element){
                                 ids[index] = $(this).data('id');    
                            });
                        }

                        if(ids.length > 0 ){
                            verificaTodos(ids,checked); 
                        }
                        $(this).dialog("close");
                    }
                }, {
                    html : "<i class='fa fa-times'></i>&nbsp; Cancelar",
                    "class" : "btn btn-danger",
                    click : function() {

                        if ($(".seleciona-todos").is(':checked') ) {                
                            $(".seleciona-todos").prop('checked',false);
                        }else {
                            $(".seleciona-todos").prop('checked',true);
                        }

                        $(this).dialog("close");
                    }
                }]
        });

        $(".seleciona-todos").click(function(){

            $('#dialog_simple').dialog('open');
            
        });

        $(".check-pagamento-correspondente").click(function(){

           verifica($(this));
            
        });

        $( "#correspondente_auto_complete" ).focusout(function(){
           if($("input[name='cd_correspondente_cor']").val() == ''){
                $("#correspondente_auto_complete").val('');
           }
        });

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_correspondente_cor']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-correspondente').click(function(){
            $("input[name='cd_correspondente_cor']").val('');
            $("input[name='nm_correspondente_cor']").val('');

        });

        var verificaTodos = function(ids,checked){

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/correspondente/baixa') }}",
                data:{ids:ids,checked:checked},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            $(".check-pagamento-correspondente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#8ec9bb');   
                                $(this).prop('checked',true);       
                            }); 
                            
                        }else{
                            $(".check-pagamento-correspondente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#fb8e7e');         
                                $(this).prop('checked',false);      
                            }); 
                            
                        }
                    }                                               
                }
            });
        }
    
        var verifica = function(checkbox){

            var input = checkbox;
            var id = checkbox.data('id');
            if (checkbox.is(':checked') ) {
                var checked = 'S';            
            }else {
                var checked = 'N';
            }

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/correspondente/baixa') }}",
                data:{ids:[id],checked:checked},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            input.closest('tr').css('background-color','#8ec9bb');
                        }else{
                            input.closest('tr').css('background-color','#fb8e7e');
                        }
                    }                                               
                }
            });
        }

    });
</script>

@endsection