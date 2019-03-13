@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-home"></i>Início 
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <h4>
                    <span class="semi-bold">Bem-vindo <strong>{{ (Auth::user()) ? Auth::user()->name : "Usuário não logado!" }}!</strong> </span>
                </h4> 
                @if(Auth::user()->tipoPerfil()->first()->dc_nivel_niv == 'ADMIN')
                    <a href="{{ url("conta/detalhes/".\App\Entidade::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first()->cd_conta_con) }}">Minha Conta</a>  |
                @endif
                <a href="{{ url("usuarios/".Auth::user()->id) }}">Meu Perfil</a>
                    
                @if(\App\Conta::where('cd_conta_con',\App\Entidade::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first()->cd_conta_con)->first()->cd_tipo_pessoa_tpp == null)
                    <div class="alert alert-warning fade in">
                        <button class="close" data-dismiss="alert">×</button>
                            <i class="fa-fw fa fa-warning"></i><strong> Atenção!</strong> Seu cadastro está incompleto, <a href="{{ url('conta/atualizar/'.\App\Entidade::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first()->cd_conta_con) }}">clique aqui</a> para atualizar seus dados!
                    </div>
                @endif                   
            </div>      
        </div>
    </div>
</div>
@endsection