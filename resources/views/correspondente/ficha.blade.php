@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Dados</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Dados</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            @if($flag)
                <div class="alert alert-block alert-warning">
                    <span class="alert-heading">Atenção!</span>
                    Para utilizar todas as funcionalidades do sistema, você deve preencher os dados obrigatórios do seu perfil
                </div>
            @endif
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i></span><h2>Cadastro de Usuário </h2>             
                </header>
                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-edit-usuario', 'url' => ['correspondente/editar',$correspondente->cd_entidade_ete], 'class' => 'smart-form','method' => 'PUT']) !!}
                            <input type="hidden" name="conta" value="{{ $correspondente->cd_conta_con }}">
                            <div class="row">
                                <div  class="col col-8">
                                    <header>
                                        <i class="fa fa-user"></i> Dados Básicos
                                    </header>
                                    <fieldset>
                                        <div class="row">
                                            <section class="col col-8">
                                                <label class="label">Razão Social/Nome<span class="text-danger"> Campo Obrigatório</span></label>
                                                <label class="input">
                                                    <input required type="text" name="name" placeholder="Nome" value="{{ old('name') ? old('name') : $correspondente->nm_razao_social_con }}">
                                                </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Tipo de Pessoa <span class="text-danger">Campo Obrigatório</span></label>
                                                <label class="select"> 
                                                    <select required name="cd_tipo_pessoa_tpp">
                                                        <option value="">Selecione</option>
                                                        @foreach(\App\TipoPessoa::all() as $tipo)
                                                            <option {{ (old('cd_nivel_niv',$correspondente->cd_tipo_pessoa_tpp) ==  $tipo->cd_tipo_pessoa_tpp ? 'selected' : '' ) }} value="{{ $tipo->cd_tipo_pessoa_tpp }}" >{{ $tipo->nm_tipo_pessoa_tpp }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>

                                        </div>  
                                        <div class="row">                            
                                            <section class="col col-4">
                                                <label class="label">N º OAB</label>
                                                <label class="input">
                                                    <input type="text" name="oab" placeholder="OAB" value="{{old('oab') ? old('oab') : ($correspondente->entidade->oab) ? $correspondente->entidade->oab->nu_identificacao_ide : ''}}">
                                                </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">CPF</label>
                                                <label class="input">
                                                    <input type="text" name="cpf" class="cpf" placeholder="CPF" value="{{old('cpf') ? old('cpf') : ($correspondente->entidade->cpf) ? $correspondente->entidade->cpf->nu_identificacao_ide : '' }}">
                                                </label>
                                            </section>
                                        </div>                
                                    </fieldset>
                                </div>  
                                <div  class="col col-4">
                                    <header>
                                        <i class="fa fa-envelope"></i> Emails
                                    </header>
                                    <fieldset>
                                        <div class="row">
                                           
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        
                            <header>
                                <i class="fa fa-check"></i> Cidades de Atuação 
                            </header>

                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select  id="estado" name="cd_estado_est" class="select2">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::all() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label">Cidade <span class="text-danger"> Campo Obrigatório</span></label>          
                                        <select id="cidade" disabled name="cd_cidade_cde" class="select2">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button type="button" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-check"></i> Adicionar</a>
                                    </section>
                                </div> 
                                <span class="text-danger"> Informe pelo menos uma cidade de atuação</span>
                            </fieldset>
                                
                            <div class="row">
                                <div class="col col-8">
                                    <header>
                                        <i class="fa fa-building"></i> Endereço 
                                    </header>

                                    <fieldset>

                                        <div class="row">
     
                                            <section class="col col-2">
                                                <label class="label">CEP</label>
                                                <label class="input">
                                                    <input type="text" name="nu_cep_ede" placeholder="CEP" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->nu_cep_ede : '' }}">
                                                </label>
                                            </section>     

                                            <section class="col col-8">
                                                <label class="label">Logradouro <span class="text-danger"> Campo Obrigatório</span></label>
                                                <label class="input">
                                                    <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{old('dc_logradouro_ede') ? old('dc_logradouro_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->dc_logradouro_ede : '' }}">
                                                </label>
                                            </section>
                                            <section class="col col-2">
                                                <label class="label">Nº</label>
                                                <label class="input">
                                                    <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{old('nu_numero_ede') ? old('nu_numero_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->nu_numero_ede : '' }}">
                                                </label>
                                            </section>
                                            <section class="col col-6">
                                                <label class="label">Bairro</label>
                                                <label class="input">
                                                    <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{old('nm_bairro_ede') ? old('nm_bairro_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->nm_bairro_ede : '' }}">
                                                </label>
                                            </section> 
                                            <section class="col col-6">
                                                <label class="label">Complemento</label>
                                                <label class="input">
                                                    <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('dc_complemento_ede') ? old('dc_complemento_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->dc_complemento_ede : '' }}">
                                                </label>
                                            </section>
                                        </div>

                                        <div class="row">                    
                                            <section class="col col-4">
                                               
                                                <label class="label" >Estado</label>          
                                                <select  id="estado" name="cd_estado_est" class="select2">
                                                    <option selected value="">Selecione</option>
                                                    @foreach(\App\Estado::all() as $estado) 
                                                        <option {{ ($correspondente->entidade->endereco and $correspondente->entidade->endereco->cidade) ? (old('cd_estado_est', $correspondente->entidade->endereco->cidade->cd_estado_est) == $estado->cd_estado_est ) ? 'selected' : '' : ''  }} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>
                                            <section class="col col-8">
                                               <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                               <label class="label" >Cidade <span class="text-danger"> Campo Obrigatório</span></label>          
                                                <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                                   <option selected value="">Selecione a cidade</option>
                                                </select> 
                                            </section>                               
                                        </div> 
                                    </fieldset>  
                                </div>
                                <div class="col col-4">
                                    <header>
                                        <i class="fa fa-phone"></i> Telefones
                                    </header>
                                    <fieldset>
                                        <div class="row">
                                           
                                        </div>
                                    </fieldset>
                                </div>
                            </div>                        

                            <header>
                                <i class="fa fa-bank"></i> Dados Bancários 
                            </header>
                            <div class="row">
                                <div class="col col-6">
                                   
                                    <fieldset>

                                        <div class="row">
                                            
                                            <section class="col col-8">
                                               
                                                <label class="label" >Banco</label>          
                                                <select  name="cd_banco_ban" class="select2">
                                                    <option selected value="">Selecione</option>
                                                    @foreach(\App\Banco::all() as $banco)
                                                        <option {{ ($correspondente->entidade->banco) ? (old('cd_banco_ban',$correspondente->entidade->banco->cd_banco_ban) == str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT) ? 'selected' : '' ) : '' }}  value="{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}}">{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}} - {{ $banco->nm_banco_ban}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Agência</label>
                                                <label class="input">
                                                    <input type="text" name="nu_agencia_dba" placeholder="Agência" value="{{old('nu_agencia_dba') ? old('nu_agencia_dba') : ($correspondente->entidade->banco) ? $correspondente->entidade->banco->nu_agencia_dba : '' }}">
                                                </label>
                                            </section>
                                        </div>
                                        <div class="row">
                                            <section class="col col-8">
                                                <label class="label">Tipo de Conta</label>
                                                <label class="select"> 
                                                    <select name="cd_tipo_conta_tcb">
                                                        <option value="" >Selecione</option>
                                                        @foreach(\App\TipoConta::all() as $tipoConta)
                                                            <option {!! ($correspondente->entidade->banco) ? (old('cd_tipo_conta_tcb', $correspondente->entidade->banco->cd_tipo_conta_tcb) == $tipoConta->cd_tipo_conta_tcb ? 'selected' : '' ) : '' !!}  value="{{ $tipoConta->cd_tipo_conta_tcb }}" >{{ $tipoConta->nm_tipo_conta_tcb }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Conta</label>
                                                <label class="input">
                                                    <input type="text" name="nu_conta_dba" placeholder="Conta" value="{{old('nu_conta_dba') ? old('nu_conta_dba') : ($correspondente->entidade->banco) ? $correspondente->entidade->banco->nu_conta_dba : '' }}">
                                                </label>
                                            </section>
                                        </div> 
                                    </fieldset>
                                </div>
                            </div>

                            
                            
                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Atualizar Dados </button>
                                <a href="{{ url('correspondente/dashboard/'.$correspondente->entidade->cd_entidade_ete) }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar </a>
                            </footer>
                        {!! Form::close() !!}                      
                    </div>
                </div>                
            </div>
        </article>
    </div>
</div>
@endsection