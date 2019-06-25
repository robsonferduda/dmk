@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url("conta/detalhes/".\Crypt::encrypt(Auth::user()->cd_conta_con)) }}">Conta</a></li>
        <li>Editar Dados</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-user"></i> Conta <span>> Editar Dados</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Editar Conta </h2>             
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                    <div role="content">
                        <div class="widget-body no-padding">
                            {!! Form::open(['id' => 'frm-update-conta', 'url' => ['contas', $conta->cd_conta_con], 'class' => 'smart-form', 'method' => 'PUT']) !!}
                            <input type="hidden" name="telefones" id="telefones">
                            <input type="hidden" name="emails" id="emails">
                            <input type="hidden" name="registrosBancarios" id="registrosBancarios">
                            <input type="hidden" name="entidade" id="entidade" value="{{ $conta->entidade->cd_entidade_ete }}">
                                <header>
                                    Dados Básicos
                                </header>

                                <fieldset>

                                    <section>
                                        <div class="inline-group">
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="2" {{ ($conta->cd_tipo_pessoa_tpp == 2) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Jurídica</label>
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="1" {{ ($conta->cd_tipo_pessoa_tpp == 1) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Física</label>
                                        </div>
                                    </section>
                                    <div class="row">
                                        <section class="col col-3 box-pessoa-juridica">
                                            <label class="label">CNPJ</label>
                                            <label class="input">
                                                <input type="text" name="cnpj" id="cnpj" class="cnpj" placeholder="00.000.000/000-00" value="{{ ($conta->entidade->cnpj) ? $conta->entidade->cnpj->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>

                                        <section class="col col-3 box-pessoa-fisica">
                                            <label class="label">CPF</label>
                                            <label class="input">
                                                <input type="text" name="cpf" id="cpf" class="cpf" placeholder="000.000.000-000" value="{{ ($conta->entidade->cpf) ? $conta->entidade->cpf->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>

                                        <section class="col col-5">
                                            <label class="label">Razão Social <span class="text-danger">(Obrigatório)</span></label>
                                            <label class="input">
                                                <input type="text" name="nm_razao_social_con" placeholder="Razão Social" value="{{ old('nm_razao_social_con') ? old('nm_razao_social_con') : $conta->nm_razao_social_con }}"> 
                                            </label>
                                        </section>

                                        <section class="col col-2">
                                            <label class="label">OAB</label>
                                            <label class="input">
                                                <input type="text" name="oab" id="oab" class="oab" placeholder="OAB" value="{{ ($conta->entidade->oab) ? $conta->entidade->oab->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>

                                        <section class="col col-4 box-pessoa-juridica">
                                            <label class="label label-tipo-pessoa">Nome Fantasia</label>
                                            <label class="input">
                                                <input type="text" name="nm_fantasia_con" id="nm_fantasia_con" value="{{ old('nm_fantasia_con') ? old('nm_fantasia_con') : $conta->nm_fantasia_con }}" placeholder="Nome Fantasia">
                                            </label>
                                        </section>
                                                                                
                                    </div>                                                          
                                </fieldset>

                                <div class="row" style="padding: 5px 20px;">
            
                                    <header>
                                        <i class="fa fa-map-marker"></i> Endereço 
                                    </header>

                                    <fieldset>

                                        <div class="row">
                                            <section class="col col-2">
                                                <label class="label">CEP</label>
                                                <label class="input">
                                                    <input type="text" class="cep" name="nu_cep_ede" id="cep" placeholder="00000-000" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : ($conta->entidade->endereco) ? $conta->entidade->endereco->nu_cep_ede : '' }}">
                                                </label>
                                            </section> 
                                            <section class="col col-sm-8">
                                                <label class="label">Logradouro</label>
                                                <label class="input">
                                                    <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{old('dc_logradouro_ede') ? old('dc_logradouro_ede') : ($conta->entidade->endereco) ? $conta->entidade->endereco->dc_logradouro_ede : '' }}">
                                                </label>
                                            </section>
                                            <section class="col col-2">
                                                <label class="label">Nº</label>
                                                <label class="input">
                                                    <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{old('nu_numero_ede') ? old('nu_numero_ede') : ($conta->entidade->endereco) ? $conta->entidade->endereco->nu_numero_ede : '' }}">
                                                </label>
                                            </section>
                                        </div>

                                         <div class="row">
                                            <section class="col col-6">
                                                <label class="label">Bairro</label>
                                                <label class="input">
                                                    <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{old('nm_bairro_ede') ? old('nm_bairro_ede') : ($conta->entidade->endereco) ? $conta->entidade->endereco->nm_bairro_ede : '' }}">
                                                </label>
                                            </section>
                                            <section class="col col-6">
                                                <label class="label">Complemento</label>
                                                <label class="input">
                                                    <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('nm_bairro_ede') ? old('dc_complemento_ede') : ($conta->entidade->endereco) ? $conta->entidade->endereco->dc_complemento_ede : '' }}">
                                                </label>
                                            </section>                                                                    
                                        </div> 

                                        <div class="row">                    
                                            <section class="col col-6">                                       
                                                <label class="label" >Estado</label>          
                                                <select  id="estado" name="cd_estado_est" class="select2">
                                                    <option selected value="">Selecione um estado</option>
                                                    @foreach(App\Estado::all() as $estado) 
                                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>
                                            <section class="col col-6">
                                               <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                               <label class="label" >Cidade</label>          
                                                <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                                   <option selected value="">Selecione uma cidade</option>
                                                </select> 
                                            </section>  
                                        </div>
                                    </fieldset>
                                
                            </div>

                                <div class="row" style="margin-bottom: 20px;">
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
                                                    <select class="select2" name="cd_tipo_fone_tfo" id="cd_tipo_fone_tfo">
                                                        <option value="0">Tipo</option>
                                                        @foreach(\App\TipoFone::all() as $tipoFone)
                                                            <option {!! (old('cd_tipo_fone_tfo') == $tipoFone->cd_tipo_fone_tfo ? 'selected' : '') !!}  value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                                        @endforeach   
                                                    </select>
                                                </section> 
                                                <section class="col col-1">
                                                    <button type="button" id="btnSalvarTelefone" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</button>
                                                </section>
                                            </div> 
                                            <div class="row center" id="erroFone"></div>
                                        </fieldset>

                                         <div class="row" style="margin: 0; padding: 5px 13px;">
                                                
                                                <table id="tabelaFone" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="center">Tipo</th>
                                                            <th>Telefone</th>
                                                            <th class="center">Opções</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($conta->entidade->fone()->get() as $fone)
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
                                                    <select  id="cd_tipo_endereco_eletronico_tee" name="cd_tipo_endereco_eletronico_tee" class="select2" style="float: left;">
                                                        <option selected value="">Selecione</option>
                                                            @foreach(\App\TipoEnderecoEletronico::all() as $tipo) 
                                                                <option value="{{$tipo->cd_tipo_endereco_eletronico_tee}}">{{ $tipo->dc_tipo_endereco_eletronico_tee}}</option>
                                                            @endforeach
                                                    </select> 
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
                                                    @foreach($conta->entidade->enderecoEletronico()->get() as $email)
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
                                                <label class="label">CPF</label>
                                                <label class="input">
                                                    <input type="text" class="cpf" name="nu_cpf_cnpj_dba" id="nu_cpf_cnpj_dba" class="cpf" placeholder="CPF" value="{{old('nu_cpf_cnpj_dba')}}">
                                                </label>
                                            </section>   
                                            <section class="col col-4">
                                                <label class="label" >Banco</label>          
                                                <select  name="cd_banco_ban" class="select2" id="cd_banco_ban">
                                                    <option selected value="">Selecione</option>
                                                    @foreach($bancos as $banco)
                                                        <option {!! (old('cd_banco_ban') == str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT) ? 'selected' : '' )!!}  value="{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}}">{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}} - {{ $banco->nm_banco_ban}}</option>
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
                                                        @foreach($tiposConta as $tipoConta)
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
                                                    @foreach($conta->entidade->banco()->get() as $banco)
                                                       <tr>
                                                            <td>{{ $banco->nm_titular_dba }}</td>
                                                            <td>{{ $banco->nu_cpf_cnpj_dba }}</td>
                                                            <td>{{ str_pad($banco->banco->cd_banco_ban,3, '0', STR_PAD_LEFT).' - '.$banco->banco->nm_banco_ban }}</td>
                                                            <td>{{ $banco->tipoConta->nm_tipo_conta_tcb }}</td>
                                                            <td>{{ $banco->nu_agencia_dba }}</td>
                                                            <td>{{ $banco->nu_conta_dba }}</td>
                                                            <td class="center">
                                                                <a class="excluirDadosBancariosBase" style="cursor: pointer;" data-codigo="{{ $banco->cd_dados_bancarios_dba }}"><i class="fa fa-trash"></i> Excluir</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach     
                                                </tbody>
                                            </table>                                               
                                        </div>
                                    </fieldset>
                                </div> 

                                <footer>
                                    <button type="submit" class="btn btn-success"><i class="fa-fw fa fa-save"></i> Salvar</button>
                                    <a href="{{ url('home') }}" class="btn btn-danger"><i class="fa-fw fa fa-times"></i> Cancelar</a>
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
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '../../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Carregando...</option>');
                            $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Selecione uma cidade</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('#cidade').trigger('change');     
                            $('#cidade').prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                    });
            }
        }

        buscaCidade();

        $("#estado").change(function(){
            
            buscaCidade(); 

        });

    });

    $(function() {

            $("#frm-update-conta").validate({
                rules : {
                    nm_razao_social_cli : {
                        required : true
                    }
                },

                messages : {
                    nm_razao_social_cli : {
                        required : 'Campo de preenchimento obrigatório'
                    }
                },
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        });
</script>
@endsection