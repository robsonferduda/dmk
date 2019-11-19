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
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Detalhes </span> <span>> {{ $usuario->name }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            @can('usuario.index')
                <a data-toggle="modal" href="{{ url('usuarios') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Usuários</a>
                <a data-toggle="modal" href="{{ url('usuarios/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
                <a data-toggle="modal" href="{{ url('usuarios/editar/'.\Crypt::encrypt($usuario->id)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
            @endcan
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
                        <h2>Dados do Usuário </h2>             
                    </header>
                
                    <div class="col-sm-12">

                        <div class="col-md-6">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-group fa-fw"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Nome: </strong> {{ $usuario->name }}
                                                </li>
                                                <li>
                                                    <strong>Perfil: </strong> {{ !empty($usuario->role()->first()) ? $usuario->role()->first()->name : 'Não definido' }}
                                                </li>
                                                <li>
                                                    <strong>Data Nascimento: </strong> {{ !empty($usuario->data_nascimento) ? date('d/m/Y', strtotime($usuario->data_nascimento)) : 'Não informado' }}
                                                </li>
                                                <hr style="margin-top: 5px; margin-bottom: 5px;" />
                                                <li>
                                                    <strong>CPF: </strong> {{ !empty($usuario->entidade->cpf->nu_identificacao_ide) ? $usuario->entidade->cpf->nu_identificacao_ide : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>N º OAB: </strong> {{ !empty($usuario->entidade->oab->nu_identificacao_ide) ? $usuario->entidade->oab->nu_identificacao_ide : 'Não informado' }}
                                                </li>                                                
                                                <li>
                                                    <strong>RG: </strong> {{ !empty($usuario->entidade->rg->nu_identificacao_ide) ? $usuario->entidade->rg->nu_identificacao_ide : 'Não informado' }}
                                                </li>
                                                <hr style="margin-top: 5px; margin-bottom: 5px;" />
                                                <li>
                                                    <strong>Data de Admissão: </strong> {{ !empty($usuario->data_admissao) ? date('d/m/Y', strtotime($usuario->data_admissao)) : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Estado Civil: </strong> {{ !empty($usuario->estadoCivil->nm_estado_civil_esc) ? $usuario->estadoCivil->nm_estado_civil_esc : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Departamento: </strong> {{ !empty($usuario->departamento->nm_departamento_dep) ? $usuario->departamento->nm_departamento_dep : 'Não informado' }}
                                                </li>                                                
                                                <li>
                                                    <strong>Cargo: </strong> {{ !empty($usuario->cargo->nm_cargo_car) ? $usuario->cargo->nm_cargo_car : 'Não informado' }}
                                                </li>   
                                                <li>
                                                    <strong>Email: </strong> {{ $usuario->email }}
                                                </li>                                                                                            
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <fieldset style="margin-bottom: 15px;">
                                <legend><i class="fa fa-building"></i> <strong>Endereço</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>    
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>CEP: </strong> {{ !empty($usuario->entidade->endereco->nu_cep_ede) ? $usuario->entidade->endereco->nu_cep_ede : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Logradouro: </strong> {{ !empty($usuario->entidade->endereco->dc_logradouro_ede) ? $usuario->entidade->endereco->dc_logradouro_ede : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Nº: </strong> {{ !empty($usuario->entidade->endereco->nu_numero_ede) ? $usuario->entidade->endereco->nu_numero_ede : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Complemento: </strong> {{ !empty($usuario->entidade->endereco->dc_complemento_ede) ? $usuario->entidade->endereco->dc_complemento_ede : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Bairro: </strong> {{ !empty($usuario->entidade->endereco->nm_bairro_ede) ? $usuario->entidade->endereco->nm_bairro_ede : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($usuario->entidade->endereco->cidade->nm_cidade_cde) ? $usuario->entidade->endereco->cidade->nm_cidade_cde : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Estado: </strong> {{ !empty($usuario->entidade->endereco->cidade->estado->nm_estado_est) ? $usuario->entidade->endereco->cidade->estado->nm_estado_est : 'Não informado' }}
                                                </li>                                              
                                            </ul>
                                        </p> 
                                    </div>
                            </fieldset>
                         </div>
                         
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <fieldset style="margin-bottom: 15px;">
                                        <legend><i class="fa fa-phone fa-fw"></i> <strong>Telefones</strong></legend>
                                        <div class="row" style="margin-left: 5px;">
                                            @if(count($usuario->entidade->fone()->get()) > 0)
                                                @foreach($usuario->entidade->fone()->get() as $fone)
                                                    <div><span>{{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo->dc_tipo_fone_tfo }}</span><br/></div>
                                                @endforeach   
                                            @else
                                                <span>Nenhum telefone infomado</span>
                                            @endif
                                        </div>
                                    </fieldset>
                                </div>           
                            </div> 
                            <div class="col-md-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-bank"></i> <strong>Dados Bancários</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>    
                                            @if(count($usuario->entidade->banco()->get()) > 0)
                                                @foreach($usuario->entidade->banco()->get() as $banco)
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>Titular: </strong> {{ !empty($banco->nm_titular_dba) ? $banco->nm_titular_dba: 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>CPF: </strong> {{ !empty($banco->nu_cpf_cnpj_dba) ? $banco->nu_cpf_cnpj_dba: 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Banco: </strong> {{ !empty($banco->banco->nm_banco_ban) ? $banco->banco->nm_banco_ban : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Tipo de Conta </strong> {{ !empty($banco->tipoConta->nm_tipo_conta_tcb) ? $banco->tipoConta->nm_tipo_conta_tcb : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Agência: </strong> {{ !empty($banco->nu_agencia_dba) ? $banco->nu_agencia_dba : 'Não informado' }}
                                                    </li>
                                                    <li>
                                                        <strong>Conta: </strong> {{ !empty($banco->nu_conta_dba) ? $banco->nu_conta_dba : 'Não informado' }}
                                                    </li>
                                                </ul>
                                                @endforeach   
                                            @else
                                                <span>Nenhum dados bancário infomado</span>
                                            @endif
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-fw"></i> <strong></strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>    
                                            <ul class="list-unstyled">
                                                <li style="display: inline-block;max-width: 100%;word-break:break-all;">
                                                    <strong>Observações </strong><br/> 
                                                    {!! ($usuario->observacao) ? $usuario->observacao : 'Nenhuma obervação cadastrada' !!} 
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