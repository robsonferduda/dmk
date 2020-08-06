@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                 <i class="fa-fw fa fa-cog"></i>Configurações <span> > Preferências de Notificações</span>
            </h1>
        </div>
       
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <div class="col-md-12">            
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-cog"></i> </span>
                        <h2>Preferências de Notificação</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            {!! Form::open(['id' => 'frm-notificacoes', 'url' => 'configuracoes/notificacoes/salvar', 'class' => 'smart-form','method' => 'PUT']) !!}
                                <section>
                                    <label class="text-warning" style="margin-bottom: 5px;"><i class="fa fa-warning"></i> Você deve confirmar a alteração dos valores clicando em "Salvar"</label>
                                </section>
                                <section>                          
                                    <div class="onoffswitch-container">
                                        <span class="onoffswitch-title">Enviar notificações via email nas ações de processo?</span> 
                                        <span class="onoffswitch">
                                            <input type="checkbox" {{ ($conta->fl_envio_notificacao_con == 'S') ? 'checked' : '' }} name="fl_envio_notificacao_con" class="onoffswitch-checkbox" id="fl_envio_notificacao_con">
                                            <label class="onoffswitch-label" for="fl_envio_notificacao_con"> 
                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                <span class="onoffswitch-switch"></span>
                                            </label> 
                                        </span> 
                                    </div>
                                    <br/>
                                    <div class="onoffswitch-container">
                                        <span class="onoffswitch-title">Enviar notificações via email nas ações de correspondentes?</span> 
                                        <span class="onoffswitch">
                                            <input type="checkbox" {{ ($conta->fl_notificacao_correspondente_con == 'S') ? 'checked' : '' }} name="fl_notificacao_correspondente_con" class="onoffswitch-checkbox" id="fl_notificacao_correspondente_con">
                                            <label class="onoffswitch-label" for="fl_notificacao_correspondente_con"> 
                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                <span class="onoffswitch-switch"></span>
                                            </label> 
                                        </span> 
                                    </div>
                                </section>
                                <footer>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Salvar
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
