@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondentes') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-list"></i> Listar Correspondentes</a>
            <button class="btn btn-default pull-right header-btn" data-toggle="modal" data-target="#modalConviteCorrespondente"><i class="fa fa-send"></i> Enviar Convite para Cadastro</button>            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('correspondente/todos') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-12"> 
                                <span>BUSCAR NO DIRETÓRIO CENTRAL DE CORRESPONDENTES</span><span class="text-primary"> Selecione um ou mais critérios<span><hr/>
                            </section>
                            <section class="col col-md-4">
                                <label class="label label-black" >Nome</label><br>
                                <input type="text" style="width: 100%;" name="nome" class="form-control" id="Nome" placeholder="Nome">
                            </section>
                             <section class="col col-md-3">
                                <label class="label label-black" >Email</label><br>
                                <input type="text" style="width: 100%;" name="email" class="form-control" id="email" placeholder="Email">
                            </section>
                            <section class="col col-md-3">
                                <label class="label label-black">CPF/CNPJ</label>
                                <input type="text" style="width: 100%;" name="identificacao" class="form-control" id="Nome" placeholder="CPF/CNPJ">
                            </section>
                            <section class="col col-md-1">
                                <label class="label" >Buscar</label>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>

            @if(isset($correspondetes) and count($correspondetes) > 0)
                <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                    <header>
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>Correspondentes</h2>
                    </header>
                    <div>
                        <div class="widget-body no-padding">
                            <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                                <thead>                         
                                    <tr>    
                                        <th style="width: 15%;">CPF/CNPJ</th>                                
                                        <th style="width: 45%;">Nome</th>
                                        <th style="width: 15%;" class="center">Email</th>                                  
                                        <th style="width: 25%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($correspondetes as $correspondente)
                                        <tr>
                                            <td>{{ ($correspondente->entidade->cpf) ? $correspondente->correspondente->entidade->cpf : "Não informado" }}</td>
                                            <td>{{ $correspondente->nm_razao_social_con }}</td>
                                            <td>{{ $correspondente->entidade->usuario->email }}</td>
                                            <td class="center">
                                                <button title="Dados do Correspondente" class="btn btn-default btn-xs modal_dados_correspondente" data-id="{{ $correspondente->cd_conta_con }}"><i class="fa fa-folder"></i> Dados</button>
                                                @if($correspondente->contaCorrespondente()->where('cd_conta_con',1)->first())
                                                    <button title="Dados do Correspondente" class="btn btn-danger btn-xs remover_registro" data-url="{{ url('correspondente/excluir/'.$correspondente->contaCorrespondente()->first()->cd_conta_correspondente_ccr) }}" data-id="{{ $correspondente->contaCorrespondente()->first()->cd_conta_correspondente_ccr}}"><i class="fa fa-times"></i> Remover</button>   
                                                @else
                                                    <button title="Dados do Correspondente" class="btn btn-primary btn-xs adicionar_registro" data-url="{{ url('correspondente/adicionar/'.$correspondente->cd_conta_con) }}" data-id="{{ $correspondente->cd_conta_con }}"><i class="fa fa-plus"></i> Adicionar</button>   

                                                @endif                                            
                                            </td>
                                        </tr>
                                    @endforeach                                                           
                                </tbody>
                            </table>                          
                        </div>
                    </div>
                </div>
            @else
                @if(Session::get('busca_vazia'))
                    <div class="well">
                    
                            <div class="row"> 
                                <section class="col col-md-12"> 
                                    <span><i class="fa fa-info-circle"></i> Nenhum registro encontrado para sua busca, revise seus termos e tente novamente. Caso o correspondente não possua cadastro, utilize a opção abaixo para enviar o convite.</span><hr/>
                                </section>
                                <section class="col col-md-12 center"> 
                                    <button class="btn btn-default" data-toggle="modal" data-target="#modalConviteCorrespondente"><i class="fa fa-send"></i> Enviar Convite para Cadastro</button>
                                </section>
                            </div>
                    
                    </div>
                @endif
                {{ Session::forget('busca_vazia') }}
            @endif
        </article>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalCadastroCorrespondente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-fw fa fa-plus"></i> Cadastrar Correspondente</h4>
            </div>            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/convidar', 'class' => 'form']) !!}
                        <div class="row"> 
                            <section class="col col-md-6"> 
                                <div class="form-group" style="width: 100%;">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{old('email')}}">
                                </div> 
                            </section>
                            <section class="col col-md-6"> 
                                <div class="form-group" style="width: 100%;">
                                    <label>CPF/CNPJ</label>
                                    <input type="text" class="form-control" name="email" id="email" placeholder="CPF/CNPJ" value="{{old('email')}}">
                                </div>  
                            </section>
                        </div>
                        <div class="row marginTop10"> 
                            <section class="col col-md-12"> 
                                <div class="form-group" style="width: 100%;">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" name="email" id="email" placeholder="Nome" value="{{old('email')}}">
                                </div> 
                            </section>
                        </div>
                        <div class="row">    
                            <div class="center marginTop20">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-fw fa fa-times"></i>  Cancelar</button>
                                <button type="submit" id="btnSalvarTelefone" class="btn btn-success"><i class="fa-fw fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
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
                            <span>Informe o email do convidado para enviar o endereço para cadastro</span>
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
                                Essa operação irá adicionar o corresponde da coleção na sua lista de correspondentes.
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
                    <div class="col-md-12">
                        <label id="nm_correspondente"><strong>Nome: </strong><span></span></label><br/>
                        <label id="tipo_correpondente"><strong>Tipo: </strong><span></span></label><br/>
                        <label id="identificacao_correpondente"><strong>CPF/CNPJ: </strong><span></span></label><br/>
                        <label id="email_correpondente"><strong>Email: </strong><span></span></label><br/>
                        <label id="fone_correpondente"><strong>Telefone: </strong><span></span></label><hr>
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
                },
                success: function(response){      

                    $("#nm_correspondente span").append(response.dados.nm_razao_social_con);

                    $('#modalDadosCorrespondente').modal('show');                                   
                },
                error: function(response)
                {
                    alert("Erro ao processar requisição");
                }
            });
        });        

    });
</script>
@endsection