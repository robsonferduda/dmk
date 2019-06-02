@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Minha Conta</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-user"></i> Minha Conta 
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
             
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
                            @if(file_exists('public/img/users/ent'.$conta->entidade->cd_entidade_ete.'.png')) 
                                <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.$conta->entidade->cd_entidade_ete.'.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                            @else
                                <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                            @endif
                        </div>
                        <h4 class="center" style="margin-top: 15px;"><strong>{{ $conta->nm_razao_social_con }}</strong></h4>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-6">
                            <h2 style="margin-bottom: 5px;">{{ $conta->nm_razao_social_con }} <a href="{{ url('conta/atualizar/'.\Crypt::encrypt($conta->cd_conta_con)) }}"><span class="fa fa-edit"></span></a></h2>
                                <ul class="list-unstyled">
                                    <li style="margin-bottom: 8px;">
                                        {!! ($conta->tipoPessoa) ? '<span class="label label-primary">Pessoa '.$conta->tipoPessoa->nm_tipo_pessoa_tpp.'</span>' : '' !!}
                                    </li>
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:$usuario->email">{{ ($conta->entidade->usuario) ? $conta->entidade->usuario->email : 'Não informado' }}</a>
                                        </p>
                                    </li>                                
                                    @if($conta->entidade->cpf)
                                        <li>
                                            <p class="text-muted">
                                                <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CPF </strong>: {{ ($conta->entidade->cpf) ? $conta->entidade->cpf->nu_identificacao_ide : 'Não informado' }} </span>
                                            <p>
                                        </li>
                                    @elseif($conta->entidade->cnpj)
                                        <li>
                                            <p class="text-muted">
                                                <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CNPJ </strong>: {{ ($conta->entidade->cnpj) ? $conta->entidade->cnpj->nu_identificacao_ide : 'Não informado' }} </span>
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
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>OAB</strong>: {{ ($conta->entidade->oab) ? $conta->entidade->oab->nu_identificacao_ide : 'Não informado' }}</span>
                                        </p>
                                    </li>
                                </ul>

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-phone"></i> Telefones
                            </h4>
                            <p>
                                @if(count($conta->entidade->fone()->get()) > 0)
                                    @foreach($conta->entidade->fone()->get() as $fone)
                                        <div><span><i class="fa fa-phone-square"></i> {{ $fone->nu_fone_fon }}</span> - <span>{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</span><br/></div>
                                    @endforeach   
                                @else
                                    <span>Nenhum telefone infomado</span>
                                @endif 
                            </p>

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-envelope"></i> Emails
                            </h4>
                            <p>
                                @if(count($conta->entidade->enderecoEletronico()->get()) > 0)
                                    @foreach($conta->entidade->enderecoEletronico()->get() as $email)
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
                        </div>
                        <div class="col-md-6">
                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-map-marker"></i> Endereço 
                            </h4>
                            <p>
                                @if($conta->entidade->endereco)
                                <ul class="list-unstyled">
                                    <li>
                                        <strong>CEP: </strong> {{ $conta->entidade->endereco->nu_cep_ede }}
                                    </li>
                                    <li>
                                        <strong>Logradouro: </strong> {{ $conta->entidade->endereco->dc_logradouro_ede }}
                                    </li>
                                    <li>
                                        <strong>Número: </strong> {{ $conta->entidade->endereco->nu_numero_ede }}
                                    </li>
                                    <li>
                                        <strong>Complemento: </strong> {{ $conta->entidade->endereco->dc_complemento_ede }}
                                    </li>
                                    <li>
                                        <strong>Bairro: </strong> {{ $conta->entidade->endereco->nm_bairro_ede }}
                                    </li>
                                    <li>
                                        <strong>Cidade/Estado: </strong> {{ ($conta->entidade->endereco->cidade) ? $conta->entidade->endereco->cidade->nm_cidade_cde.'/'.$conta->entidade->endereco->cidade->estado->nm_estado_est : 'Não informado' }}
                                    </li>
                                </ul>
                                @else
                                    <span>Não informado</span>
                                @endif
                            </p>

                            <h4 style="margin-top: 25px;">
                                <i class="fa fa-bank"></i> Dados Bancários 
                            </h4>
                            <p>    
                                            @if(count($conta->entidade->banco()->get()) > 0)
                                                @foreach($conta->entidade->banco()->get() as $banco)
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
                    </div>             
                </div>   
                <div style="clear: both;"></div>    
             </div>                 
            </div>
        </div>                 
    </div>
</div>
@endsection