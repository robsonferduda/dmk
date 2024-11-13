@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('processos') }}">Processos</a></li>
        <li><a href="{{ url('notificacao/processos') }}">Notificações</a></li>
        <li>Grupos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i> Processos <span> > Notificações </span><span class="ml-5"> > Grupos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a href="{{ url('notificacao/processos') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-send"></i> Grupos de Notificações</a>   
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">   
                <div class="row">
                    <div class="col-md-12">     
                        <h4 style="margin-left: 15px; margin-bottom: 5px; font-weight: bold;">Novo Grupo</h4>                   
                        {!! Form::open(['id' => 'frm-add-conta', 'url' => 'correspondente/cadastro/conta', 'id' => 'frmAddCorrespondente', 'class' => 'smart-form client-form']) !!}
                            <fieldset>
                                    <h5 style="margin-bottom: 10px;"><strong>Dados do Correspondente</strong></h5>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input type="text" name="nm_razao_social_con" id="nm_razao_social_con" placeholder="Nome" value="{{ old('nm_razao_social_con') }}">
                                        <b class="tooltip tooltip-bottom-right">Nome Completo</b> </label>
                                    </section>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-envelope"></i>
                                        <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                                        <b class="tooltip tooltip-bottom-right">Email do Correspondente</b> </label>
                                    </section>
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-send"></i> Adicionar</button>                                 
                            </footer>
                        {!! Form::close() !!} 
                    </div>
                    </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

       
    });
</script>
@endsection