@extends('layouts.admin')
@section('content')
<div class="row">
<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark">
                        
                        <!-- PAGE HEADER -->
                        <i class="fa-fw fa fa-home"></i> 
                            Início 
                        <span>>  
                            Meus Processos
                        </span>
                    </h1>
                </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="well">
                <h1>
                    <span class="semi-bold">Olá {{ Auth::user()->name }}! Vamos começar? </span> <br>{{ (Session::get('menu_minify') == 'on') ? 'minified' : '' }}
                </h1>                            
            </div>      
        </div>
    </div>

@endsection
