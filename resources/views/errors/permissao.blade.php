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
                <i class="fa-fw fa fa-warning"></i> Alerta do Sistema 
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <h4>
                    <span class="semi-bold"><i class="fa-fw fa fa-times"></i> Você não tem permissão para acessar essa página. Foi registrado um log dessa requisição e um alerta de uso indevido do sistema.</strong> </span>
                </h4>            
            </div>      
        </div>
    </div>
</div>
@endsection