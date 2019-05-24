@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Usuários</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Novo</span>
            </h1>
        </div>
         <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('usuarios') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Usuários</a>
            <a data-toggle="modal" href="{{ url('usuarios/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>                
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                    
                    data-widget-colorbutton="false" 
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true" 
                    data-widget-sortable="false"
                    
                -->
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Usuário </h2>             
                    
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <!-- widget div-->
                <div role="content">
                    
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                        
                    </div>
                    <!-- end widget edit box -->
                    
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        
                        {!! Form::open(['id' => 'frm-edit-usuario', 'url' => ['usuarios',$usuario->id], 'class' => 'smart-form','method' => 'PUT']) !!}
                            <div class="row">
                                <div  class="col col-6">
                                    <header>
                                        <i class="fa fa-user"></i> Dados Básicos
                                    </header>

                                    <fieldset>
                                       
                                        <div class="row">
                            
                                            <section class="col col-sm-12">
                                                <label class="label">Nome<span class="text-danger">*</span></label>
                                                <label class="input">
                                                    <input required type="text" name="name" placeholder="Nome" value="{{ old('name') ? old('name') : $usuario->name }}">
                                                </label>
                                            </section>
                                      {{--   <section class="col col-4">
                                                <label class="label">Perfil<span class="text-danger">*</span></label>
                                                <label class="select"> 
                                                    <select required name="cd_nivel_niv">
                                                        <option value="" >Selecione</option>
                                                        @foreach($niveis as $nivel)
                                                            <option {{ (old('cd_nivel_niv',$usuario->cd_nivel_niv) ==  $nivel->cd_nivel_niv ? 'selected' : '' ) }} value="{{ $nivel->cd_nivel_niv }}" >{{ $nivel->dc_nivel_niv }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>--}}
                                        </div>
                                        <div class="row">
                                            <section class="col col-4">
                                                <label class="label">Data de Nascimento</label>
                                                <label class="input">
                                                    <input type="text" name="data_nascimento" class="data_nascimento" placeholder="___ /___ /___" value="{{old('data_nascimento') ? old('data_nascimento') : $usuario->data_nascimento}}">
                                                </label>
                                            </section>                                                                                                
                                            <section class="col col-4">
                                                <label class="label">Data de Admissão</label>
                                                <label class="input">
                                                    <input type="text" name="data_admissao" class="data_admissao" placeholder="___ /___ /___" value="{{old('data_admissao') ? old('data_admissao') : $usuario->data_admissao}}">
                                                </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Estado Civil</label>
                                                <label class="select"> 
                                                    <select name="cd_estado_civil_esc">
                                                        <option value="" >Selecione</option>
                                                        @foreach($estadoCivis as $estadoCivil)
                                                            <option {{ (old('cd_estado_civil_esc', $usuario->cd_estado_civil_esc) == $estadoCivil->cd_estado_civil_esc ? 'selected' : '' ) }} value="{{ $estadoCivil->cd_estado_civil_esc }}" >{{ $estadoCivil->nm_estado_civil_esc }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>
                                                                                
                                        </div> 
                                        <div class="row">
                                        <section class="col col-6">
                                            <label class="label">Departamento</label>
                                            <label class="select"> 
                                                <select name="cd_departamento_dep">
                                                    <option value="" >Selecione</option>
                                                    @foreach($departamentos as $departamento)
                                                        <option {!! (old('cd_departamento_dep',$usuario->cd_departamento_dep) == $departamento->cd_departamento_dep ? 'selected' : '' ) !!} value="{{ $departamento->cd_departamento_dep }}" >{{ $departamento->nm_departamento_dep }}</option>
                                                    @endforeach
                                                  
                                                </select> <i></i> </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label">Cargo</label>
                                            <label class="select"> 
                                                <select name="cd_cargo_car">
                                                    <option value="" >Selecione</option>
                                                    @foreach($cargos as $cargo)
                                                        <option {!! (old('cd_cargo_car',$usuario->cd_cargo_car) == $cargo->cd_cargo_car ? 'selected' : '' ) !!} value="{{ $cargo->cd_cargo_car }}" >{{ $cargo->nm_cargo_car }}</option>
                                                    @endforeach
                                                  
                                                </select> <i></i> </label>
                                        </section>
                                    </div>                                              
                                        
                                    </fieldset>
                                </div>  
                                <div  class="col col-6">
                                    <header>
                                        <i class="fa fa-phone"></i> Contatos
                                    </header>
                                    <fieldset>
                                        <div class="row">
                                           <section class="col col-sm-12">
                                                <label class="label">Email<span class="text-danger">* Utilizado na autenticação</span></label>
                                                <label class="input">
                                                    <input required type="text" name="email" class="email" placeholder="E-mail" value="{{old('email') ? old('email') : $usuario->email }}">
                                                </label>
                                            </section>
                                        </div>
                                        <div class="row">
                                            <section class="col col-4">
                                                <label class="label">Tipo do Telefone</label>
                                                <label class="select"> 
                                                    <select name="cd_tipo_fone_tfo">
                                                        <option value="" >Selecione</option>
                                                        @foreach($tiposFone as $tipoFone)
                                                            <option {!! (old('cd_tipo_fone_tfo', $usuario->entidade->fone->cd_tipo_fone_tfo) == $tipoFone->cd_tipo_fone_tfo ? 'selected' : '') !!}  value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>
                                            <section class="col col-8">
                                                <label class="label">Telefone</label>
                                                <label class="input">
                                                    <input type="text" name="nu_fone_fon" placeholder="Ex: (99) 999999999" value="{{old('nu_fone_fon') ? old('nu_fone_fon') : $usuario->entidade->fone->nu_fone_fon }}">
                                                </label>
                                            </section>
                                        </div>
                                     
                                    </fieldset>
                                </div>
                            </div>

                           <!--  <header>
                                <i class="fa fa-key"></i> Autenticação 
                            </header>

                            <fieldset>

                                <div class="row">
                                    <section class="col col-3">
                                        <label class="label">Senha<span class="text-danger">*</span></label>
                                        <label class="input">
                                            <input required type="password" name="password" value="{{old('password') ? old('password') : $usuario->password }}" placeholder="Senha">
                                        </label>
                                    </section>                                    
                                </div> 
                            </fieldset> -->
                            <div class="row">
                                <div class="col col-6">
                                    <header>
                                        <i class="fa fa-file-o"></i> Documentos 
                                    </header>

                                    <fieldset>

                                        <div class="row">
                            
                                            <section class="col col-4">
                                                <label class="label">N º OAB</label>
                                                <label class="input">
                                                    <input type="text" name="oab" placeholder="N º OAB" value="{{old('oab') ? old('oab') : $usuario->entidade->oab->nu_identificacao_ide}}">
                                                </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">CPF</label>
                                                <label class="input">
                                                    <input type="text" name="cpf" class="cpf" placeholder="CPF" value="{{old('cpf') ? old('cpf') : $usuario->entidade->cpf->nu_identificacao_ide}}">
                                                </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">RG</label>
                                                <label class="input">
                                                    <input type="text" name="rg" placeholder="RG" value="{{old('rg') ? old('rg') :  $usuario->entidade->rg->nu_identificacao_ide}}">
                                                </label>
                                            </section>
                                        </div> 
                                    </fieldset>
                                </div>
                                <div class="col col-6">
                                    <header>
                                        <i class="fa fa-bank"></i> Dados Bancários 
                                    </header>

                                    <fieldset>

                                        <div class="row">
                                            
                                            <section class="col col-8">
                                               
                                                <label class="label" >Banco</label>          
                                                <select  name="cd_banco_ban" class="select2">
                                                    <option selected value="">Selecione</option>
                                                    @foreach($bancos as $banco)
                                                        <option {{ (old('cd_banco_ban',$usuario->entidade->banco->cd_banco_ban) == str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT) ? 'selected' : '' ) }}  value="{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}}">{{str_pad($banco->cd_banco_ban,3, '0', STR_PAD_LEFT)}} - {{ $banco->nm_banco_ban}}</option>
                                                    @endforeach

                                                </select> 
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Agência</label>
                                                <label class="input">
                                                    <input type="text" name="nu_agencia_dba" placeholder="Agência" value="{{old('nu_agencia_dba') ? old('nu_agencia_dba') : $usuario->entidade->banco->nu_agencia_dba }}">
                                                </label>
                                            </section>
                                        </div>
                                        <div class="row">
                                            <section class="col col-8">
                                                <label class="label">Tipo de Conta</label>
                                                <label class="select"> 
                                                    <select name="cd_tipo_conta_tcb">
                                                        <option value="" >Selecione</option>
                                                        @foreach($tiposConta as $tipoConta)
                                                            <option {!! (old('cd_tipo_conta_tcb', $usuario->entidade->banco->cd_tipo_conta_tcb) == $tipoConta->cd_tipo_conta_tcb ? 'selected' : '' ) !!}  value="{{ $tipoConta->cd_tipo_conta_tcb }}" >{{ $tipoConta->nm_tipo_conta_tcb }}</option>
                                                        @endforeach
                                                      
                                                    </select> <i></i> </label>
                                            </section>
                                            <section class="col col-4">
                                                <label class="label">Conta</label>
                                                <label class="input">
                                                    <input type="text" name="nu_conta_dba" placeholder="Conta" value="{{old('nu_conta_dba') ? old('nu_conta_dba') : $usuario->entidade->banco->nu_conta_dba }}">
                                                </label>
                                            </section>
                                        </div> 
                                    </fieldset>
                                </div>
                            </div>

                            <header>
                                <i class="fa fa-building"></i> Endereço 
                            </header>

                            <fieldset>

                                <div class="row">
                    
                                    <section class="col col-3">
                                       
                                        <label class="label" >Estado</label>          
                                        <select  id="estado" name="cd_estado_est" class="select2">
                                            <option selected value="">Selecione</option>
                                            @foreach($estados as $estado) 
                                                <option {{ (old('cd_estado_est', $usuario->entidade->endereco->cidade->cd_estado_est) == $estado->cd_estado_est ? 'selected' : '' ) }} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                            @endforeach

                                        </select> 
                                    </section>
                                    <section class="col col-3">
                                       <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde') ? old('cd_cidade_cde') : $usuario->entidade->endereco->cd_cidade_cde }}">
                                       <label class="label" >Cidade</label>          
                                        <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                           <option selected value="">Selecione o Estado</option>
                                        </select> 
                                    </section>  
                                    <section class="col col-4">
                                        <label class="label">Bairro</label>
                                        <label class="input">
                                            <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{old('nm_bairro_ede') ? old('nm_bairro_ede') : $usuario->entidade->endereco->nm_bairro_ede }}">
                                        </label>
                                    </section>      
                                     <section class="col col-2">
                                        <label class="label">CEP</label>
                                        <label class="input">
                                            <input type="text" class="cep" name="nu_cep_ede" placeholder="CEP" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : $usuario->entidade->endereco->nu_cep_ede }}">
                                        </label>
                                    </section>                                  
                                </div> 
                                
                                <div class="row">
                                    <section class="col col-6">
                                        <label class="label">Logradouro</label>
                                        <label class="input">
                                            <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{old('dc_logradouro_ede') ? old('dc_logradouro_ede') : $usuario->entidade->endereco->dc_logradouro_ede }}">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Nº</label>
                                        <label class="input">
                                            <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{old('nu_numero_ede') ? old('nu_numero_ede') : $usuario->entidade->endereco->nu_numero_ede}}">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label class="label">Complemento</label>
                                        <label class="input">
                                            <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('dc_complemento_ede') ? old('dc_complemento_ede') : $usuario->entidade->endereco->dc_complemento_ede }}">
                                        </label>
                                    </section>
                                </div>
                            </fieldset>                           
                            
                            <footer>
                                <button type="submit" class="btn btn-success">
                                   <i class="fa fa-save"></i> Salvar
                                </button>
                            </footer>
                        {!! Form::close() !!}                      
                        
                    </div>
                    <!-- end widget content -->
                    
                </div>
                <!-- end widget div -->
                
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
                            $('#cidade').append('<option selected value="">Selecione</option>');
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
</script>
@endsection
