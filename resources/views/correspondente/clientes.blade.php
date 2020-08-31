@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Clientes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Correspondentes <span> > Clientes</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Clientes</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>    
                                    <th style="width: 20%;">Data de Filiação</th> 
                                    <th style="width: 55%;">Cliente</th>                                                                                                  
                                    <th style="width: 15%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td>{{ date('d/m/Y H:i:s', strtotime($cliente->created_at)) }}</td>
                                        <td>{{ $cliente->conta->nm_razao_social_con }}</td>
                                        <td class="center">
                                            <a title="Meus Dados no Cliente" class="btn btn-default btn-xs btn-m-bottom" href="{{ url('correspondente/cliente/'.\Crypt::encrypt($cliente->cd_conta_con).'/dados') }}"><i class="fa fa-file-text-o"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach                                                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection