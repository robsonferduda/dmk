@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-user"></i> Conta 
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('conta/atualizar/'.\Crypt::encrypt($conta->cd_conta_con)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
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
                        <h2>Dados do conta </h2>             
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
                                                    <strong>Razão Social: </strong> {{ $conta->nm_razao_social_con }}
                                                </li>
                                                <li>
                                                    <strong>Nome Fantasia: </strong> {{ $conta->nm_fantasia_con }}
                                                </li>
                                                <li>
                                                    <strong>Tipo: </strong> {{ ($conta->tipoPessoa()->first()) ? $conta->tipoPessoa()->first()->nm_tipo_pessoa_tpp : 'Não informado' }}
                                                </li>
                                                @if($conta->entidade->cpf()->first())
                                                    <li>
                                                        <strong>CPF: </strong> {{ ($conta->entidade->cpf()->first()) ? $conta->entidade->cpf()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                @elseif($conta->entidade->cnpj()->first())
                                                    <li>
                                                        <strong>CNPJ: </strong> {{ ($conta->entidade->cnpj()->first()) ? $conta->entidade->cnpj()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                @endif                                            
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <fieldset style="margin-bottom: 15px;">
                                <legend><i class="fa fa-bank"></i> <strong>Dados Bancários</strong></legend>
                                <div class="row" style="margin-left: 5px;">
                                    @if(!empty($conta->entidade->banco->banco->nm_banco_ban))
                                    <p>    
                                        <ul class="list-unstyled">
                                            <li>
                                                <strong>Banco: </strong> {{ !empty($conta->entidade->banco->banco->nm_banco_ban) ? $conta->entidade->banco->banco->nm_banco_ban : ' ' }}
                                            </li>
                                            <li>
                                                <strong>Tipo de Conta </strong> {{ !empty($conta->entidade->banco->tipoConta->nm_tipo_conta_tcb) ? $conta->entidade->banco->tipoConta->nm_tipo_conta_tcb : ' ' }}
                                            </li>
                                            <li>
                                                <strong>Agência: </strong> {{ !empty($conta->entidade->banco->nu_agencia_dba) ? $conta->entidade->banco->nu_agencia_dba : ' ' }}
                                            </li>
                                            <li>
                                                <strong>Conta: </strong> {{ !empty($conta->entidade->banco->nu_conta_dba) ? $conta->entidade->banco->nu_conta_dba : ' ' }}
                                            </li>
                                        </ul>
                                    </p> 
                                    @else
                                        <span>Nenhum dado bancário informado</span>
                                    @endif
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-md-12">
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-map-marker fa-fw"></i> <strong>Endereço</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if($conta->entidade->endereco()->first() and !is_null($conta->entidade->endereco()->first()->dc_logradouro_ede))
                                            <p>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>CEP: </strong> {{ ($conta->entidade->endereco()->first()) ? $conta->entidade->endereco()->first()->nu_cep_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Logradouro: </strong> {{ ($conta->entidade->endereco()->first()) ? $conta->entidade->endereco()->first()->dc_logradouro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Número: </strong> {{ ($conta->entidade->endereco()->first()) ? $conta->entidade->endereco()->first()->nu_numero_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Complemento: </strong> {{ ($conta->entidade->endereco()->first()) ? $conta->entidade->endereco()->first()->dc_complemento_ede : ''}}
                                                    </li>
                                                    <li>
                                                        <strong>Bairro: </strong> {{ ($conta->entidade->endereco()->first()) ? $conta->entidade->endereco()->first()->nm_bairro_ede : '' }}
                                                    </li>
                                                    <li>
                                                        <strong>Cidade/Estado: </strong> {{ ($conta->entidade->endereco and $conta->entidade->endereco->cidade()->first()) ? $conta->entidade->endereco()->first()->cidade->nm_cidade_cde .'/'. $conta->entidade->endereco()->first()->cidade->estado->nm_estado_est : '' }}
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
                                        @if(count($conta->entidade->fone()->get()) > 0)
                                            @foreach($conta->entidade->fone()->get() as $fone)
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
                                        @if(count($conta->entidade->enderecoEletronico()->get()) > 0)
                                            @foreach($conta->entidade->enderecoEletronico()->get() as $email)
                                                <div><span>{{ $email->dc_endereco_eletronico_ede }}</span> - <span>{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</span><br/></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhum email infomado</span>
                                        @endif 
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