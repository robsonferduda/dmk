@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Detalhes </span> <span>> {{ $cliente->nm_fantasia_cli }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('cliente/honorarios/'.$cliente->cd_cliente_cli) }}" class="btn btn-warning pull-right header-btn btnMargin"><i class="fa fa-money fa-lg"></i> Honorários</a> 
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" target="blank" href="{{ url('cliente/contatos/'.$cliente->cd_entidade_ete) }}" class="btn btn-default pull-right header-btn"><i class="fa fa-book fa-lg"></i>Contatos</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Dados do Cliente </h2>             
                    </header>
                
                    <div class="col-sm-12">

                        <div class="col-md-8">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-group fa-fw"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Razão Social: </strong> {{ $cliente->nm_razao_social_cli }}
                                                </li>
                                                <li>
                                                    <strong>Nome Fantasia: </strong> {{ $cliente->nm_fantasia_cli }}
                                                </li>
                                                <li>
                                                    <strong>Tipo: </strong> {{ $cliente->tipoPessoa()->first()->nm_tipo_pessoa_tpp }}
                                                </li>
                                                @if($cliente->entidade->cpf()->first())
                                                    <li>
                                                        <strong>CPF: </strong> {{ ($cliente->entidade->cpf()->first()) ? $cliente->entidade->cpf()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Data de Nascimento: </strong> {{ ($cliente->dt_inicial_cli) ? date('d/m/Y', strtotime($cliente->dt_inicial_cli)) : 'Não informada' }}
                                                    </li>
                                                @else
                                                    <li>
                                                        <strong>CNPJ: </strong> {{ ($cliente->entidade->cnpj()->first()) ? $cliente->entidade->cnpj()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Data de Fundação: </strong> {{ ($cliente->dt_inicial_cli) ? date('d/m/Y', strtotime($cliente->dt_inicial_cli)) : 'Não informado' }}
                                                    </li>    
                                                @endif                                            
                                                <li>
                                                    <strong>Inscrição Municipal: </strong> {{ $cliente->inscricao_municipal_cli }}
                                                </li>
                                                <li>
                                                    <strong>Inscrição Estadual: </strong> {{ $cliente->inscricao_estadual_cli }}
                                                </li>
                                                <li>
                                                    <strong>Pagamento com Nota Fiscal: </strong> {!! ($cliente->fl_nota_fiscal_cli == "S") ? '<span class="label label-success">SIM</span>' : '<span class="label label-danger">NÃO</span>' !!}
                                                </li>
                                                @if($cliente->fl_nota_fiscal_cli == "S")
                                                    <li>
                                                        <strong>Percentual de Desconto: </strong> {{ $cliente->taxa_imposto_cli }}
                                                    </li>
                                                @endif
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-money fa-fw"></i> <strong>Despesas Reembolsáveis</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($cliente->entidade->reembolso()->get()) > 0)
                                            @foreach($cliente->entidade->reembolso()->get() as $despesa)
                                                <div><span>{{ $despesa->tipoDespesa->nm_tipo_despesa_tds }}</span></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhuma despesa infomada</span>
                                        @endif 
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-12">
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-map-marker fa-fw"></i> <strong>Endereço</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if($cliente->entidade->endereco()->first() and !is_null($cliente->entidade->endereco()->first()->dc_logradouro_ede))
                                        @php $endereco = $cliente->entidade->endereco()->first() @endphp
                                            <p>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>CEP: </strong> {{ ($endereco) ? $endereco->nu_cep_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Logradouro: </strong> {{ ($endereco) ? $endereco->dc_logradouro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Número: </strong> {{ ($endereco) ? $endereco->nu_numero_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Complemento: </strong> {{ ($endereco) ? $endereco->dc_complemento_ede : ''}}
                                                    </li>
                                                    <li>
                                                        <strong>Bairro: </strong> {{ ($endereco) ? $endereco->nm_bairro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Cidade/Estado: </strong> {{ ($endereco and $endereco->cidade()->first()) ? $endereco->cidade->nm_cidade_cde .'/'. $cliente->entidade->endereco()->first()->cidade->estado->nm_estado_est : '' }}
                                                    </li>
                                                </ul>
                                            </p> 
                                        @else
                                            <span>Endereço não informado</span>
                                        @endif
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-phone fa-fw"></i> <strong>Telefones</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($cliente->entidade->fone()->get()) > 0)
                                            @foreach($cliente->entidade->fone()->get() as $fone)
                                                <div><span>{{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo->dc_tipo_fone_tfo }}</span><br/></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhum telefone infomado</span>
                                        @endif
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-envelope fa-fw"></i> <strong>Emails</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($cliente->entidade->enderecoEletronico()->get()) > 0)
                                            @foreach($cliente->entidade->enderecoEletronico()->get() as $email)
                                                <div><span>{{ $email->dc_endereco_eletronico_ede }}</span> - <span>{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</span><br/></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhum email infomado</span>
                                        @endif 
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-file-text-o fa-fw"></i> <strong>Observação</strong></legend>
                                    {!! $cliente->observacao_cli !!}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection