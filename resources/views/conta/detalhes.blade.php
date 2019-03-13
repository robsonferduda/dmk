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
                <i class="fa-fw fa fa-user"></i> Conta 
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
         <div class="well profile" style="display: inline-block;">
            <div class="col-sm-12">
                <div class="col-xs-12 col-sm-3 text-center">
                    <figure>
                        <img src="{{ asset('img/users/user.png') }}" alt="" class="img-circle img-responsive">
                        <figcaption class="ratings">
                            <p>Avaliação
                            <a href="#">
                                <span class="fa fa-star"></span>
                            </a>
                            <a href="#">
                                <span class="fa fa-star"></span>
                            </a>
                            <a href="#">
                                <span class="fa fa-star"></span>
                            </a>
                            <a href="#">
                                <span class="fa fa-star"></span>
                            </a>
                            <a href="#">
                                 <span class="fa fa-star-o"></span>
                            </a> 
                            </p>
                        </figcaption>
                    </figure>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <h2>{{ $conta->nm_razao_social_con }} <a href="{{ url('conta/atualizar/'.$conta->cd_conta_con) }}"><span class="fa fa-edit"></span></a></h2>
                    <p><strong>Nome Fantasia: </strong> {{ $conta->nm_fantasia_con }} </p>
                    <p><strong>Tipo: </strong> {{ $conta->tipoPessoa()->first()->nm_tipo_pessoa_tpp }} </p>
                    <p><strong>Contatos </strong>  
                        <ul class="list-unstyled">
                            <li>
                                <p class="text-muted">
                                    <i class="fa fa-phone"></i>&nbsp;&nbsp;<span class="txt-color-darken"></span>
                                </p>
                            </li>
                            <li>
                                <p class="text-muted">
                                    <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:simmons@smartadmin">{{ Auth::user()->email }}</a>
                                </p>
                            </li>
                        </ul>
                    </p>
                </div>             
            </div>            
            <div class="col-xs-12 divider text-center">
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> {{ count($usuarios) }} </strong></h2>                    
                    <p><small>Usuários</small></p>
                    <a href="{{ url('usuarios/novo') }}" class="btn btn-primary btn-block"><span class="fa fa-user"></span> Novo Usuário </a>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> 0 </strong></h2>                    
                    <p><small>Processos</small></p>
                    <button class="btn btn-success btn-block"><span class="fa fa-archive"></span> Novo Processo </button>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong>0</strong></h2>                    
                    <p><small>Colaboradores</small></p>
                    <button class="btn btn-warning btn-block"><span class="fa fa-legal"></span> Novo Correspondente </button>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong><span class="fa fa-gear"></strong></h2>                    
                    <p><small>Configurações da Conta</small></p>
                    <div class="btn-group dropup btn-block">
                      <button type="button" class="btn btn-primary" style="width: 90%;"><span class="fa fa-user"></span> Opções </button>
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu text-left" role="menu">
                        <li><a href="#"><span class="fa fa-credit-card pull-right"></span> Dados de Pagamento</a></li>
                        <li><a href="#"><span class="fa fa-list pull-right"></span> Histórico de Pagamentos</a></li>
                        <li class="divider"></li>
                        <li><a href="#"><span class="fa fa-times pull-right"></span>Cancelar Assinatura</a></li>
                      </ul>
                    </div>
                </div>
            </div>
         </div>                 
        </div>
    </div>
</div>
@endsection