@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Perfil</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Perfil</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <div class="col-md-12 col-lg-12">
         <div class="well profile">
            <div class="col-md-12">
                <div class="col-md-3 text-center">
                    <div>
                        @if(file_exists('img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png')) 
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @else
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @endif
                    </div>
                    <h4 class="center" style="margin-top: 15px;"><strong>{{ $correspondente->nm_razao_social_con }}</strong></h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-6">
                        <h2>{{ $correspondente->nm_razao_social_con }} <a href="{{ url('correspondente/ficha/'.$correspondente->cd_conta_con) }}"><span class="fa fa-edit"></span></a></h2>
                        <p>
                            <ul class="list-unstyled">
                                <li>
                                    <span class="label label-primary">{!! ($correspondente->tipoPessoa()->first()) ? $correspondente->tipoPessoa()->first()->nm_tipo_pessoa_tpp : '' !!}</span>
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:$usuario->email">{{ $correspondente->entidade->usuario->email }}</a>
                                    </p>
                                </li>                                
                                @if($correspondente->entidade->cpf()->first()))
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CPF </strong>: {{ ($correspondente->entidade->cpf()->first()) ? $correspondente->entidade->cpf()->first()->nu_identificacao_ide : 'Não informado' }} </span>
                                        <p>
                                    </li>
                                @elseif($correspondente->entidade->cnpj()->first()))
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CNPJ </strong>: {{ ($correspondente->entidade->cnpj()->first()) ? $correspondente->entidade->cnpj()->first()->nu_identificacao_ide : 'Não informado' }} </span>
                                        </p>
                                    </li>
                                @else
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CPF/CNPJ </strong>: {{ 'Não informado' }}</span>
                                        </p>
                                    </li>
                                @endif     
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>OAB</strong>: {{ ($correspondente->entidade->oab) ? $correspondente->entidade->oab->nu_identificacao_ide : 'Não informado' }}</span>
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
                            @if($correspondente->entidade->endereco)
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
                            @else
                                <span>Não informado</span>
                            @endif
                        </p>

                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-envelope"></i> Emails
                        </h4>
                        <p>
                            @if(count($correspondente->entidade->enderecoEletronico()->get()) > 0)
                                @foreach($correspondente->entidade->enderecoEletronico()->get() as $email)
                                    @if($email->tipo->cd_tipo_endereco_eletronico_tee == 1)
                                        <div><span><i class="fa fa-user"></i> {{ $email->dc_endereco_eletronico_ede }}</span> - <span>{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</span><br/></div>
                                    @else
                                        <div><span><i class="fa fa-bell"></i> {{ $email->dc_endereco_eletronico_ede }}</span> - <span>{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</span><br/></div>
                                    @endif
                                @endforeach   
                            @else
                                <span>Nenhum email infomado</span>
                            @endif 
                        </p>

                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-bank"></i> Dados Bancários 
                        </h4>
                        <p>
                            @if($correspondente->entidade->banco)
                                <ul class="list-unstyled">
                                    <li>
                                        <strong>Banco: </strong> {{ $correspondente->entidade->banco->banco->nm_banco_ban }}
                                    </li>
                                    <li>
                                        <strong>Agência: </strong> {{ $correspondente->entidade->banco->nu_agencia_dba }}
                                    </li>
                                    <li>
                                        <strong>Conta: </strong> {{ $correspondente->entidade->banco->nu_conta_dba }}
                                    </li>
                                    <li>
                                        <strong>Tpo de Conta: </strong> {{ $correspondente->entidade->banco->tipoConta->nm_tipo_conta_tcb }}
                                    </li>
                                </ul>
                            @else
                                <span>Não informados</span>
                            @endif
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