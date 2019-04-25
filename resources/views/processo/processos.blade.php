@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Lista</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('Processos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Nome</span>
                        <input size="30" type="text" name="nome" class="form-control" id="Nome" placeholder="Nome" >
                    </div>                    
                    <div class="form-group">
                        <select name="perfil" class="form-control">
                            <option value="">Perfil</option>
                            @foreach(\App\Nivel::all() as $nivel)
                                <option value="{{ $nivel->cd_nivel_niv }}">{{ $nivel->dc_nivel_niv }}</option>
                            @endforeach
                        </select>
                    </div>                
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a href="{{ url('Processos') }}" class="btn btn-primary" ><i class="fa fa-list"></i> Listar</a>
                </form>
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Processos</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 20%;">Nº Processo</th>
                                    <th style="width: 10%;">Data do Cadastro</th>
                                    <th style="width: 30%;">Cliente</th>
                                   
                                    <th style="width: 20%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processos as $processo)
                                    <tr>                                    
                                        <td>{{ $processo->nu_processo_pro }}</td>
                                        <td>{{ date('d/m/Y H:i', strtotime($processo->created_at)) }}</td>
                                        <td>{{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}</td>
                                      
                                        <td>
                                            <a class="btn btn-default btn-xs"  href="{{ url('processos/detalhes/'.$processo->cd_processo_pro) }}"><i class="fa fa-folder"></i> Detalhes</a>
                                            <a class="btn btn-primary btn-xs editar_vara" href="{{ url('processos/editar/'.$processo->cd_processo_pro) }}"><i class="fa fa-edit"></i> Editar</a>
                                            <a class="btn btn-warning btn-xs" href="{{ url('processos/financas/'.$processo->cd_processo_pro) }}"><i class="fa fa-money"></i> Finanças</a>
                                            <button data-url="processos/" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i> Excluir</button>
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