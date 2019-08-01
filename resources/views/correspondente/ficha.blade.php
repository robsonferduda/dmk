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
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i></span><h2>Cadastro de Correspondente </h2>             
                </header>
                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-edit-usuario', 'url' => ['correspondente/editar',$correspondente->correspondente->cd_entidade_ete], 'class' => 'smart-form','method' => 'PUT']) !!}
                            <input type="hidden" name="conta" id="conta" value="{{ $correspondente->correspondente->cd_conta_con }}">
                            <input type="hidden" name="entidade" id="entidade" value="{{ $correspondente->entidade->cd_entidade_ete }}">
                            <input type="hidden" name="telefones" id="telefones">
                            <input type="hidden" name="emails" id="emails">
                            <input type="hidden" name="registrosBancarios" id="registrosBancarios">
                                    <header>
                                        <i class="fa fa-user"></i> Dados Básicos
                                    </header>
                                    <fieldset>
                                         <section>
                                            <div class="inline-group">
                                                <label class="radio">
                                                    <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="2" {{ ($correspondente->cd_tipo_pessoa_tpp == 2) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Jurídica</label>
                                                <label class="radio">
                                                    <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="1" {{ ($correspondente->cd_tipo_pessoa_tpp == 1) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Física</label>
                                            </div>
                                        </section>
                                        <div class="row">
                                            <section class="col col-3 box-pessoa-juridica">
                                                <label class="label">CNPJ</label>
                                                <label class="input">
                                                    <input type="text" name="cnpj" id="cnpj" class="cnpj" placeholder="00.000.000/0000-00" value="{{ ($correspondente->entidade->cnpj) ? $correspondente->entidade->cnpj->nu_identificacao_ide : '' }}">
                                                </label>
                                            </section>
                    

                                            <section class="col col-3 box-pessoa-fisica">
                                                <label class="label">CPF</label>
                                                <label class="input">
                                                    <input type="text" name="cpf" id="cpf" class="cpf" placeholder="000.000.000-000" value="{{ ($correspondente->entidade->cpf) ? $correspondente->entidade->cpf->nu_identificacao_ide : '' }}">
                                                </label>
                                            </section>
                
                                           <section class="col col-6">
                                                <label class="label">Razão Social/Nome<span class="text-danger"> Campo Obrigatório</span></label>
                                                <label class="input">
                                                    <input required type="text" name="nm_conta_correspondente_ccr" placeholder="Nome" value="{{ old('nm_conta_correspondente_ccr') ? old('nm_conta_correspondente_ccr') : $correspondente->nm_conta_correspondente_ccr }}">
                                                </label>
                                            </section>

                                            <section class="col col-3">
                                                <label class="label">N º OAB</label>
                                                <label class="input">
                                                    <input type="text" name="oab" placeholder="OAB" value="{{old('oab') ? old('oab') : ($correspondente->entidade->oab) ? $correspondente->entidade->oab->nu_identificacao_ide : ''}}">
                                                </label>
                                            </section>    
                                        </div>       
                                    </fieldset>
                            <hr/>

                            <header>
                                <i class="fa fa-map-marker"></i> Comarca de Origem
                                <a href="#" rel="popover-hover" data-placement="top" data-original-title="Comarca de Origem" data-content="Informe a comarca de origem do correspondente. Caso deseje alterar o valor informado, clique sobre ela para excluir e adicione novamente.">
                                <i class="fa fa-question-circle text-primary"></i>
                                </a> 
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select  id="pai_cidade_origem" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label">Cidade</label>          
                                        <select id="cidade_origem" disabled name="cd_cidade_cde" class="select2 pai_cidade_origem">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button data-atuacao="S" type="button" class="btn btn-success adicionar-origem" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</a>
                                    </section>
                                </div> 
                            </fieldset>
                            <div class="row"> 
                                <div class="box_btn_origem" style="margin: 5px 30px;">
                                    @if(count($correspondente->entidade->origem()->get()) > 0)
                                        @foreach($correspondente->entidade->origem()->get() as $atuacao) 
                                            <button type="button" class="btn btn-warning btn-atuacao" style="padding: 3px 8px;" data-id="{{ $atuacao->cd_cidade_atuacao_cat }}">{{ $atuacao->cidade()->first()->nm_cidade_cde }} <i class="fa fa-times"></i></button>
                                        @endforeach
                                    @else
                                        <span class="text-warning erro-origem-vazia"><i class="fa fa-warning"></i> Comarca de origem não informada</span>
                                    @endif
                                </div>
                            </div>
                            <hr/>

                            <header>
                                <i class="fa fa-check"></i> Comarcas de Atuação 
                                <a href="#" rel="popover-hover" data-placement="top" data-original-title="Comarcas de Atuação" data-html="true" data-content="Para informar somente uma cidade, selecione o estado e em seguida a cidade desejada. Para inserir todas as cidades de um estado, selecione o estado e na opção Cidade selecione a opção: <strong>Todas as cidades <strong>">
                                <i class="fa fa-question-circle text-primary"></i>
                                </a> 
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select id="pai_cidade_atuacao" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label">Cidade</label>          
                                        <select id="cidade_atuacao" disabled name="cd_cidade_cde" class="select2 pai_cidade_atuacao">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button data-atuacao="N" type="button" class="btn btn-success adicionar-atuacao" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</a>
                                    </section>
                                </div> 
                            </fieldset>
                            <div class="row">
                                <div class="box_btn_atuacao" style="margin: 5px 30px;">
                                    @if(count($correspondente->entidade->atuacao()->get()) > 0)
                                    <p class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> Clique sobre a cidade para excluir</p>
                                        @foreach($correspondente->entidade->atuacao()->get() as $atuacao) 
                                            <button type="button" class="btn btn-default btn-atuacao" style="padding: 3px 8px;" data-id="{{ $atuacao->cd_cidade_atuacao_cat }}">{{ $atuacao->cidade()->first()->nm_cidade_cde }} <i class="fa fa-times"></i></button>
                                        @endforeach
                                    @else
                                        <span class="text-warning erro-atuacao-vazia"><i class="fa fa-warning"></i> Nenhuma cidade de atuação informada</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row" style="padding: 5px 20px;">
                                <header>
                                    <i class="fa fa-building"></i> Endereço 
                                </header>

                                    <fieldset>

                                        <div class="row">
     
                                            <section class="col col-3">
                                                <label class="label">CEP</label>
                                                <label class="input">
                                                    <input type="text" name="nu_cep_ede" class="cep" placeholder="CEP" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->nu_cep_ede : '' }}">
                                                </label>
                                            </section>     

                                            <section class="col col-9">
                                                <label class="label">Logradouro</label>
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
                                            <section class="col col-4">
                                                <label class="label">Complemento</label>
                                                <label class="input">
                                                    <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('dc_complemento_ede') ? old('dc_complemento_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->dc_complemento_ede : '' }}">
                                                </label>
                                            </section>
                                        </div>

                                        <div class="row">                    
                                            <section class="col col-4">                                               
                                                <label class="label">Estado</label>          
                                                <select id="pai_cidade_endereco" name="cd_estado_est" class="select2 estado">
                                                    <option selected value="">Selecione</option>
                                                    @foreach(\App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                        <option {{ ($correspondente->entidade->endereco and $correspondente->entidade->endereco->cidade) ? (old('cd_estado_est', $correspondente->entidade->endereco->cidade->cd_estado_est) == $estado->cd_estado_est ) ? 'selected' : '' : ''  }} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>
                                            <section class="col col-8">
                                               <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                               <label class="label" >Cidade</label>          
                                                <select  id="cidade" disabled name="cd_cidade_cde" class="select2 pai_cidade_endereco">
                                                   <option selected value="">Selecione a cidade</option>
                                                </select> 
                                            </section>                               
                                        </div> 
                                    </fieldset>  

                                </div>

                            <hr style="margin-top: 20px;" />
                            <div class="row">
                                <div class="col col-6">
                                    <header>
                                        <i class="fa fa-phone"></i> Telefones
                                    </header>
                                    <fieldset>
                                        <div class="row">    
                                            <section class="col col-5">
                                                <label class="input">
                                                    <input type="text" class="form-control telefone" name="nu_fone_fon" id="nu_fone_fon" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" placeholder="(99) 999999999" value="{{old('nu_fone_fon')}}">
                                                </label>
                                            </section>                     
                                            <section class="col col-4">
                                                <label class="select">     
                                                    <select name="cd_tipo_fone_tfo" id="cd_tipo_fone_tfo">
                                                        <option value="0">Tipo</option>
                                                        @foreach(\App\TipoFone::all() as $tipoFone)
                                                            <option {!! (old('cd_tipo_fone_tfo') == $tipoFone->cd_tipo_fone_tfo ? 'selected' : '') !!}  value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                                        @endforeach   
                                                    </select>
                                                <i></i> </label>
                                            </section> 
                                            <section class="col col-1">
                                                <button type="button" id="btnSalvarTelefone" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</button>
                                            </section>
                                        </div> 
                                        <div class="row center" id="erroFone"></div>
                                    </fieldset>

                                    <div class="row" style="margin: 0; padding: 5px 13px;">
                                            @if(count($correspondente->entidade->fone()->get()) == 0)
                                                <div style="margin-bottom: 5px;"><span class="text-danger"> Informe pelo menos um telefone para contato</span></div>
                                            @endif
                                            <table id="tabelaFone" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="center">Tipo</th>
                                                        <th>Telefone</th>
                                                        <th class="center">Opções</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($correspondente->entidade->fone()->get() as $fone)
                                                        <tr>
                                                            <td class="center">{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                            <td>{{ $fone->nu_fone_fon }}</td>
                                                            <td class="center">
                                                                <a class="excluirFoneBase" data-codigo="{{ $fone->cd_fone_fon }}"><i class="fa fa-trash"></i> Excluir</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>                                       
                                            
                                    </div>
                                </div>
                                <div class="col col-6">
                                    <header>
                                        <i class="fa fa-envelope"></i> Emails
                                    </header>

                                    <fieldset>
                                        <div class="row">    
                                            <section class="col col-5">
                                                <label class="input">
                                                    <input type="text" name="dc_endereco_eletronico_ede" id="dc_endereco_eletronico_ede" placeholder="Email" value="{{ old('dc_endereco_eletronico_ede') }}">
                                                </label>
                                            </section>                     
                                            <section class="col col-4">  
                                                <label class="select">  
                                                    <select  id="cd_tipo_endereco_eletronico_tee" name="cd_tipo_endereco_eletronico_tee" style="float: left;">
                                                        <option selected value="">Selecione</option>
                                                            @foreach(\App\TipoEnderecoEletronico::all() as $tipo) 
                                                                <option value="{{$tipo->cd_tipo_endereco_eletronico_tee}}">{{ $tipo->dc_tipo_endereco_eletronico_tee}}</option>
                                                            @endforeach
                                                    </select> 
                                                <i></i> </label>
                                            </section> 
                                            <section class="col col-1">
                                                <button type="button" id="btnSalvarEmail" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</button>
                                            </section>
                                        </div> 
                                        <div class="row center" id="erroEmail"></div>
                                    </fieldset>

                                    <div class="row" style="margin: 0; padding: 5px 13px;">
                                        <table id="tabelaEmail" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="center">Tipo</th>
                                                    <th>Email</th>
                                                    <th class="center">Opções</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($correspondente->entidade->enderecoEletronico()->get() as $email)
                                                    <tr>
                                                        <td class="center">{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</td>
                                                        <td>{{ $email->dc_endereco_eletronico_ede }} </td>                                                        
                                                        <td class="center">
                                                            <a class="excluirEmailBase" data-codigo="{{ $email->cd_endereco_eletronico_ele }}"><i class="fa fa-trash"></i> Excluir</a>
                                                        </td>
                                                    </tr>                                                  
                                                @endforeach  
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            
                                
                             <div class="row" style="padding: 5px 20px;">
                                    <header>
                                        <i class="fa fa-bank"></i> Dados Bancários 
                                    </header>
                                           
                                            <fieldset>

                                                 <div class="row">
                                                    <section class="col col-4">
                                                        <label class="label">Titular</label>
                                                        <label class="input">
                                                            <input type="text" name="nm_titular_dba" id="nm_titular_dba" placeholder="TItular" value="{{old('nm_titular_dba')}}">
                                                        </label>
                                                    </section> 
                                                    <section class="col col-4">  
                                                        <label class="label">CPF/CNPJ <span class="text-primary" style="margin-bottom: 5px;"> (Digite somente números)</span></label>
                                                        <label class="input">
                                                            <input type="text" name="nu_cpf_cnpj_dba" id="nu_cpf_cnpj_dba" placeholder="CPF" value="{{old('nu_cpf_cnpj_dba')}}">
                                                        </label>
                                                    </section>   
                                                    <section class="col col-4">
                                                        <label class="label" >Banco</label>          
                                                        <select  name="cd_banco_ban" class="select2" id="cd_banco_ban">
                                                            <option selected value="">Selecione</option>
                                                            @foreach(\App\Banco::all() as $banco)
                                                                <option {!! (old('cd_banco_ban') == str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT) ? 'selected' : '' )!!}  value="{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}}">{{ $banco->nm_banco_ban}}</option>
                                                            @endforeach

                                                        </select> 
                                                    </section>                        
                                                </div>
                                                <div class="row">
                                                    <section class="col col-4">
                                                        <label class="label">Tipo de Conta</label>
                                                        <label class="select"> 
                                                            <select name="cd_tipo_conta_tcb" id="cd_tipo_conta_tcb">
                                                                <option value="" >Selecione</option>
                                                                @foreach(\App\TipoConta::all() as $tipoConta)
                                                                    <option {!! (old('cd_tipo_conta_tcb') == $tipoConta->cd_tipo_conta_tcb ? 'selected' : '' ) !!}  value="{{ $tipoConta->cd_tipo_conta_tcb }}" >{{ $tipoConta->nm_tipo_conta_tcb }}</option>
                                                                @endforeach
                                                              
                                                            </select> <i></i> </label>
                                                    </section>
                                                    <section class="col col-4">
                                                        <label class="label">Agência</label>
                                                        <label class="input">
                                                            <input type="text" name="nu_agencia_dba" placeholder="Agência" value="{{old('nu_agencia_dba')}}" id="nu_agencia_dba">
                                                        </label>
                                                    </section>
                                                    <section class="col col-4">
                                                        <label class="label">Conta</label>
                                                        <label class="input">
                                                            <input type="text" name="nu_conta_dba" placeholder="Conta" value="{{old('nu_conta_dba')}}" id="nu_conta_dba">
                                                        </label>
                                                    </section>
                                                </div>

                                                <div class="row">
                                                    <section class="col col-sm-12">
                                                        <button type="button" id="btnSalvarContaBancaria" class="btn btn-success" style="padding: 6px 15px;float: right;"><i class="fa fa-plus"></i> Adicionar</button>
                                                    </section>
                                                </div>
                                                <div class="row center" id="erroContaBancaria"></div>
                                                </fieldset>
                                                <div class="row" style="margin: 0; padding: 5px 13px;">            
                                                    <table id="tabelaRegistroBancario" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Titular</th>
                                                                <th>CPF</th>
                                                                <th>Banco</th>
                                                                <th>Tipo de Conta</th>
                                                                <th>Agência</th>
                                                                <th>Conta</th>
                                                                <th class="center">Opções</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($correspondente->entidade->banco()->get() as $banco)
                                                               <tr>
                                                                    <td>{{ $banco->nm_titular_dba }}</td>
                                                                    <td>{{ $banco->nu_cpf_cnpj_dba }}</td>
                                                                    <td>{{ str_pad($banco->banco->cd_banco_ban,3, '0', STR_PAD_LEFT).' - '.$banco->banco->nm_banco_ban }}</td>
                                                                    <td>{{ $banco->tipoConta->nm_tipo_conta_tcb }}</td>
                                                                    <td>{{ $banco->nu_agencia_dba }}</td>
                                                                    <td>{{ $banco->nu_conta_dba }}</td>
                                                                    <td class="center">
                                                                        <span>
                                                                            <a class="editarDadosBancarios" style="cursor: pointer;" data-codigo="{{ $banco->cd_dados_bancarios_dba }}"><i class="fa fa-edit"></i> Editar</a>
                                                                        </span>
                                                                        <span>
                                                                            <a class="excluirDadosBancariosBase" style="cursor: pointer;" data-codigo="{{ $banco->cd_dados_bancarios_dba }}"><i class="fa fa-trash"></i> Excluir</a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach     
                                                        </tbody>
                                                    </table>                                               
                                                </div>

                                </div>

                                <div class="row" style="padding: 5px 20px;">
                                    <header>
                                        <i class="fa  fa-file-text-o"></i> Observações 
                                    </header>
                                    <fieldset>
                                        <div class="row"> 
                                            <section class="col col-sm-12">
                                            <label class="input">
                                                <textarea class="form-control" rows="4" name="obs_ccr" id="observacao" value="{{old('obs_ccr')}}" >{{old('obs_ccr') ? old('obs_ccr') : ($correspondente->obs_ccr) ? $correspondente->obs_ccr : '' }}</textarea>
                                            </label>
                                            </section> 
                                        </div>
                                    </fieldset>
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
<div class="modal fade modal_top_alto" id="modal_erro_atuacao" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> Erro de Processamento<strong></strong></h4>
            </div>
                <div class="modal-body" style="text-align: center;">
                        <h4 class="text-danger"><i class="fa fa-times"></i> Ops...</h4>
                        <h4>Ocorreu um erro ao processar sua operação.</h4>
                        <h4 class="msg_erro_adicao"></h4>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        $('.adicionar-atuacao').click(function(){

            var entidade = $("#entidade").val();
            var estado = $("#pai_cidade_atuacao").val();
            var cidade = $("#cidade_atuacao").val();
            var atuacao = $(this).data("atuacao");

            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/atuacao/adicionar",
                dataType: "json",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "entidade": entidade,
                    "cidade": cidade,
                    "estado": estado,
                    "atuacao": atuacao
                },
                beforeSend: function()
                {
                    $("#processamento").modal('show');
                },
                success: function(response)
                {
                    $(".box_btn_atuacao button").remove();
                    $(".erro-atuacao-vazia").remove();
                    loadAtuacao(entidade);
                    loadOrigem(entidade);
                    $("#processamento").modal('hide');
                    
                },
                error: function(response)
                {
                    console.log(response.responseJSON.msg);
                    $("#processamento").modal('hide');
                    $(".msg_erro_adicao").html(response.responseJSON.msg);
                    $("#modal_erro_atuacao").modal('show');
                }
            });


        });

        $('.adicionar-origem').click(function(){

            var entidade = $("#entidade").val();
            var cidade = $("#cidade_origem").val();
            var atuacao = $(this).data("atuacao");

            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/atuacao/adicionar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "entidade": entidade,
                    "cidade": cidade,
                    "atuacao": atuacao
                },
                beforeSend: function()
                {
                    $("#processamento").modal('show');
                },
                success: function(response)
                {
                    $(".box_btn_atuacao button").remove();
                    $(".erro-atuacao-vazia").remove();
                    loadOrigem(entidade);
                    loadAtuacao(entidade);
                    $("#processamento").modal('hide');
                    
                },
                error: function(response)
                {
                    console.log(response.responseJSON.msg);
                    $("#processamento").modal('hide');
                    $(".msg_erro_adicao").html(response.responseJSON.msg);
                    $("#modal_erro_atuacao").modal('show');
                }
            });


        });

        function loadAtuacao(entidade){

            $.ajax({

                url: "../../correspondente/atuacao/"+entidade,
                type: 'GET',
                dataType: "JSON",

                success: function(response)
                {                       
                    $.each(response, function(index, value){
                        $('.box_btn_atuacao').append('<button type="button" class="btn btn-default btn-atuacao" style="padding: 3px 8px;" data-id="'+value.cd_cidade_atuacao_cat+'"> '+value.cidade.nm_cidade_cde+' <i class="fa fa-times"></i> </button>');
                    });

                    $('.btn-atuacao').on('click', function(){

                        atuacao = $(this).data("id");
                        entidade = $("#entidade").val();

                        $.ajax({
                                url: '../../correspondente/atuacao/excluir/'+atuacao,
                                type: 'GET',
                                dataType: "JSON",
                            success: function(response)
                            {                
                                $(".box_btn_atuacao button").remove();       
                                loadAtuacao(entidade);
                                loadOrigem(entidade);
                            },
                            error: function(response)
                            {

                            }
                        });

                    });   
                },
                error: function(response)
                {

                }
            });
        }

        function loadOrigem(entidade){

            $.ajax({

                url: "../../correspondente/origem/"+entidade,
                type: 'GET',
                dataType: "JSON",

                success: function(response)
                {                   
                    $(".box_btn_origem button").remove();
                    $('.erro-origem-vazia').html("");

                    $.each(response, function(index, value){
                        $('.box_btn_origem').append('<button type="button" class="btn btn-warning btn-atuacao" style="padding: 3px 8px;" data-id="'+value.cd_cidade_atuacao_cat+'">'+value.cidade.nm_cidade_cde+' <i class="fa fa-times"></i> </button>');
                    });

                    $('.btn-atuacao').on('click', function(){

                        atuacao = $(this).data("id");
                        entidade = $("#entidade").val();

                        $.ajax({
                                url: '../../correspondente/atuacao/excluir/'+atuacao,
                                type: 'GET',
                                dataType: "JSON",
                            success: function(response)
                            {                
                                $(".box_btn_atuacao button").remove();       
                                loadAtuacao(entidade);
                                loadOrigem(entidade);
                            },
                            error: function(response)
                            {

                            }
                        });

                    });   
                },
                error: function(response)
                {

                }
            });
        }

        $(".btn-atuacao").click(function(){

            atuacao = $(this).data("id");
            entidade = $("#entidade").val();

            $.ajax({
                    url: '../../correspondente/atuacao/excluir/'+atuacao,
                    type: 'GET',
                    dataType: "JSON",
                success: function(response)
                {                
                    $(".box_btn_atuacao button").remove();       
                    $(".box_btn_origem button").remove(); 
                    loadAtuacao(entidade);
                    loadOrigem(entidade);
                },
                error: function(response)
                {

                }
            });

        });

        var buscaCidade = function(estado,target){

            if(estado != ''){

                $.ajax(
                    {
                        url: '../../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Carregando...</option>');
                            $('.'+target).prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Selecione</option>');
                            $('.'+target).append('<option value="0">Todas as cidades</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('.'+target).append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('.'+target).append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('.'+target).trigger('change');     
                            $('.'+target).prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                    });
            }
        }

        $(".estado").change(function(){
            buscaCidade($(this).val(),$(this).attr('id')); 
        });

    });
</script>
@endsection