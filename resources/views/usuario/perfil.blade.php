@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-user"></i> Conta 
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
         <div class="well profile">
            <div class="col-md-12">
                <div class="col-md-3 text-center">
                    <div>
                        <img src="{{ asset('img/users/user.png') }}" alt="" style="margin: 0 auto;" class="img-circle img-responsive">
                    </div>
                    <h4 class="center" style="margin-top: 15px;"><strong>{{ $usuario->name }}</strong></h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-6">
                        <h2>{{ $usuario->name }} <a href="{{ url('usuarios/editar/'.$usuario->id) }}"><span class="fa fa-edit"></span></a></h2>
                        <p><strong>Perfil: </strong> {{ $usuario->tipoPerfil()->first()->dc_nivel_niv }} </p>
                        <p><strong>Estado Civil: </strong> {{ $usuario->estadoCivil()->first()->nm_estado_civil_esc }} </p>
                        <p>
                            <ul class="list-unstyled">
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-phone"></i>&nbsp;&nbsp;<span class="txt-color-darken">{{ $usuario->entidade()->first()->fone()->first()->nu_fone_fon }}</span>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:$usuario->email">{{ $usuario->email }}</a>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-calendar"></i>&nbsp;&nbsp;<span class="txt-color-darken">Data de Nascimento: {{ date('d/m/Y', strtotime($usuario->data_nascimento)) }}</span>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-calendar"></i>&nbsp;&nbsp;<span class="txt-color-darken">Data de Admissão: {{ date('d/m/Y', strtotime($usuario->data_admissao)) }}</span>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken">CPF: {{ $usuario->entidade->cpf->nu_identificacao_ide }}</span>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken">OAB: {{ $usuario->entidade->oab->nu_identificacao_ide }}</span>
                                    </p>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken">RG: {{ $usuario->entidade->rg->nu_identificacao_ide }}</span>
                                    </p>
                                </li>
                            </ul>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-map-marker"></i> Endereço 
                        </h4>
                        <p>
                            <ul class="list-unstyled">
                                <li>
                                    <strong>CEP: </strong> {{ $usuario->entidade->endereco->nu_cep_ede }}
                                </li>
                                <li>
                                    <strong>Logradouro: </strong> {{ $usuario->entidade->endereco->dc_logradouro_ede }}
                                </li>
                                <li>
                                    <strong>Número: </strong> {{ $usuario->entidade->endereco->nu_numero_ede }}
                                </li>
                                <li>
                                    <strong>Complemento: </strong> {{ $usuario->entidade->endereco->dc_complemento_ede }}
                                </li>
                                <li>
                                    <strong>Bairro: </strong> {{ $usuario->entidade->endereco->nm_bairro_ede }}
                                </li>
                                <li>
                                    <strong>Cidade/Estado: </strong> {{ $usuario->entidade->endereco->cidade->nm_cidade_cde }}/{{ $usuario->entidade->endereco->cidade->estado->nm_estado_est }}
                                </li>
                            </ul>
                        </p>

                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-bank"></i> Dados Bancários 
                        </h4>
                        <p>
                            <ul class="list-unstyled">
                                <li>
                                    <strong>Banco: </strong> {{ $usuario->entidade->banco->banco->nm_banco_ban }}
                                </li>
                                <li>
                                    <strong>Agência: </strong> {{ $usuario->entidade->banco->nu_agencia_dba }}
                                </li>
                                <li>
                                    <strong>Conta: </strong> {{ $usuario->entidade->banco->nu_conta_dba }}
                                </li>
                                <li>
                                    <strong>Tpo de Conta: </strong> {{ $usuario->entidade->banco->tipoConta->nm_tipo_conta_tcb }}
                                </li>
                            </ul>
                        </p>
                    </div>
                    <br>
                </div>             
            </div>            
         </div>                 
        </div>
    </div>
</div>
@endsection