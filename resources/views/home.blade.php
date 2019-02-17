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
                <h1>
                    <span class="semi-bold">Olá {{ Auth::user()->name }}! Vamos começar? </span>
                </h1>                            
            </div>      
        </div>
    </div>
</div>
@endsection