@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-home"></i>Painel Administrativo 
            </h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 folder_settings">
            <ul id="sparks" class="">
                <li class="sparks-info">
                    <h5>TAMANHO DA PASTA
                        <span class="txt-color-purple driver_tamanho"> </span>
                    </h5>
                </li>
                <li class="sparks-info">
                    <h5>ESPAÇO EM DISCO
                        <span class="txt-color-blue driver_percentual"> </span>
                    </h5>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="row">

        <!--
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="alert alert-warning fade in">
                <button class="close" data-dismiss="alert">×</button>
                <i class="fa-fw fa fa-warning"></i>
                <strong>Atenção</strong> Sua conta não foi ativada. Acesse seu email e ative sua conta. Não recebeu o email? <a href="{{ url("/") }}">Clique aqui</a>!
            </div>
        </div>
        -->

        <div class="col-sm-12 col-md-6 col-lg-4">                
            <div class="well text-center connect box-home" style="min-height: 110px;">
                <div class="col-sm-12 col-md-6 col-lg-3">
                    @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                        <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                    @else
                        <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                    @endif
                </div>
                <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                    <h4><span>Olá <b>{{ (Auth::user()) ? Auth::user()->name : "Usuário não logado!" }}</b>!</span></h4>
                    <h5>
                        @if(Auth::user()->cd_nivel_niv == 2)
                            <a href="{{ url("usuarios/".\Crypt::encrypt(Auth::user()->id)) }}" class="margin-top-5 margin-bottom-5"> <span>Meu Perfil</span></a>
                        @endif

                        @if(Auth::user()->cd_nivel_niv == 1) 
                            <a href="{{ url("conta/detalhes/".\Crypt::encrypt(Auth::user()->cd_conta_con)) }}"> Minha Conta</a>  
                        @endif
                    </h5>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>  

        <div class="col-sm-12 col-md-6 col-lg-4">                
            <div class="well text-center box-home" style="min-height: 110px;">
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <a href="{{ url('processos') }}"><img src="{{ asset('img/processo.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                    <h4>
                        <span><b>Processos</b></span>
                    </h4>
                    
                    <h5>
                        @if(count($processos) > 0)
                            <span>({{ count($processos) }})</span>
                        @endif
                        <a href="{{ url('processos') }}">Meus Processos</a>
                    </h5>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div> 

        <div class="col-sm-12 col-md-6 col-lg-4">                
            <div class="well text-center box-home" style="min-height: 110px;">
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <a href="{{ url('processos') }}"><img src="{{ asset('img/legal.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                    <h4><span><b>Correspondentes</b></span></h4>                    
                    <h5><a href="{{ url('correspondentes') }}">Meus Correspondentes</a></h5>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

    </div>
</div>
@endsection