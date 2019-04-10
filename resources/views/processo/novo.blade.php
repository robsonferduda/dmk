@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Usuários</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Novo</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
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
                    <h2>Cadastro de Usuário </h2>             
                    
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <!-- widget div-->
                <div role="content">
                    
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                        
                    </div>
                    <!-- end widget edit box -->
                    
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        
                        {!! Form::open(['id' => 'frm-add-usuario', 'url' => 'usuarios', 'class' => 'smart-form']) !!}
                        <div class="row">
                            <div  class="col col-6">
                                <header>
                                    <i class="fa fa-user"></i> Dados Básicos
                                </header>

                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-sm-12">
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control ui-autocomplete-input" placeholder="Cliente..." type="text" id="client" autocomplete="off">
                                            </label>
                                        </section>
                                        
                                       
                                        
                                    </div>                                                                                     
                                </fieldset>
                        </div>
                        <div  class="col col-6">
                            <header>
                                <i class="fa fa-phone"></i> Contatos
                            </header>
                            <fieldset>
                               
                             
                            </fieldset>
                        </div>
                        </div>
                     
                          
                        <footer>
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
        var path = "{{ url('autocompleteCliente') }}";
        $( "#client" ).autocomplete({
          source: path,
          minLength: 3,
          select: function(event, ui) {
            alert(ui);
          }
        });
    });
   
    
</script>

@endsection
