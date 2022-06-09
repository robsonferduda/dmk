@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('contatos') }}">Agenda de Contatos</a></li>
        <li>Detalhes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-book"></i> Contatos <span>> Detalhes </span> <span>> {{ 'Ticket '.$id}}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a href="{{ url('suporte/tickets') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-ticket fa-lg"></i> Listar Tickets</a>
            <a href="{{ url('suporte/ticket/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Dados do ticket </h2>             
                    </header>
                
                    <div class="col-sm-12">
                        <div class="col-md-12">
                            <fieldset style="margin-bottom: 15px;">
                                <legend><i class="fa fa-ticket fa-fw"></i> <strong>Ticket {{$id}}</strong></legend>
                                <div class="row" style="margin-left: 5px;">
                                    <div class="col-md-6">
                                        <p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Título: </strong> {!! $ticket['issue']['subject'] !!}
                                                </li>
                                                <li>
                                                    <strong>Situação: </strong> {{ $ticket['issue']['status']['name'] }}
                                                </li>                                               
                                            </ul>
                                        </p>
                                    </div>    
                                <div>         
                                <div class="row" style="margin-left: 5px;">
                                    <div class="col-md-6">
                                        <p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Anexos: </strong> 
                                                </li>
                                                @foreach($ticket['issue']['attachments'] as $anexo)
                                                    <li>
                                                        <strong><a href="{{ url('suporte/ticket/anexo/'.$anexo['id']) }}" >{{ $anexo['filename'] }}</a></strong>
                                                    </li>     
                                                @endforeach                                          
                                            </ul>
                                        </p>
                                    </div>    
                                <div>                                    
                                </div class="row" style="margin-left: 5px;">
                                    <div class="col-md-12">
                                        <p>
                                            <ul class="list-unstyled">                                               
                                                <li>
                                                    <strong>Descrição: </strong> {!! $ticket['issue']['description'] !!}
                                                </li>
                                            </ul>
                                        </p>
                                    </div>                                
                                </div>
                                <a class="pull-right btn-sm btn-primary" id='btn_comentario' href="#comentario"><i class="fa fa-plus fa-sm"></i> Comentário</a>
                            </fieldset>
                        </div>            
                    </div>  
                </div>
                @foreach($ticket['issue']['journals'] as $comentario)
                    @if(empty($comentario['details']) && $comentario['private_notes'] != true)
                    <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                        <div class="jarviswidget jarviswidget-sortable">
                            <header role="heading" class="ui-sortable-handle">
                                <span class="widget-icon"> <i class="fa fa-comments"></i> </span>
                                @if(in_array($comentario['id'],$idComentarios))
                                    <h2>Comentado por <strong>{{ Auth::user()->name }}</strong></h2>  
                                @else
                                    <h2>Comentado por <strong>Suporte EasyJuris</strong></h2>       
                                @endif     
                            </header>                        
                            <div class="col-sm-12">
                                <div class="col-md-12">
                                    <fieldset style="margin-bottom: 15px;">
                                        <div class="row" style="margin-left: 5px;">
                                            <div class="col-md-12">
                                                <p>                                                   
                                                    {!! $comentario['notes'] !!}
                                                </p>
                                            </div>                                    
                                        </div>                                       
                                    </fieldset>
                                </div>            
                            </div>  
                        </div>
                    </article>
                    @endif
                @endforeach
            </article>
            <article id='comentario' class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" style="display: none">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-comment"></i> </span>
                        <h2>Comentário </h2>                                     
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">                                             
                        <div class="widget-body no-padding">                            
                            {!! Form::open(['id' => 'frm-comentario-ticket', 'url' => 'suporte/ticket/'.$ticket['issue']['id'].'/comentario', 'class' => 'smart-form', 'enctype' => 'multipart/form-data']) !!}                            
                            <div class="row">
                                <div  class="col col-sm-12">
                                    <fieldset>
                                        <div class="row">             
                                            <section class="col col-xs-12 col-lg-12">
                                                <div class="input input-file">
                                                    <span class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Procurar Arquivo</span><input type="text" placeholder="Anexo" readonly="">
                                                </div>
                                            </section>                                
                                            <section class="col col-xs-12 col-lg-12">                                        
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

        $('#btn_comentario').click(function(){
            $('#comentario').css('display','block');
        })
    });
</script>>
@endsection