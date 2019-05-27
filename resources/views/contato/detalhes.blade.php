@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('contatos') }}">Agenda de Contatos</a></li>
        <li>Detalhes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-book"></i> Contatos <span>> Detalhes </span> <span>> {{ $contato->nm_contato_cot }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a href="{{ url('contatos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Contatos</a>
            <a href="{{ url('contato/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
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
                        <h2>Dados do contato </h2>             
                    </header>
                
                    <div class="col-sm-12">
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-group fa-fw"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                                
                                                @if(!empty($contato->entidadeCliente->cliente))
                                                    <li>
                                                        <strong>Cliente: </strong> {{ $contato->entidadeCliente->cliente->nm_razao_social_cli }}
                                                    </li>
                                                @endif
                                                <li>
                                                    <strong>Nome: </strong> {{ $contato->nm_contato_cot }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Contato: </strong> {{ $contato->tipoContato->nm_tipo_contato_tct }}
                                                </li>                                     
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-phone fa-fw"></i> <strong>Telefones</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($contato->entidade->fone()->get()) > 0)
                                            @foreach($contato->entidade->fone()->get() as $fone)
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
                                        @if(count($contato->entidade->enderecoEletronico()->get()) > 0)
                                            @foreach($contato->entidade->enderecoEletronico()->get() as $email)
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
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-map-marker fa-fw"></i> <strong>Endereço</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if($contato->entidade->endereco()->first() and !is_null($contato->entidade->endereco()->first()->dc_logradouro_ede))
                                            
                                            <p>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>CEP: </strong> {{ ($contato->entidade->endereco) ? $contato->entidade->endereco->nu_cep_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Logradouro: </strong> {{ ($contato->entidade->endereco) ? $contato->entidade->endereco->dc_logradouro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Número: </strong> {{ ($contato->entidade->endereco) ? $contato->entidade->endereco->nu_numero_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Complemento: </strong> {{ ($contato->entidade->endereco) ? $contato->entidade->endereco->dc_complemento_ede : ''}}
                                                    </li>
                                                    <li>
                                                        <strong>Bairro: </strong> {{ ($contato->entidade->endereco) ? $contato->entidade->endereco->nm_bairro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Cidade/Estado: </strong> {{ ($contato->entidade->endereco and $contato->entidade->endereco->cidade) ? $contato->entidade->endereco->cidade->nm_cidade_cde .'/'. $contato->entidade->endereco->cidade->estado->nm_estado_est : '' }}
                                                    </li>
                                                </ul>
                                            </p> 
                                        @else
                                            <span>Endereço não informado</span>
                                        @endif
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <fieldset style="margin-bottom: 15px;">
                                        <legend><i class="fa fa-fw"></i> <strong></strong></legend>
                                        <div class="row" style="margin-left: 5px;">
                                            <p>    
                                                <ul class="list-unstyled">
                                                    <li style="display: inline-block;max-width: 100%;word-break:break-all;">
                                                        <strong>Observações: </strong> {!! $contato->dc_observacao_cot !!} 
                                                    </li>
                                                </ul>
                                            </p> 
                                        </div>
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