@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Ticket</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Ticket <span>> Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('tickets') }}" data-toggle="modal" href="#addPermissao" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Tickets</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Cadastro de Ticket </h2>                                     
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">                                             
                        <div class="widget-body no-padding">                            
                            {!! Form::open(['id' => 'frm-add-ticket', 'url' => 'suporte/ticket/novo', 'class' => 'smart-form' , 'enctype' => 'multipart/form-data']) !!}                            
                            <div class="row">
                                <div  class="col col-sm-12">
                                    <fieldset>
                                        <div class="row">
                                            <section class="col col-xs-12 col-lg-6">
                                                <label class="label">Título<span class="text-danger">*</span></label>
                                                <label class="input">
                                                    <input required type="text" name="titulo" placeholder="Título" value="{{ old('titulo') }}">
                                                </label>
                                            </section>  
                                            <section class="col col-xs-12 col-lg-6">
                                                <label class="label" >Tipo</label>          
                                                <select name="tipo" class="select2" required>
                                                    <option selected value="">Selecione um tipo</option>
                                                    @foreach($trackers as $tracker) 
                                                        <option value="{{ $tracker['id']}}">{{ $tracker['name']}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>  
                                            <section class="col col-xs-12 col-lg-12">
                                                <div class="input input-file">
                                                    <span class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Procurar Arquivo</span><input type="text" placeholder="Anexo" readonly="">
                                                </div>
                                            </section>  
                                            <section class="col col-xs-12 col-lg-12">
                                                <label class="label">Descrição</label>
                                                <label class="input">
                                                    <textarea class="form-control" rows="4" id="descricao" name="descricao" value="{{old('descricao')}}" required >{{old('descricao')}}</textarea>
                                                </label>
                                            </section>                                                           
                                        </div>                                              
                                    </fieldset>
                                </div>
                            </div>
                            <footer>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Cadastrar
                                </button>
                            </footer>
                            {!! Form::close() !!}                                              
                        </div>
                    </div>     
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $( document ).ready(function() {
        $(function() {
            var validobj = $("#frm-add-ticket").validate({
                ignore: [],
                rules : {
                    titulo : {
                        required: function() 
                            {
                             CKEDITOR.instances.descricao.updateElement();
                            },

                    },
                    descricao:{
                        required: true
                    }
                },
                messages : {
                    titulo : {
                        required : 'Campo Título é Obrigatório'
                    },
                    descricao : {
                        required : 'Campo Descrição é Obrigatório'
                    },
                    tipo : {
                        required: 'Campo Tipo é Obrigatório'
                    }                                               
                },  
                errorPlacement: function (error, element) {
                    var elem = $(element);                        
                    if(element.attr("name") == "tipo") {
                        error.appendTo( element.next("span") );
                    } else {
                        error.insertAfter(element);
                    }
                },         
            });

            CKEDITOR.replace( 'descricao', 
                            { toolbar : [
                            { name: 'document', items : [ 'NewPage','Preview' ] },
                            { name: 'clipboard', items : ['Undo','Redo' ] },
                            { name: 'insert', items : [ 'HorizontalRule' ] },
                             '/',                       
                            { name: 'basicstyles', items : [ 'Bold'] },
                            { name: 'paragraph', items : [ 'BulletedList','-'] },
                
                            ], height: '200px', startupFocus : true,
                            });
        });
    });
</script>>
@endsection

