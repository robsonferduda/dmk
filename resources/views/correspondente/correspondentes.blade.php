@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Listar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <button class="btn btn-success pull-right header-btn" data-toggle="modal" data-target="#modalNovoCorrespondente"><i class="fa fa-plus"></i> Novo</button>   
            <button class="btn btn-default pull-right header-btn" data-toggle="modal" data-target="#modalConviteCorrespondente"><i class="fa fa-send"></i> Enviar Convite</button> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">   
                <form action="{{ url('correspondente/buscar') }}" class="form-inline" method="GET" role="search">             
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-2">                                       
                                <label class="label label-black" >Estado</label>          
                                <select  id="pai_cidade_atuacao" name="cd_estado_est" class="select2 estado">
                                    <option selected value="">Estado</option>
                                    @foreach(App\Estado::all() as $estado) 
                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                    @endforeach
                                </select>
                            </section>
                            <section class="col col-md-3">
                                <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                <label class="label label-black" >Cidade de Atuação</label>
                                <a href="#" rel="popover-hover" data-placement="top" data-original-title="Cidades de Atuação" data-content="A busca considera as cidades de atuação do correspondente, independente dela ser a comarca de origem. Para ver todas as comarcas de atuação, visualize o cadastro completo.">
                                <i class="fa fa-question-circle text-primary"></i>
                                </a>          
                                <select id="cidade" name="cd_cidade_cde" class="select2 pai_cidade_atuacao">
                                    <option selected value="">Selecione uma cidade</option>
                                </select> 
                            </section> 
                            <section class="col col-md-2">
                                <label class="label label-black" >Categoria</label>
                                <select id="cidade" name="cd_categoria_correspondente_cac" class="select2">
                                    <option selected value="">Selecione</option>
                                    @foreach(App\CategoriaCorrespondente::where('cd_conta_con',Auth::user()->cd_conta_con)->get() as $categoria) 
                                        <option value="{{ $categoria->cd_categoria_correspondente_cac }}">{{ $categoria->dc_categoria_correspondente_cac }}</option>
                                    @endforeach
                                </select> 
                            </section>
                            <section class="col col-md-2">
                                <label class="label label-black">CPF/CNPJ</label>
                                <input type="text" style="width: 100%;" name="identificacao" class="form-control" id="Nome" placeholder="CPF/CNPJ">
                            </section>
                            <section class="col col-md-2">
                                <label class="label label-black" >Nome</label><br>
                                <input type="text" style="width: 100%;" name="nome" class="form-control" id="Nome" placeholder="Nome">
                            </section>
                            <section class="col col-md-1">
                                <label class="label" >Buscar</label>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> </button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Correspondentes</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if(isset($correspondetes))
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>   
                                    <th style="">Categoria</th> 
                                    <th style="">Comarca de Origem</th> 
                                    <th style="">CPF/CNPJ</th>
                                    <th style="">Nome</th>
                                    <th style="" class="center">Email</th>                                  
                                    <th style="width:100px;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($correspondetes as $correspondente)
                                    <tr>
                                        <td>
                                            @if($correspondente->categoria)
                                            <span class="label label-primary" style="background-color: {{ $correspondente->categoria->color_cac }}">{{ $correspondente->categoria->dc_categoria_correspondente_cac }}</span>
                                            @else
                                                <span class="label label-default">Não informado</span>
                                            @endif
                                        </td>
                                        <td>{!! ($correspondente->entidade->origem) ? $correspondente->entidade->origem->cidade->nm_cidade_cde : '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td>{!! ($correspondente->entidade->identificacao) ? $correspondente->entidade->identificacao->nu_identificacao_ide : '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td>
                                            {{ $correspondente->nm_conta_correspondente_ccr }}
                                        </td>
                                        <td>{!! ($correspondente->correspondente->entidade->usuario) ? $correspondente->correspondente->entidade->usuario->email: '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td class="center">
                                            <div>
                                                <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('correspondente/detalhes/'.$correspondente->cd_correspondente_cor) }}"><i class="fa fa-file-text-o"></i> </a>
                                                <a title="Editar" class="btn btn-primary btn-xs" href="{{ url('correspondente/ficha/'.$correspondente->cd_correspondente_cor) }}"><i class="fa fa-edit"></i> </a>
                                                <div class="dropdown" style="display: inline;">
                                                    <a href="javascript:void(0);" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                                                    <ul class="dropdown-menu">
                                                        <li><a title="Despesas" class="" href="{{ url('correspondente/despesas/'.$correspondente->cd_correspondente_cor) }}"><i class="fa fa-dollar"></i> Despesas</a></li>
                                                        <li><a title="Honorários" class=""  href="{{ url('correspondente/honorarios/'.$correspondente->cd_correspondente_cor) }}"><i class="fa fa-money"></i> Honorários</a></li>
                                                        <li><a title="Enviar Notificação" href="{{ url('correspondente/notificacao/'.$correspondente->cd_correspondente_cor) }}"><i class="fa fa-send"></i> Enviar Notificação</a></li>
                                                        <li><a title="Excluir" class="remover_registro" data-url="{{ url('correspondente/excluir/'.$correspondente->cd_conta_correspondente_ccr) }}" data-id="{{ $correspondente->cd_conta_correspondente_ccr }}"><i class="fa fa-trash"></i> Excluir</a> </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach                                                           
                            </tbody>
                        </table>
                        @else
                            <h5 class="center marginTop20"><i class="fa fa-info-circle"></i> Selecione os termos da sua busca e clique em <strong>Buscar</strong></h5>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalNovoCorrespondente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-fw fa fa-plus"></i> Novo Correspondente</h4>
            </div>            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">                        
                        {!! Form::open(['id' => 'frm-add-conta', 'url' => 'correspondente/cadastro/conta', 'id' => 'frmAddCorrespondente', 'class' => 'smart-form client-form']) !!}
                        <div class="well" style="margin: 0px 15px; padding: 5px;">
                            <p>
                                <strong class="text-danger">Atenção!</strong><br/>
                                Ao realizar o cadastro, o correpondente é cadastrado e inserido automaticamente na sua lista de correspondentes.
                                O email informado recebe uma mensagem com as informações de cadastro e o endereço para acessar o sistema atualizar seus dados pessoais.
                            </p>
                        </div>
                            <fieldset>
                                    <h5 style="margin-bottom: 10px;"><strong>Dados do Correspondente</strong></h5>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input type="text" name="nm_razao_social_con" id="nm_razao_social_con" placeholder="Nome" value="{{ old('nm_razao_social_con') }}">
                                        <b class="tooltip tooltip-bottom-right">Nome Completo</b> </label>
                                    </section>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-envelope"></i>
                                        <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                                        <b class="tooltip tooltip-bottom-right">Email do Correspondente</b> </label>
                                    </section>
                                    <section class="center"> 
                                        <p>O sistema gera uma senha aleatória e envia automaticamente para os correspondentes</p>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Cadastrar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-fw fa fa-times"></i>  Cancelar</button>                                    
                                </footer>
                        {!! Form::close() !!} 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalConviteCorrespondente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-fw fa fa-send"></i> Enviar Convite para Cadastro</h4>
            </div>            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                        {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/convidar', 'class' => 'form-inline']) !!}
                            <p style="font-size: 14px;">
                                Ao enviar o convite o correspondente receberá uma mensagem, no email informado abaixo, para acessar o sistema e realizar seu cadastro.
                            </p>
                            <span>Informe o email do correspondente para enviar o convite</span>
                            <div style="margin-top: 8px;">
                                <div class="form-group" style="width: 100%;">
                                    <input type="text" class="form-control" style="width: 60%;" name="email" id="email" placeholder="Email" value="{{old('email')}}">
                                </div>                        
                            </div>
                            <div class="center marginTop20">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-fw fa fa-times"></i>  Cancelar</button>
                                <button type="submit" id="btnSalvarTelefone" class="btn btn-success"><i class="fa-fw fa fa-send"></i> Enviar</button>
                            </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="modal_confirma_correspondente" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> <strong> Adicionar Correspondente</strong></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                        {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/adicionar', 'class' => 'form-inline']) !!}
                            <p style="font-size: 14px;">
                                Essa operação irá adicionar o correspondentes a sua lista de correspondentes.
                                Após adicionar o correspondente você pode adicionar os valores referentes aos serviços prestados, por comarca.
                            </p>
                            <h6>Confirma a inclusão na sua lista de Correspondentes?</h6>
                            <input type="hidden" name="id" id="id_correspondente">
                            <input type="hidden" name="url" id="url">
                            <div class="msg_retorno"></div>

                            <div class="center marginTop20">
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="modalDadosCorrespondente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-fw fa fa-legal"></i> Dados do Correspondente</h4>
            </div>            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center data-modal-correspondente-loading">
                        <h2><i class="fa fa-gear fa-spin"></i> Aguarde, buscando dados...</h2>
                    </div>
                    <div class="col-md-12 data-modal-correspondente" style="display: none">
                        <label id="nm_correspondente"><strong>Nome: </strong><span></span></label><br/>
                        <label id="tipo_correpondente"><strong>Tipo: </strong><span></span></label><br/>
                        <label id="identificacao_correpondente"><strong>CPF/CNPJ: </strong><span></span></label><br/>
                        <label id="email_correpondente"><strong>Email: </strong><span></span></label><br/>
                        <label id="fone_correpondente"><strong>Telefone: </strong><span></span></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-fw fa fa-times"></i>  Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        $('.modal_dados_correspondente').click(function () {
            
            var id = $(this).data("id");

            $.ajax({
                
                url: '../correspondente/dados/'+id,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function(){
                    $("#label > span").empty(); 
                    $('#modalDadosCorrespondente').modal('show');                           
                },
                success: function(response){      

                    $(".data-modal-correspondente-loading").css('display','none');
                    $(".data-modal-correspondente").css('display','block');
                    $("#nm_correspondente span").append(response.dados.nm_razao_social_con);
                                                      
                },
                error: function(response)
                {
                    $(".data-modal-correspondente-loading").css('display','none');
                    alert("Erro ao processar requisição");
                }
            });
        }); 

        $(function() {

            $("#frmAddCorrespondente").validate({
                rules : {
                    nm_razao_social_con : {
                        required : true
                    },
                    email : {
                        required : true
                    }

                },

                messages : {
                    nm_razao_social_con : {
                        required : 'Campo nome é obrigatório'
                    },
                    email : {
                        required : 'Campo email é obrigatório'
                    }
                },
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        });  

        var buscaCidade = function(estado,target){

            if(estado != ''){

                $.ajax(
                    {
                        url: '../cidades-por-estado/'+estado,
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