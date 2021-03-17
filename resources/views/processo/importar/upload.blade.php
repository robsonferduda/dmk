@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Importar</li>
        <li>Upload</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Processos <span> > Importar </span><span> > Upload </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
            @if($failures)
                <div class="alert alert-danger" role="alert">
                    <strong>Erros:</strong>
                  
                    <ul>
                        @foreach ($failures as $failure)
                            @foreach ($failure->errors() as $error)
                                <li>Na linha {{ $failure->row() }} - {{ $error }}</li>
                            @endforeach
                        @endforeach
                  </ul>
                </div>
            @endif
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <div class="row"> 
                    {{ Form::open(array('url' => 'processos/importar', 'method' => 'post',  'enctype' => 'multipart/form-data')) }}               
                    <section class="col col-xs-12 col-md-5 smart-form">
                        <div class="input input-file">
                            <span class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Procurar Arquivo</span><input type="text" placeholder="Arquivo" readonly="">
                        </div>
                    </section>
                     <section class="col col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <button class="btn btn-success"><i class="fa fa-file-excel-o fa-lg"></i><span> Importar</span></button>
                    </section>
                    {{ Form::close() }}
                    <section class="col col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <a href="{{ url('layouts/processos-importar') }}" class="btn btn-default"><i class="fa fa-file-excel-o fa-lg"></i><span> Layout de Importação</span></a>
                    </section>
                </div>
                <div class="row">
                    <section class="col col-xs-12 col-md-5">
                        <div class="col-md-12 upload-arquivo-processo" style="display: none; margin-top: 10px;">           
                            <div class="progress">
                                <div class="progress-bar-upload-arquivo-processo progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </article>
    </div>
</div>      
@endsection
@section('script')
    <script type="text/javascript">
              
    </script>
@endsection