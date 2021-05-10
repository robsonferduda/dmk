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
                <i class="fa-fw fa fa-user"></i> Meu Perfil</span>
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
            <div class="col-md-12 col-lg-12">
             <div class="well profile">
                <div class="col-md-12">
                    <div class="col-md-3 text-center">
                        <div>
                            @if(file_exists('public/img/users/ent'.$usuario->entidade->cd_entidade_ete.'.png')) 
                                <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.$usuario->entidade->cd_entidade_ete.'.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                            @else
                                <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                            @endif
                        </div>
                        <h4 class="center" style="margin-top: 15px;"><strong>{{ $usuario->name }}</strong></h4>
                        <h6><a href="#" class="alterar_senha" data-id="{{ \Crypt::encrypt(Auth::user()->id) }}"><i class="fa fa-lock"></i> Alterar Senha</a></h6>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-6">
                            <h2 style="margin-bottom: 5px;">{{ $usuario->name  }} <a href="{{ url('usuarios/editar/'.\Crypt::encrypt($usuario->id)) }}"><span class="fa fa-edit"></span></a></h2>
                                <ul class="list-unstyled">
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:$usuario->email">{{ ($usuario->email) ? $usuario->email : 'Não informado' }}</a>
                                        </p>
                                    </li>                                
                                    @if($usuario->entidade->cpf)
                                        <li>
                                            <p class="text-muted">
                                                <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CPF </strong>: {{ !empty($usuario->entidade->cpf->nu_identificacao_ide) ? $usuario->entidade->cpf->nu_identificacao_ide : 'Não informado' }} </span>
                                            <p>
                                        </li>
                                    @endif   
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>OAB</strong>:  {{ !empty($usuario->entidade->oab->nu_identificacao_ide) ? $usuario->entidade->oab->nu_identificacao_ide : 'Não informado' }}</span>
                                        </p>
                                    </li>
                                    <li>
                                        <strong>RG: </strong> {{ !empty($usuario->entidade->rg->nu_identificacao_ide) ? $usuario->entidade->rg->nu_identificacao_ide : 'Não informado' }}
                                    </li>
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
                                </ul>

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-phone"></i> Telefones
                            </h4>
                             <div class="row" style="margin-left: 5px;">
                                            @if(count($usuario->entidade->fone()->get()) > 0)
                                                @foreach($usuario->entidade->fone()->get() as $fone)
                                                    <div><span>{{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo->dc_tipo_fone_tfo }}</span><br/></div>
                                                @endforeach   
                                            @else
                                                <span>Nenhum telefone infomado</span>
                                            @endif
                                        </div>

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-envelope"></i> Emails Alternativos
                            </h4>
                            <div class="row" style="margin-left: 5px;">
                                            @if(count($usuario->entidade->fone()->get()) > 0)
                                                @foreach($usuario->entidade->fone()->get() as $fone)
                                                    <div><span>{{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo->dc_tipo_fone_tfo }}</span><br/></div>
                                                @endforeach   
                                            @else
                                                <span>Nenhum email alternativo infomado</span>
                                            @endif
                                        </div>
                        </div>
                        <div class="col-md-6">
                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-map-marker"></i> Endereço 
                            </h4>
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

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-bank"></i> Dados Bancários 
                            </h4>
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
                            </div>             
                        </div>   
                    <div style="clear: both;"></div>    
                </div>                 
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="alterarSenha" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Alterar Senha
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-alterar-senha', 'method' => 'PUT', 'url' => 'usuarios/alterar-senha', 'class' => 'smart-form']) !!}
                     <fieldset>
                       <section class="col col-6">
                            <input type="hidden" name="fl_conta" value="N">
                            <label class="label">Senha<span class="text-danger">*</span></label>
                            <label class="input">
                                 <input type="password" name="password" id="password" placeholder="Senha" required>
                            </label>
                            </section>  
                            <section class="col col-6">
                                <label class="label">Confirmar Senha<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Senha" required>
                                </label>
                            </section>     
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-alterar-senha"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection