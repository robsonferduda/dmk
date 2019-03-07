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
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Novo</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
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
                        
                        {!! Form::open(['id' => 'frm-add-usuario', 'url' => 'usuarios', 'class' => 'smart-form']) !!}
                            <header>
                                Dados Básicos
                            </header>

                            <fieldset>
                               
                                <div class="row">
                    
                                    <section class="col col-6">
                                        <label class="label">Nome<sup class="text-danger">*</sup></label>
                                        <label class="input">
                                            <input required type="text" name="name" placeholder="Nome">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Perfil</label>
                                        <label class="select"> 
                                            <select name="cd_nivel_niv">
                                                <option value="" >Selecione</option>
                                                @foreach($niveis as $nivel)
                                                    <option value="{{ $nivel->cd_nivel_niv }}" >{{ $nivel->dc_nivel_niv }}</option>
                                                @endforeach
                                              
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Data de Nascimento</label>
                                        <label class="input">
                                            <input type="text" name="data_nascimento"  placeholder="Data de Nascimento">
                                        </label>
                                    </section>
                                    
                                </div> 

                                <div class="row ">                                  
                                    <section class="col col-3">
                                        <label class="label">Data de Admissão</label>
                                        <label class="input">
                                            <input type="text" name="data_admissao" placeholder="Data de Admissão">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Estado Civil</label>
                                        <label class="select"> 
                                            <select name="cd_estado_civil_esc">
                                                <option value="" >Selecione</option>
                                                @foreach($estadoCivis as $estadoCivil)
                                                    <option value="{{ $estadoCivil->cd_estado_civil_esc }}" >{{ $estadoCivil->nm_estado_civil_esc }}</option>
                                                @endforeach
                                              
                                            </select> <i></i> </label>
                                    </section>
                                                                        
                                </div>                                   
                                
                            </fieldset>

                            <header>
                                <i class="fa fa-phone"></i> Contatos
                            </header>
                            <fieldset>
                                <div class="row">
                                   <section class="col col-6">
                                        <label class="label">Email<sup class="text-danger">* Utilizado na autenticação</sup></label>
                                        <label class="input">
                                            <input required type="text" name="email" placeholder="E-mail">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Tipo do Telefone</label>
                                        <label class="select"> 
                                            <select name="cd_tipo_fone_tfo">
                                                <option value="" >Selecione</option>
                                                @foreach($tiposFone as $tipoFone)
                                                    <option value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                                @endforeach
                                              
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Telefone</label>
                                        <label class="input">
                                            <input type="text" name="nu_fone_fon" placeholder="XXXXXX">
                                        </label>
                                    </section>
                                </div>
                             
                            </fieldset>

                            <header>
                                <i class="fa fa-key"></i> Autenticação 
                            </header>

                            <fieldset>

                                <div class="row">
                                    <section class="col col-3">
                                        <label class="label">Senha<sup class="text-danger">*</sup></label>
                                        <label class="input">
                                            <input required type="password" name="password" placeholder="Senha">
                                        </label>
                                    </section>                                    
                                </div> 
                            </fieldset>

                            <header>
                                <i class="fa fa-file-o"></i> Documentos 
                            </header>

                            <fieldset>

                                <div class="row">
                    
                                    <section class="col col-3">
                                        <label class="label">N º OAB</label>
                                        <label class="input">
                                            <input type="text" name="oab" placeholder="N º OAB">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">CPF</label>
                                        <label class="input">
                                            <input type="text" name="cpf" placeholder="CPF">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">RG</label>
                                        <label class="input">
                                            <input type="text" name="rg" placeholder="RG">
                                        </label>
                                    </section>
                                </div> 
                            </fieldset>

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
                                                <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                            @endforeach

                                        </select> 
                                    </section>
                                    <section class="col col-3">
                                       <label class="label" >Cidade</label>          
                                        <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                           <option selected value="">Selecione o Estado</option>
                                        </select> 
                                    </section>  
                                    <section class="col col-4">
                                        <label class="label">Bairro</label>
                                        <label class="input">
                                            <input type="text" name="nm_bairro_ede" placeholder="Bairro">
                                        </label>
                                    </section>      
                                     <section class="col col-2">
                                        <label class="label">CEP</label>
                                        <label class="input">
                                            <input type="text" name="nu_cep_ede" placeholder="CEP">
                                        </label>
                                    </section>                                  
                                </div> 
                                
                                <div class="row">
                                    <section class="col col-6">
                                        <label class="label">Logradouro</label>
                                        <label class="input">
                                            <input type="text" name="dc_logradouro_ede" placeholder="Logradouro">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Nº</label>
                                        <label class="input">
                                            <input type="text" name="nu_numero_ede" placeholder="Nº">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label class="label">Complemento</label>
                                        <label class="input">
                                            <input type="text" name="dc_complemento_ede" placeholder="Complemento">
                                        </label>
                                    </section>
                                </div>
                            </fieldset>

                            <header>
                                <i class="fa fa-bank"></i> Dados Bancários 
                            </header>

                            <fieldset>

                                <div class="row">
                                    
                                    <section class="col col-4">
                                       
                                        <label class="label" >Banco</label>          
                                        <select  id="estado" name="cd_estado_est" class="select2">
                                            <option selected value="">Selecione</option>
                                            @foreach($bancos as $banco)
                                                <option value="{{$banco->cd_banco_ban}}">{{ $banco->cd_banco_ban}} - {{ $banco->nm_banco_ban}}</option>
                                            @endforeach

                                        </select> 
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Agência</label>
                                        <label class="input">
                                            <input type="text" name="oab" placeholder="Agência">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Tipo de Conta</label>
                                        <label class="select"> 
                                            <select name="cd_nivel_niv">
                                                <option value="" >Selecione</option>
                                                @foreach($niveis as $nivel)
                                                    <option value="{{ $nivel->cd_nivel_niv }}" >{{ $nivel->dc_nivel_niv }}</option>
                                                @endforeach
                                              
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Conta</label>
                                        <label class="input">
                                            <input type="text" name="rg" placeholder="Conta">
                                        </label>
                                    </section>
                                </div> 
                            </fieldset>
                            
                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    Cadastrar
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
       
        $("#estado").change(function(){

            estado = $(this).val();

            $.ajax(
            {
                url: '../cidades-por-estado/'+estado,
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

                        $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');
                        
                    });       
                    $('#cidade').trigger('change');     
                    $('#cidade').prop( "disabled", false );        
                },
                error: function(response)
                {
                    //console.log(response);
                }
            });

        });

        $('#limparCidades').click(function(){
            duallistbox.empty();
            duallistbox.bootstrapDualListbox('refresh');
        })

    });
</script>
@endsection
