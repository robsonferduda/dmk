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
                        @if(file_exists('public/img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png')) 
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @else
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 70%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @endif
                    </div>
                    <h4 class="center" style="margin-top: 15px;"><strong>{{ $correspondente->nm_razao_social_con }}</strong></h4>
                    <h6><a href="#" class="alterar_senha" data-id="{{ \Crypt::encrypt(Auth::user()->id) }}"><i class="fa fa-lock"></i> Alterar Senha</a></h6>
                </div>
                <div class="col-md-9">
                    <div class="col-md-6">
                        <h2 style="margin-bottom: 5px;">{{ $correspondente->nm_razao_social_con }} <a href="{{ url('correspondente/ficha/'.\Crypt::encrypt($correspondente->cd_conta_con)) }}"><span class="fa fa-edit"></span></a></h2>
                            <ul class="list-unstyled">
                               
                                <li style="margin-bottom: 8px;">
                                    {!! ($correspondente->tipoPessoa()->first()) ? '<span class="label label-primary">Pessoa '.$correspondente->tipoPessoa()->first()->nm_tipo_pessoa_tpp.'</span>' : '' !!}
                                </li>
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:$usuario->email">{{ $correspondente->entidade->usuario->email }}</a>
                                    </p>
                                </li>                                
                                @if($correspondente->entidade->cpf()->first())
                                    <li>
                                        <p class="text-muted">
                                            <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>CPF </strong>: {{ ($correspondente->entidade->cpf()->first()) ? $correspondente->entidade->cpf()->first()->nu_identificacao_ide : 'Não informado' }} </span>
                                        <p>
                                    </li>
                                @elseif($correspondente->entidade->cnpj()->first())
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
                                <li>
                                    <p class="text-muted">
                                        <i class="fa fa-tag"></i>&nbsp;&nbsp;<span class="txt-color-darken"><strong>Comarca de Origem</strong>: {{ ($correspondente->entidade->atuacao()->where('fl_origem_cat','S')->first()) ?  $correspondente->entidade->atuacao()->where('fl_origem_cat','S')->first()->cidade()->first()->nm_cidade_cde : 'Não informado' }}</span>
                                    </p>
                                </li>
                            </ul>

                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-phone"></i> Telefones
                        </h4>
                        <p>
                            @if(count($correspondente->entidade->fone()->get()) > 0)
                                @foreach($correspondente->entidade->fone()->get() as $fone)
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
                            <i class="fa fa-map-marker"></i> Cidades de Atuação
                        </h4>
                        <div style="margin-top: 10px; margin-bottom: 10px;">
                            @if(count($correspondente->entidade->atuacao()->get()) > 0)
                                @foreach($correspondente->entidade->atuacao()->get() as $atuacao) 
                                    <button type="button" class="btn btn-default" style="padding: 3px 8px; margin-top: 5px;" data-id="{{ $atuacao->cd_cidade_atuacao_cat }}">{{ $atuacao->cidade()->first()->nm_cidade_cde }}</button>
                                @endforeach
                            @else
                                <span class="text-danger"> Informe pelo menos uma cidade de atuação</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 style="margin-top: 25px;">
                            <i class="fa fa-map-marker"></i> Endereço 
                        </h4>
                        <p>
                            @if($correspondente->entidade->endereco)
                            <ul class="list-unstyled">
                                <li>
                                    <strong>CEP: </strong> {{ $correspondente->entidade->endereco->nu_cep_ede }}
                                </li>
                                <li>
                                    <strong>Logradouro: </strong> {{ $correspondente->entidade->endereco->dc_logradouro_ede }}
                                </li>
                                <li>
                                    <strong>Número: </strong> {{ $correspondente->entidade->endereco->nu_numero_ede }}
                                </li>
                                <li>
                                    <strong>Complemento: </strong> {{ $correspondente->entidade->endereco->dc_complemento_ede }}
                                </li>
                                <li>
                                    <strong>Bairro: </strong> {{ $correspondente->entidade->endereco->nm_bairro_ede }}
                                </li>
                                <li>
                                    <strong>Cidade/Estado: </strong> {{ ($correspondente->entidade->endereco->cidade) ? $correspondente->entidade->endereco->cidade->nm_cidade_cde.'/'.$correspondente->entidade->endereco->cidade->estado->nm_estado_est : 'Não informado' }}
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
                </div>             
            </div>   
            <div style="clear: both;"></div>    
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
                            <input type="hidden" name="fl_conta" value="S">
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