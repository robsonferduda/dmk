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
                 <i class="fa-fw fa fa-cog"></i>Configurações <span> > Prazos</span>
            </h1>
        </div>
       
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-cog"></i> </span>
                        <h2>Configurações de Prazos</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            {!! Form::open(['id' => 'frm-notificacoes', 'url' => 'configuracoes/prazos/salvar', 'class' => 'smart-form','method' => 'PUT']) !!}                  
                                <fieldset>                  
                                    <section>
                                        <label class="label">Correspondente > Prazo Para Recusar Processo <span class="text-info"> Informe o prazo em horas, usando números inteiros</span> </label>
                                        <label class="label">
                                            <span class="text-warning"> Esse valor corresponde ao prazo em horas que o correspondente tem para recusar o processo pelo sistema, após ter feito a confirmação via email</span> 
                                        </label>
                                        <label class="input">
                                            <input type="text" name="prazo_cancelamento_processo" style="width: 10%" value="{{ $conta->prazo_cancelamento_processo }}"> 
                                        </label>
                                        
                                    </section>
                                </fieldset>
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
@endsection
