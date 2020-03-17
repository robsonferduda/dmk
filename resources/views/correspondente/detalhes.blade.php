@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span>> Detalhes </span> <span>> {{ $correspondente->nm_conta_correspondente_ccr }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondentes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Correspondentes</a>
            <a data-toggle="modal" href="{{ url('correspondente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>   
            <a data-toggle="modal" href="{{ url('correspondente/despesas/'.$correspondente->cd_correspondente_cor) }}" class="btn btn-info pull-right header-btn"><i class="fa fa-dollar fa-lg"></i> Despesas</a>   
            <a data-toggle="modal" href="{{ url('correspondente/honorarios/'.\Crypt::encrypt($correspondente->cd_correspondente_cor) }}" class="btn btn-warning pull-right header-btn"><i class="fa fa-money fa-lg"></i> Honorários</a> 
            <a data-toggle="modal" href="{{ url('correspondente/ficha/'.$correspondente->correspondente->cd_conta_con) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar Dados</a> 
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
                        <h2>Dados do Correspondente </h2>             
                    </header>
                
                    <div class="col-sm-12">

                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-group fa-fw"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Razão Social: </strong> {{ $correspondente->nm_conta_correspondente_ccr }}
                                                </li>
                                                <li>
                                                    <strong>Tipo: </strong> {{ ($correspondente->tipoPessoa()->first()) ? $correspondente->tipoPessoa()->first()->nm_tipo_pessoa_tpp : 'Não informado' }}
                                                </li>
                                                @if($correspondente->entidade->cpf()->first())
                                                    <li>
                                                        <strong>CPF: </strong> {{ ($correspondente->entidade->cpf()->first()) ? $correspondente->entidade->cpf()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                @elseif($correspondente->entidade->cnpj()->first())
                                                    <li>
                                                        <strong>CNPJ: </strong> {{ ($correspondente->entidade->cnpj()->first()) ? $correspondente->entidade->cnpj()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                @endif  
                                                @if($correspondente->entidade->oab()->first())
                                                     <li>
                                                        <strong>OAB: </strong> {{ ($correspondente->entidade->oab()->first()) ? $correspondente->entidade->oab()->first()->nu_identificacao_ide : 'Não informado' }}
                                                    </li>
                                                @endif 
                                                <li>
                                                    <strong>Comarca de Origem</strong>: {{ ($correspondente->entidade->atuacao()->where('fl_origem_cat','S')->first()) ?  $correspondente->entidade->atuacao()->where('fl_origem_cat','S')->first()->cidade()->first()->nm_cidade_cde : 'Não informado' }}
                                                </li>  
                                                <li>
                                                    <strong>Categoria: </strong> 
                                                    @if($correspondente->categoria)
                                                    <span class="label label-primary" style="background-color: {{ $correspondente->categoria->color_cac }}">{{ $correspondente->categoria->dc_categoria_correspondente_cac }}</span>
                                                    @else
                                                        Não informado
                                                    @endif
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
                                    <legend><i class="fa fa-dollar fa-fw"></i> <strong>Despesas Reembolsáveis</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($correspondente->correspondente->entidade->reembolso()->get()) > 0)
                                            @foreach($correspondente->correspondente->entidade->reembolso()->get() as $despesa)
                                                <div><span>{{ $despesa->tipoDespesa->nm_tipo_despesa_tds }}</span></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhuma despesa infomada</span>
                                        @endif 
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-bank"></i> <strong>Dados Bancários</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>    
                                            @if(count($correspondente->entidade->banco()->get()) > 0)
                                                @foreach($correspondente->entidade->banco()->get() as $banco)
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>Titular: </strong> {{ !empty($banco->nm_titular_dba) ? $banco->nm_titular_dba: ' ' }}
                                                    </li>
                                                    <li>
                                                        <strong>CPF: </strong> {{ !empty($banco->nu_cpf_cnpj_dba) ? $banco->nu_cpf_cnpj_dba: ' ' }}
                                                    </li>
                                                    <li>
                                                        <strong>Banco: </strong> {{ !empty($banco->banco->nm_banco_ban) ? $banco->banco->nm_banco_ban : ' ' }}
                                                    </li>
                                                    <li>
                                                        <strong>Tipo de Conta </strong> {{ !empty($banco->tipoConta->nm_tipo_conta_tcb) ? $banco->tipoConta->nm_tipo_conta_tcb : ' ' }}
                                                    </li>
                                                    <li>
                                                        <strong>Agência: </strong> {{ !empty($banco->nu_agencia_dba) ? $banco->nu_agencia_dba : ' ' }}
                                                    </li>
                                                    <li>
                                                        <strong>Conta: </strong> {{ !empty($banco->nu_conta_dba) ? $banco->nu_conta_dba : ' ' }}
                                                    </li>
                                                </ul>
                                                @endforeach   
                                            @else
                                                <span>Nenhuma conta infomada</span>
                                            @endif
                                        </p>
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
                                        @if($correspondente->entidade->endereco()->first() and !is_null($correspondente->entidade->endereco()->first()->dc_logradouro_ede))
                                            <p>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>CEP: </strong> {{ ($correspondente->entidade->endereco()->first()) ? $correspondente->entidade->endereco()->first()->nu_cep_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Logradouro: </strong> {{ ($correspondente->entidade->endereco()->first()) ? $correspondente->entidade->endereco()->first()->dc_logradouro_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Número: </strong> {{ ($correspondente->entidade->endereco()->first()) ? $correspondente->entidade->endereco()->first()->nu_numero_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Complemento: </strong> {{ ($correspondente->entidade->endereco()->first()->dc_complemento_ede) ? $correspondente->entidade->endereco()->first()->dc_complemento_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Bairro: </strong> {{ ($correspondente->entidade->endereco()->first()->nm_bairro_ede) ? $correspondente->entidade->endereco()->first()->nm_bairro_ede : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Cidade/Estado: </strong> {{ ($correspondente->entidade->endereco and $correspondente->entidade->endereco->cidade()->first()) ? $correspondente->entidade->endereco()->first()->cidade->nm_cidade_cde .'/'. $correspondente->entidade->endereco()->first()->cidade->estado->nm_estado_est : 'Não informado' }}
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
                                        @if(count($correspondente->entidade->fone()->get()) > 0)
                                            @foreach($correspondente->entidade->fone()->get() as $fone)
                                                <div><span>{{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo->dc_tipo_fone_tfo }}</span><br/></div>
                                            @endforeach   
                                        @else
                                            <span>Nenhum telefone informado</span>
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
                                        @if(count($correspondente->entidade->enderecoEletronico()->get()) > 0)
                                            @foreach($correspondente->entidade->enderecoEletronico()->get() as $email)
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
                                    <legend><i class="fa fa-pencil"></i> <strong>Observações</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        {!! $correspondente->obs_ccr !!}
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-map-marker"></i> <strong>Cidades de Atuação</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        @if(count($correspondente->entidade->atuacao()->get()) > 0)
                                            @foreach($correspondente->entidade->atuacao()->get() as $atuacao) 
                                                <button type="button" class="btn btn-default btn-atuacao" style="padding: 3px 8px;" data-id="{{ $atuacao->cd_cidade_atuacao_cat }}">{{ $atuacao->cidade()->first()->nm_cidade_cde }}</button>
                                            @endforeach
                                        @else
                                            <span class="text-warning erro-atuacao-vazia"><i class="fa fa-warning"></i> Nenhuma cidade de atuação informada</span>
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