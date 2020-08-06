@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('contatos') }}">Agenda de Contatos</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-book"></i> Agenda de Contatos <span>> Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('contatos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-book fa-lg"></i> Listar Contatos</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-book"></i> </span>
                    <h2>Contatos </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-add-contato', 'url' => ['contatos',$contato->cd_contato_cot], 'class' => 'smart-form', 'method' => 'PUT']) !!}
                        <input type="hidden" name="telefones" id="telefones">
                        <input type="hidden" name="emails" id="emails">
                        <input type="hidden" name="entidade" id="entidade" value="{{ $contato->entidade->cd_entidade_ete }}">
                        <div class="row">
                            <div style="padding: 5px 20px;">
                                <header>
                                    Dados do Contato 
                                </header>

                                <fieldset>

                                     <section>
                                        <div class="onoffswitch-container" style="margin-left: 0px;">
                                            <span class="onoffswitch-title">Contato com Cliente?</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox"  value="N" id="contato_cliente">
                                                <label class="onoffswitch-label" for="contato_cliente"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>   
                                        <div class="row">
                                            <section class="col col-4 box-cliente">
                                                 <input type="hidden" name="cd_cliente_cli" value="{{old('cd_cliente_cli') ? old('cd_cliente_cli') : (!empty($contato->entidadeCliente->cliente->cd_cliente_cli)) ?  $contato->entidadeCliente->cliente->cd_cliente_cli : '' }}" >
                                                <label class="label">Cliente<span class="text-danger">*</span></label>
                                                <label class="input">
                                                    <input required name="nm_cliente_cli" value="{{old('nm_cliente_cli') ? old('nm_cliente_cli') : $nome }}" class="form-control ui-autocomplete-input" placeholder="Digite 3 caracteres para busca" type="text" id="client" autocomplete="off">
                                                </label> 
                                            </section>
                                        </div>
                                                                       
                                    </section>

                                    <div class="row">
                                        <section class="col col-4">
                                            <label class="label" >Tipo de Contato <span class="text-danger">Campo obrigatório</span></label>          
                                            <select  id="cd_tipo_contato_tct" name="cd_tipo_contato_tct" class="select2" required="required">
                                                <option value="0">Selecione um tipo</option>
                                                @foreach($tipos as $tipo) 
                                                    <option {!! ($contato->cd_tipo_contato_tct == $tipo->cd_tipo_contato_tct) ? 'selected' : ''  !!} value="{{ $tipo->cd_tipo_contato_tct }}">{{ $tipo->nm_tipo_contato_tct}}</option>
                                                @endforeach
                                            </select> 
                                        </section>
                                        <section class="col col-8">
                                            <label class="label">Nome<span class="text-danger"> Campo obrigatório</span></label>
                                            <label class="input">
                                                <input type="text" name="nm_contato_cot" id="nm_contato_cot" value="{{ $contato->nm_contato_cot }}" placeholder="Nome">
                                            </label>
                                        </section>
                                    </div>
                                    <div class="row">                                        
                                        <section class="col col-2">
                                            <label class="label">CEP</label>
                                            <label class="input">
                                                <input type="text" class="cep" name="nu_cep_ede" id="cep" placeholder="00000-000" value="{{old('nu_cep_ede') ? old('nu_cep_ede') : ($contato->entidade->endereco) ? $contato->entidade->endereco->nu_cep_ede : '' }}">
                                            </label>
                                        </section> 
                                        <section class="col col-sm-8">
                                            <label class="label">Logradouro</label>
                                            <label class="input">
                                                <input type="text" name="dc_logradouro_ede" placeholder="Logradouro" value="{{old('dc_logradouro_ede') ? old('dc_logradouro_ede') : ($contato->entidade->endereco) ? $contato->entidade->endereco->dc_logradouro_ede : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-2">
                                            <label class="label">Nº</label>
                                            <label class="input">
                                                <input type="text" name="nu_numero_ede" placeholder="Nº" value="{{old('nu_numero_ede') ? old('nu_numero_ede') : ($contato->entidade->endereco) ? $contato->entidade->endereco->nu_numero_ede : '' }}">
                                            </label>
                                        </section>
                                    </div>

                                     <div class="row">
                                        <section class="col col-6">
                                            <label class="label">Bairro</label>
                                            <label class="input">
                                                <input type="text" name="nm_bairro_ede" placeholder="Bairro" value="{{old('nm_bairro_ede') ? old('nm_bairro_ede') : ($contato->entidade->endereco) ? $contato->entidade->endereco->nm_bairro_ede : '' }}">
                                            </label>
                                        </section>
                                        <section class="col col-6">
                                            <label class="label">Complemento</label>
                                            <label class="input">
                                                <input type="text" name="dc_complemento_ede" placeholder="Complemento" value="{{old('dc_complemento_ede') ? old('dc_complemento_ede') : ($contato->entidade->endereco) ? $contato->entidade->endereco->dc_complemento_ede : '' }}">
                                            </label>
                                        </section>                                                                    
                                    </div> 

                                    <div class="row">                    
                                        <section class="col col-6">                                       
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione um estado</option>
                                                @foreach(App\Estado::all() as $estado) 
                                                    <option {{ ($contato->entidade->endereco and $contato->entidade->endereco->cidade) ? (old('cd_estado_est', $contato->entidade->endereco->cidade->cd_estado_est) == $estado->cd_estado_est ) ? 'selected' : '' : ''  }} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach

                                            </select> 
                                        </section>
                                        <section class="col col-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                           <label class="label" >Cidade</label>          
                                            <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div>
                                </fieldset>
                            </div>
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
                                                    @foreach($contato->entidade->fone()->get() as $fone)
                                                        <tr>
                                                            <td class="center">{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                            <td>{{ $fone->nu_fone_fon }}</td>
                                                            <td class="center">
                                                                <a class="excluirFoneBase" style="cursor: pointer;" data-codigo="{{ $fone->cd_fone_fon }}"><i class="fa fa-trash"></i> Excluir</a>
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
                                                @foreach($contato->entidade->enderecoEletronico()->get() as $email)
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
                                <div class="col col-sm-12">
                                    <header>
                                        <i class="fa  fa-file-text-o"></i> Observações 
                                    </header>
                                    <fieldset>
                                        <div class="row"> 
                                            <section class="col col-sm-12">
                                            <label class="input">
                                                <textarea class="form-control" id="observacao" rows="4" name="dc_observacao_cot" value="{{(old('dc_observacao_cot')  ? old('dc_observacao_cot') : $contato->dc_observacao_cot)}}" >{{ old('dc_observacao_cot') ? old('dc_observacao_cot') :  $contato->dc_observacao_cot }}</textarea>
                                            </label>
                                            </section> 
                                        </div>
                                    </fieldset>
                                </div>           
                            </div>

                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cadastrar</button>
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

        var path = "{{ url('autocompleteCliente') }}";

        
        if($("input[name='cd_cliente_cli']").val() != ''){
            $('.box-cliente').css('display','block');
            $('#contato_cliente').prop('checked',true);
        }else{
            $('.box-cliente').css('display','none');
        }

        

        $("#contato_cliente").click(function(){

            flag = $('#contato_cliente').is(':checked');
            if(flag){
                $('.box-cliente').css('display','block');
                $('#nm_cliente_cli').focus();
            }
            else{
                $("input[name='cd_cliente_cli']").val('');
                $("input[name='nm_cliente_cli']").val('');

                $('.box-cliente').css('display','none');
            }
        });

        $( "#client" ).autocomplete({
          source: path,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);
                
          },
          open: function(event, ui){
            
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

    $(function() {

        $("#frm-add-contato").validate({
            rules : {
                nm_contato_cot : {
                    required : true
                },
                cd_tipo_contato_tct : {
                    required : true,
                    min: function () { return $('#cd_tipo_contato_tct').val()}
                }
            },

            messages : {
                nm_contato_cot : {
                    required : 'Preencha o campo para continuar'
                },
                cd_tipo_contato_tct : {
                    required : 'Preencha o campo para continuar'
                },
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
    });

</script>
@endsection