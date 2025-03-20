@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Editar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Editar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>      
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Editar Cadastro de Cliente </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-add-cliente', 'url' => ['clientes', $cliente->cd_cliente_cli], 'class' => 'smart-form', 'method' => 'PUT']) !!}
                        <input type="hidden" name="telefones" id="telefones">
                        <input type="hidden" name="emails" id="emails">
                        <input type="hidden" name="entidade" id="entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                        <div class="row">
                            <section class="col col-8">
                                <header>
                                    Dados Básicos
                                </header>

                                <fieldset>
                                    <section>
                                        <div class="inline-group">
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="2" {{ ($cliente->cd_tipo_pessoa_tpp == 2) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Jurídica</label>
                                            <label class="radio">
                                                <input type="radio" class="tipo-pessoa" name="cd_tipo_pessoa_tpp" value="1" {{ ($cliente->cd_tipo_pessoa_tpp == 1) ? 'checked="checked"' : '' }}>
                                                    <i></i>Pessoa Física</label>
                                        </div>
                                    </section>
                                    <div class="row">
                                        <section class="col col-3 box-pessoa-juridica">
                                            <label class="label">CNPJ</label>
                                            <label class="input">
                                                <input type="text" name="cnpj" id="cnpj" class="cnpj" placeholder="00.000.000/000-00" value="{{ ($cliente->entidade->cnpj) ? $cliente->entidade->cnpj->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-3 box-pessoa-juridica">
                                            <label class="label">Data de Fundação</label>
                                            <label class="input">
                                                <input type="text" name="data_fundacao_cli" class="data_fundacao" placeholder="__/__/____" value="{{ ($cliente->dt_inicial_cli) ? date('d/m/Y', strtotime($cliente->dt_inicial_cli)) : old('data_fundacao') }}">
                                            </label>
                                        </section>

                                        <section class="col col-3 box-pessoa-fisica">
                                            <label class="label">CPF</label>
                                            <label class="input">
                                                <input type="text" name="cpf" id="cpf" class="cpf" placeholder="000.000.000-000" value="{{ ($cliente->entidade->cpf) ? $cliente->entidade->cpf->nu_identificacao_ide : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-3 box-pessoa-fisica">
                                            <label class="label">Data de Nascimento</label>
                                            <label class="input">
                                                <input type="text" name="data_nascimento_cli" class="data_nascimento" placeholder="__/__/____" value="{{ ($cliente->dt_inicial_cli) ? date('d/m/Y', strtotime($cliente->dt_inicial_cli)) : old('data_nascimento') }}">
                                            </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label">Razão Social <span class="text-danger">(Obrigatório)</span></label>
                                            <label class="input">
                                                <input type="text" name="nm_razao_social_cli" placeholder="Razão Social" value="{{ old('nm_razao_social_con') ? old('nm_razao_social_con') : $cliente->nm_razao_social_cli }}"> 
                                            </label>
                                        </section>   
                                        <section class="col col-6">
                                            <label class="label label-tipo-pessoa">Nome Fantasia</label>
                                            <label class="input">
                                                <input type="text" name="nm_fantasia_cli" id="nm_fantasia_cli" value="{{ old('nm_fantasia_cli') ? old('nm_fantasia_cli') : $cliente->nm_fantasia_cli }}" placeholder="Nome Fantasia">
                                            </label>
                                        </section> 
                                        <section class="col col-6">
                                            <label class="label">Site (Digite a URL)</label>
                                            <label class="input">
                                                <input type="text" name="ds_site_cli" id="ds_site_cli" value="{{ ($cliente->ds_site_cli) ? $cliente->ds_site_cli : old('ds_site_cli') }}">
                                            </label>
                                        </section>                                  
                                    </div>                        
                                    <div class="row">

                                        <section class="col col-3">
                                            <label class="label">N º OAB</label>
                                            <label class="input">
                                                <input type="text" name="oab" placeholder="OAB" value="{{ ( old('oab') ? old('oab') : $cliente->entidade->oab ) ? $cliente->entidade->oab->nu_identificacao_ide : '' }}">
                                                </label>
                                        </section> 

                                        <div class="box-pessoa-fisica">
                                            <section class="col col-3">
                                                <label class="label">RG</label>
                                                <label class="input">
                                                    <input type="text" name="rg" placeholder="RG" value="{{ ( old('rg') ? old('rg') : $cliente->entidade->rg ) ? $cliente->entidade->rg->nu_identificacao_ide : ''}}">
                                                    </label>
                                            </section> 
                                        </div>

                                        <div class="box-pessoa-juridica">
                                            <section class="col col-3">
                                                <label class="label">Inscrição Municipal</label>
                                                <label class="input">
                                                    <input type="text" name="inscricao_municipal_cli" placeholder="Inscrição Municipal">
                                                </label>
                                            </section>
                                            <section class="col col-3">
                                                <label class="label">Inscrição Estadual</label>
                                                <label class="input">
                                                    <input type="text" name="inscricao_estadual_cli" placeholder="Inscrição Estadual" value="{{ old('inscricao_estadual_cli') ? old('inscricao_estadual_cli') : $cliente->inscricao_estadual_cli }}">
                                                </label>
                                            </section>
                                        </div>              
                                    </div>

                                    <section>
                                        <div class="onoffswitch-container" style="margin-left: 0px;">
                                            <span class="onoffswitch-title">Cliente Ativo</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" name="fl_ativo_cli" value="S" id="fl_ativo_cli" {{ ($cliente->fl_ativo_cli == 'S') ? 'checked="checked"' : '' }}>
                                                <label class="onoffswitch-label" for="fl_ativo_cli"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>                                        
                                    </section>

                                    <section>
                                        <div class="onoffswitch-container" style="margin-left: 0px;">
                                            <span class="onoffswitch-title">Pagamento Com Nota Fiscal</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" name="fl_nota_fiscal_cli" value="S" id="fl_nota_fiscal_cli" {{ ($cliente->fl_nota_fiscal_cli == 'S') ? 'checked="checked"' : '' }}>
                                                <label class="onoffswitch-label" for="fl_nota_fiscal_cli"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>                                        
                                    </section>

                                    <div class="row">
                                        <section class="col col-3 box-desconto">
                                            <label class="label">Percentual de Desconto</label>
                                            <label class="input" style="width: 50%">
                                                <input type="text" id="taxa_imposto_cli" name="taxa_imposto_cli" value="{{ old('taxa_imposto_cli') ? old('taxa_imposto_cli') : $cliente->taxa_imposto_cli }}">
                                            </label>    
                                        </section>
                                    </div>
                                </fieldset>
                            </section>

                            <section class="col col-4">
                                <header>
                                    <i class="fa fa-dollar"></i> Despesas Reembolsáveis
                                    <a href="{{ url('configuracoes/tipos-de-despesa') }}" style="padding: 1px 8px;" ><i class="fa fa-plus-circle"></i> Novo </a>
                                </header>

                                <fieldset>
                                    <section>
                                        <label class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> Selecione as despesas reembolsáveis do cliente</label>
                                        <div class="row">
                                            <div class="col col-12">
                                                @foreach($despesas as $despesa)
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="despesas[]" value="{{ $despesa->cd_tipo_despesa_tds }}">
                                                        <i></i>{{ $despesa->nm_tipo_despesa_tds }} 
                                                    </label>
                                                @endforeach
                                                @foreach($despesas_selecionadas as $despesa)
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="remover[]" checked="checked" value="{{ $despesa->cd_tipo_despesa_tds }}">
                                                        <i></i>{{ $despesa->nm_tipo_despesa_tds }} 
                                                    </label>
                                                @endforeach
                                            </div> 
                                        </div>
                                    </section>
                                </fieldset>
                            </section>
                        </div>

                        <div class="row" style="padding: 5px 20px;">
                            <header>
                                <i class="fa fa-lock"></i> Dados de Acesso 
                            </header>
                            <fieldset>
                                <div class="row">

                                    @if($cliente->entidade->usuario)
                                        <section class="col col-md-12"> 
                                            <p><strong>Usuário</strong>: {{ ($cliente->entidade->usuario->email) ? $cliente->entidade->usuario->email : 'Nenhum usuário cadastrado' }}</p>
                                        </section>
                                    @endif

                                    <section class="col col-12">
                                        <label class="label">Usuário <span class="text-info">Digite um email válido</span></label>
                                        <label class="input">
                                            <input type="text" name="email_user" id="email_user" value="{{ $cliente->entidade->usuario ? $cliente->entidade->usuario->email : '' }}">
                                        </label>
                                    </section> 
                                    
                                    <div class="onoffswitch-container" style="margin-left: 0px; margin-top: 29px;">
                                            <span class="onoffswitch-title">Notificar Cliente</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" name="fl_notificar_cliente" value="S" id="fl_notificar_cliente">
                                                <label class="onoffswitch-label" for="fl_notificar_cliente"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                    </div>                                     
                                </div>
                            </fieldset>
                        </div>

                            <div class="row" style="padding: 5px 20px;">
        
                                <header>
                                    <i class="fa fa-map-marker"></i> Endereço 
                                </header>

                                <fieldset>

                                    <div class="row">
                                        <section class="col col-2">
                                            <label class="label">CEP</label>
                                            <label class="input">
                                                <input type="text" class="cep" name="nu_cep_ede" id="cep" placeholder="00000-000" value="{{ ( old('nu_cep_ede') ? old('nu_cep_ede') : $cliente->entidade->endereco) ? $cliente->entidade->endereco->nu_cep_ede : '' }}">
                                            </label>
                                        </section> 
                                        <section class="col col-sm-8">
                                            <label class="label">Logradouro</label>
                                            <label class="input">
                                                <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{ (old('dc_logradouro_ede') ? old('dc_logradouro_ede') : $cliente->entidade->endereco) ? $cliente->entidade->endereco->dc_logradouro_ede : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-2">
                                            <label class="label">Nº</label>
                                            <label class="input">
                                                <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{ (old('nu_numero_ede') ? old('nu_numero_ede') : $cliente->entidade->endereco) ? $cliente->entidade->endereco->nu_numero_ede : '' }}">
                                            </label>
                                        </section>
                                    </div>

                                     <div class="row">
                                        <section class="col col-6">
                                            <label class="label">Bairro</label>
                                            <label class="input">
                                                <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{ ( old('nm_bairro_ede') ? old('nm_bairro_ede') : $cliente->entidade->endereco) ? $cliente->entidade->endereco->nm_bairro_ede : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label">Complemento</label>
                                            <label class="input">
                                                <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{ (old('nm_bairro_ede') ? old('dc_complemento_ede') : $cliente->entidade->endereco) ? $cliente->entidade->endereco->dc_complemento_ede : '' }}">
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
                                                <label class="select">   
                                                    <select name="cd_tipo_fone_tfo" id="cd_tipo_fone_tfo">
                                                        <option value="0">Tipo</option>
                                                        @foreach(\App\TipoFone::all() as $tipoFone)
                                                            <option {!! (old('cd_tipo_fone_tfo') == $tipoFone->cd_tipo_fone_tfo ? 'selected' : '') !!}  value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                                        @endforeach   
                                                    </select><i></i> 
                                                </label>
                                            </section> 
                                            <section class="col col-1">
                                                <button type="button" id="btnSalvarTelefone" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Novo</button>
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
                                                    @foreach($cliente->entidade->fone()->get()->sortBy('cd_fone_fon') as $fone)
                                                        <tr>
                                                            <td data-tipo="{{ $fone->tipo()->first()->cd_tipo_fone_tfo }}" class="center">{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                            <td data-fone="{{ $fone->nu_fone_fon }}">{{ $fone->nu_fone_fon }}</td>
                                                            <td class="center">
                                                                <a title="Editar" class="editarFone btnFoneEditar" data-codigo="{{ $fone->cd_fone_fon }}" data-edit="S"><i class="fa fa-edit"></i> </a>
                                                                <a title="Excluir" class="excluirFoneBase btnFoneExcluir" data-codigo="{{ $fone->cd_fone_fon }}"><i class="fa fa-trash"></i> </a>
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
                                                @foreach($cliente->entidade->enderecoEletronico()->get() as $email)
                                                    <tr>
                                                        <td class="center">{{ $email->tipo->dc_tipo_endereco_eletronico_tee }}</td>
                                                        <td>{{ $email->dc_endereco_eletronico_ede }} </td>                                                        
                                                        <td class="center">
                                                            <a title="Editar Email" class="editarEmail btnEmailEditar" data-codigo="{{ $email->cd_endereco_eletronico_ele }}" data-edit="S"><i class="fa fa-edit"></i> </a>
                                                            <a title="Excluir Email" class="excluirEmailBase btnEmailExcluir" data-codigo="{{ $email->cd_endereco_eletronico_ele }}"><i class="fa fa-trash"></i> </a>
                                                        </td>
                                                    </tr>                                                  
                                                @endforeach  
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col col-sm-12">
                                    <header>
                                        <i class="fa  fa-file-text-o"></i> Observações 
                                    </header>
                                    <fieldset>
                                        <div class="row"> 
                                            <section class="col col-sm-12">
                                            <label class="input">
                                                <textarea class="form-control" rows="4" name="observacao_cli" id="observacao" value="{{ old('observacao_cli') }}" >{{ ( old('observacao_cli') ? old('observacao_cli') : $cliente->observacao_cli) ? $cliente->observacao_cli : '' }}</textarea>
                                            </label>
                                            </section> 
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Atualizar</button>
                                <a href="{{ url('clientes') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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

        flag = $('#fl_nota_fiscal_cli').is(':checked');
            if(flag){
                $('.box-desconto').css('display','block');
                $('#taxa_imposto_cli').focus();
            }
            else{
                $('.box-desconto').css('display','none');
            }

        $("#fl_nota_fiscal_cli").click(function(){

            flag = $('#fl_nota_fiscal_cli').is(':checked');
            if(flag){
                $('.box-desconto').css('display','block');
                $('#taxa_imposto_cli').focus();
            }
            else{
                $('.box-desconto').css('display','none');
            }
        });

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

            $("#frm-add-cliente").validate({
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