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
            @if($flag or Session::get('flag'))
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
                            <input type="hidden" name="conta" id="conta" value="{{ $correspondente->cd_conta_con }}">
                            <input type="hidden" name="entidade" id="entidade" value="{{ $correspondente->entidade->cd_entidade_ete }}">
                            <input type="hidden" name="telefones" id="telefones">
                            <input type="hidden" name="emails" id="emails">
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
                                                    <input type="text" name="cnpj" id="cnpj" class="cnpj" placeholder="00.000.000/000-00" value="{{ ($correspondente->entidade->cnpj) ? $correspondente->entidade->cnpj->nu_identificacao_ide : '' }}">
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
                                                    <input required type="text" name="nm_razao_social_con" placeholder="Nome" value="{{ old('nm_razao_social_con') ? old('nm_razao_social_con') : $correspondente->nm_razao_social_con }}">
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
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select  id="pai_cidade_origem" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::all() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label">Cidade</label>          
                                        <select id="cidade_atuacao" disabled name="cd_cidade_cde" class="select2 pai_cidade_origem">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button data-atuacao="S" type="button" class="btn btn-success adicionar-atuacao" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</a>
                                    </section>
                                </div> 
                            </fieldset>

                            <hr/>

                            <header>
                                <i class="fa fa-check"></i> Cidades de Atuação 
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select  id="pai_cidade_atuacao" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::all() as $estado) 
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
                                        <span class="text-danger erro-atuacao-vazia"> Informe pelo menos uma cidade de atuação</span>
                                    @endif
                                </div>
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
                            <hr style="margin-top: 20px;" />
                            <div class="row">
                                <div class="col col-6">
                                    <header>
                                        <i class="fa fa-building"></i> Endereço 
                                    </header>

                                    <fieldset>

                                        <div class="row">
     
                                            <section class="col col-3">
                                                <label class="label">CEP</label>
                                                <label class="input">
                                                    <input type="text" name="nu_cep_ede" placeholder="CEP" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : ($correspondente->entidade->endereco) ? $correspondente->entidade->endereco->nu_cep_ede : '' }}">
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
                                                    @foreach(\App\Estado::all() as $estado) 
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
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        $('.adicionar-atuacao').click(function(){

            var entidade = $("#entidade").val();
            var cidade = $("#cidade_atuacao").val();
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
                    loadAtuacao(entidade);
                    $("#processamento").modal('hide');
                    
                },
                error: function(response)
                {
                    console.log("Erro");
                    $("#processamento").modal('hide');
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
                        $('.box_btn_atuacao').append('<button type="button" class="btn btn-default btn-atuacao" style="padding: 3px 8px;" data-id="'+value.cd_cidade_atuacao_cat+'"><i class="fa fa-times"></i> '+value.cidade.nm_cidade_cde+'</button>');
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
                    url: pathname+'../../correspondente/atuacao/excluir/'+atuacao,
                    type: 'GET',
                    dataType: "JSON",
                success: function(response)
                {                
                    $(".box_btn_atuacao button").remove();       
                    loadAtuacao(entidade);
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