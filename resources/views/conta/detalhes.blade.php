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
         </div>                 
        </div>
    </div>
</div>
@endsection