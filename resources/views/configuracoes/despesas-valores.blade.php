@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Honorários por Tipo de Serviço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                 <i class="fa-fw fa fa-cog"></i>Configurações <span> > Despesas > Valores</span>
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
                        <h2>Configurar comportamento</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            {!! Form::open(['id' => 'frm-despesas-valores', 'url' => 'configuracoes/despesas-valores/salvar', 'class' => 'smart-form','method' => 'PUT']) !!}
                                <section>                          
                                    <div class="onoffswitch-container">
                                        <span class="onoffswitch-title">Permitir inserir valores nas despesas não reembolsáveis?</span> 
                                        <span class="onoffswitch">
                                            <input type="checkbox" {{ ($conta->fl_despesa_nao_reembolsavel_con == 'S') ? 'checked' : '' }} name="fl_despesa_nao_reembolsavel_con" class="onoffswitch-checkbox" id="fl_despesa_nao_reembolsavel_con">
                                            <label class="onoffswitch-label" for="fl_despesa_nao_reembolsavel_con"> 
                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                <span class="onoffswitch-switch"></span>
                                            </label> 
                                        </span> 
                                    </div>
                                </section>
                                <footer>
                                    <button type="submit" class="btn btn-success">
                                        Salvar
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
