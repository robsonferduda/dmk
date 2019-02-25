@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Configurações</a></li>
        <li>Novo Grupo de Cidades</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Configurações <span>> Novo Grupo de Cidades</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <div class="col-md-12">
              <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                    
                    data-widget-colorbutton="false" 
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true" 
                    data-widget-sortable="false"
                    
                -->
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Grupo de Cidades </h2>             
                </header>

                <!-- widget div-->
                <div role="content">                   
                    
                    <!-- widget content -->
                    <div class="widget-body">
                        
                        {!! Form::open(['id' => 'frm-add-grupo', 'url' => 'grupos-de-cidades', 'class' => 'form-group']) !!}
                            
                            <fieldset>
                                 <section>
                                    <div class="input-group">
                                        <label>Nome do Grupo</label>                                
                                        <input required class="form-control" type="text" name="nm_grupo_cidade_grc" value="{{ old('nm_grupo_cidade_grc') ? old('nm_grupo_cidade_grc') : ''}}" />                          
                                    </div>   
                                </section>  
                                <section>
                                    <div class="form-group">
                                        <br />
                                        <label >Estado</label>          
                                        <select multiple id="estado" name="estados[]" class="select2" data-placeholder="Selecione um ou mais estados para buscar as cidades"  >
                                            @foreach($estados as $estado)
                                                <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                            @endforeach

                                        </select>                               
                                    </div>  
                                </section>
                                <section>
                                    <div class="form-group">
                                        <input type="button" id="buscarCidades" class="btn btn-secondary" value="Buscar Cidades" />
                                         <input style="display: inline;" type="button" id="limparCidades" class="btn btn-danger" value="Limpar Cidades" />
                                        <div style="display: inline;" class="msg_aguarde">
                                        
                                        </div>                        
                                    </div>  
                                    <div>
                                        <label class="label">Cidades</label>                              
                                        <select  multiple size="10" id="cidades" name="cidades[]" >
                                        
                                                                            
                                        </select>                               
                                    </div>   
                                </section>                     
                            
                            </fieldset>
                            <footer class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    Cadastrar
                                </button>
                            </footer>
                        {!! Form::close() !!}                       
                        
                    </div>
                    <!-- end widget content -->
                    
                </div>
                <!-- end widget div -->
                
            </div>
            </article>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        var duallistbox = $('select[name="cidades[]"]').bootstrapDualListbox({
            nonSelectedListLabel: 'Cidades Não Selecionadas',
            selectedListLabel: 'Cidades Selecionadas',
            infoText: 'Mostrando {0} registros',
            filterTextClear: 'Mostrar Todos',
            infoTextFiltered: '<span class="label label-warning">Filtrados</span> {0} de {1}',
            infoTextEmpty: 'Não há registros',
            filterPlaceHolder: 'Filtrar Cidades',
            moveSelectedLabel: 'Mover Cidades Selecionadas',
            moveAllLabel: 'Mover Todas Cidades',
            removeSelectedLabel: 'Remover Cidades Selecionadas',
            removeAllLabel: 'Remover Todas Cidades',

            //preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
            //nonSelectedFilter: 'ion ([7-9]|[1][0-2])'
        });

        // function initDualListBox() {
        //     var instance = duallistbox.data('plugin_bootstrapDualListbox');
        //     var nonSelectedList = instance.elements.select1;
        //     var isDualListBoxValidated = !(instance.selectedElements > 0);

        //     nonSelectedList.prop('required', isDualListBoxValidated);
        //     instance.elements.originalSelect.prop('required', false);
        // }

        // initDualListBox();

        $('#estado').select2({
            minimumResultsForSearch: -1,
            placeholder: function(){
                $(this).data('placeholder');
            }        
        });
        // $("form button[type=submit]").click(function() {
        //     var count = duallistbox.find('option :selected').length;
        //     if(count == 0)
        //         return false;
        // });
      
    
        $("#buscarCidades").click(function(){

            var estados = [];
            $.each($("#estado option:selected"), function(){            
                estados.push($(this).val());
            });
           
            if(estados.length <= 0){
                $(".msg_aguarde").html('Escolha um estado').css('color','red');
                return false;
            }

            $.ajax(
            {
                url: '../cidades-por-estado/'+estados.join(","),
                type: 'GET',
                dataType: "JSON",
                beforeSend: function(){
                    $(".msg_aguarde").html('<h3><i class="fa fa-spinner fa-spin"></i> Buscando cidades...</h3>').css('color','black');
                    $("#buscarCidades").attr("disabled", true);
                },
                success: function(response)
                {
                    //duallistbox.empty();
                    duallistbox.find('option').not(':selected').remove();

                    $.each(response,function(index,element){

                        duallistbox.append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+' - '+element.estado.sg_estado_est+'</option>');
                        
                    });
                    duallistbox.bootstrapDualListbox('refresh');
                    $(".msg_aguarde").html('');
                    $("#buscarCidades").attr("disabled", false);

                },
                error: function(response)
                {
                    //console.log(response);
                }
            });

        });

        $('#limparCidades').click(function(){
            duallistbox.empty();
            duallistbox.bootstrapDualListbox('refresh');
        })

    });
</script>
@endsection
