@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Editar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Editar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>      
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
                    <h2>Cadastro de Cliente </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-add-cliente', 'url' => ['clientes', $cliente->cd_cliente_cli], 'class' => 'smart-form','method' => 'PUT']) !!}
                        <input type="hidden" name="telefones[]" id="telefones">
                        <input type="hidden" name="emails[]" id="emails">
                        <div class="row">
                            <section class="col col-6">
                                <header>
                                    Dados Básicos
                                </header>

                                <fieldset>
                                    <section>
                                        <div class="inline-group">
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="2" {{ ($cliente->cd_tipo_pessoa_tpp == 2) ? 'checked="checked"' : '' }}>
                                                <i></i>Pessoa Jurídica</label>
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="1" {{ ($cliente->cd_tipo_pessoa_tpp == 1) ? 'checked="checked"' : '' }}>
                                                <i></i>Pessoa Física</label>
                                        </div>
                                    </section>
                                    <div class="row">
                                         <section class="col col-3 box-pessoa-juridica">
                                            <label class="label">CNPJ</label>
                                            <label class="input">
                                                <input type="text" name="cnpj" id="cnpj" class="cnpj" placeholder="00.000.000/000-00" value="{{ ($cliente->entidade->cnpj) ? $cliente->entidade->cnpj->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-3 box-pessoa-juridica">
                                            <label class="label">Data de Fundação</label>
                                            <label class="input">
                                                <input type="text" name="data_fundacao_cli" class="data_fundacao" placeholder="__/__/____">
                                            </label>
                                        </section>

                                        <section class="col col-3 box-pessoa-fisica">
                                            <label class="label">CPF</label>
                                            <label class="input">
                                                <input type="text" name="cpf" id="cpf" class="cpf" placeholder="000.000.000-000" value="{{ ($cliente->entidade->cpf) ? $cliente->entidade->cpf->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-3 box-pessoa-fisica">
                                            <label class="label">Data de Nascimento</label>
                                            <label class="input">
                                                <input type="text" name="data_nascimento_cli" class="data_nascimento" placeholder="__/__/____">
                                            </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label label-tipo-pessoa">Nome Fantasia <span class="text-danger">(Obrigatório)</span></label>
                                            <label class="input">
                                                <input type="text" name="nm_fantasia_cli" value="{{ old('nm_fantasia_cli') ? old('nm_fantasia_cli') : $cliente->nm_fantasia_cli }}" placeholder="Nome Fantasia">
                                            </label>
                                        </section>
                                    </div>                        
                                    
                                    <div class="row">
                                        <section class="col col-6">
                                            <label class="label">Razão Social</label>
                                            <label class="input">
                                                <input type="text" name="nm_razao_social_cli" value="{{ old('nm_razao_social_cli') ? old('nm_razao_social_cli') : $cliente->nm_razao_social_cli }}" placeholder="Razão Social">
                                            </label>
                                        </section>
                                        <section class="col col-3">
                                            <label class="label">Inscrição Municipal</label>
                                            <label class="input">
                                                <input type="text" name="inscricao_municipal_cli" placeholder="Inscrição Municipal">
                                            </label>
                                        </section>
                                        <section class="col col-3">
                                            <label class="label">Inscrição Estadual</label>
                                            <label class="input">
                                                <input type="text" name="inscricao_estadual_cli" placeholder="Inscrição Estadual">
                                            </label>
                                        </section>
                                    </div>  

                                    <section>
                                        <div class="onoffswitch-container">
                                            <span class="onoffswitch-title">Pagamento Com Nota Fiscal</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" name="fl_nota_fiscal_cli" id="fl_nota_fiscal_cli" value="S" {{ ($cliente->fl_nota_fiscal_cli == "S") ? 'checked="checked"' : "" }}>
                                                <label class="onoffswitch-label" for="fl_nota_fiscal_cli"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>
                                    </section>

                                </fieldset>
                            </section>

                            <section class="col col-6">
                                <header>
                                    <i class="fa fa-map-marker"></i> Endereço 
                                </header>

                                <fieldset>

                                    <div class="row">
                                        <section class="col col-2">
                                            <label class="label">CEP</label>
                                            <label class="input">
                                                <input type="text" name="nu_cep_ede" placeholder="CEP" value="{{old('nu_cep_ede')}}">
                                            </label>
                                        </section> 
                                        <section class="col col-sm-8">
                                            <label class="label">Logradouro</label>
                                            <label class="input">
                                                <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{old('dc_logradouro_ede')}}">
                                            </label>
                                        </section>
                                        <section class="col col-2">
                                            <label class="label">Nº</label>
                                            <label class="input">
                                                <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{old('nu_numero_ede')}}">
                                            </label>
                                        </section>
                                    </div>

                                     <div class="row">
                                        <section class="col col-6">
                                            <label class="label">Bairro</label>
                                            <label class="input">
                                                <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{old('nm_bairro_ede')}}">
                                            </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label">Complemento</label>
                                            <label class="input">
                                                <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('dc_complemento_ede')}}">
                                            </label>
                                        </section>                                                                    
                                    </div> 

                                    <div class="row">                    
                                        <section class="col col-6">                                       
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione</option>
                                                @foreach(App\Estado::all() as $estado) 
                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach

                                            </select> 
                                        </section>
                                        <section class="col col-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                           <label class="label" >Cidade</label>          
                                            <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                               <option selected value="">Selecione o Estado</option>
                                            </select> 
                                        </section>  
                                    </div>
                                </fieldset>
                            </section>
                        </div>

                            <div class="row">
                                <section class="col col-6">
                                    <header>
                                            <i class="fa fa-phone"></i> Telefones
                                            <a style="padding: 1px 8px;" data-toggle="modal" data-target="#modalFone"><i class="fa fa-plus-circle"></i> Novo
                                            </a>
                                    </header>
                                    <fieldset>
                                            @if(false)
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:25%">Tipo</th>
                                                            <th style="width:50%">Telefone</th>
                                                            <th style="width:25%">Opções</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($conta->fone()->get() as $fone)
                                                            <tr>
                                                                <td>{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                                <td>{{ $fone->nu_fone_fon }}</td>
                                                                <td>
                                                                    <a><i class="fa fa-edit"></i> Editar</a>
                                                                    <a><i class="fa fa-trash"></i> Excluir</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span>Nenhum telefone cadastrado</span>
                                            @endif                                 
                                    </fieldset>
                                </section>
                                <section class="col col-6">
                                    <header>
                                            <i class="fa fa-envelope"></i> Emails
                                            <a style="padding: 1px 8px;" data-toggle="modal" data-target="#modalFone"><i class="fa fa-plus-circle"></i> Novo
                                            </a>
                                    </header>
                                    <fieldset>
                                            @if(false)
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:25%">Tipo</th>
                                                            <th style="width:50%">Telefone</th>
                                                            <th style="width:25%">Opções</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($conta->fone()->get() as $fone)
                                                            <tr>
                                                                <td>{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                                <td>{{ $fone->nu_fone_fon }}</td>
                                                                <td>
                                                                    <a><i class="fa fa-edit"></i> Editar</a>
                                                                    <a><i class="fa fa-trash"></i> Excluir</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span>Nenhum email cadastrado</span>
                                            @endif                                 
                                    </fieldset>
                                </section>
                            </div>

                            <header>
                                <i class="fa fa-dollar"></i> Despesas por Tipo de Serviço 
                                <a href="{{ url('configuracoes/tipos-de-despesa') }}" style="padding: 1px 8px;" ><i class="fa fa-plus-circle"></i> Novo </a>
                            </header>

                            <fieldset>
                                <section>
                                    <label class="label">Selecione as despesas relacionadas ao cliente</label>
                                    <div class="row">
                                        <div class="col col-12">
                                            @foreach(\App\TipoDespesa::all() as $despesa)
                                                <label class="checkbox">
                                                    <input type="checkbox" name="despesas[]" value="{{ $despesa->cd_tipo_despesa_tds }}">
                                                    <i></i>{{ $despesa->nm_tipo_despesa_tds }} 
                                                    {{ ($despesa->fl_reembolso_tds == 'S') ? '(Reembolsável)' : '(Não Reembolsável)' }}
                                                </label>
                                            @endforeach
                                        </div> 
                                    </div>
                                </section>
                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                <a href="{{ url('clientes') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </footer>
                        {!! Form::close() !!}                   
                    </div>
                </div>
            </div>
            </article>
        </div>
    </div>
</div>
@endsection