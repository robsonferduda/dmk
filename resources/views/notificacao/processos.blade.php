@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Notificações</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i> Processos <span> > Notificações</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo"> 
            <a class="btn btn-primary btn-novo-grupo pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo Grupo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">  
                <h5 class="margin-top-0"><i class="fa fa-send"></i> Grupos de Notificação</h5>
                @forelse($grupos as $key => $grupo)
                    <div style="position: relative">
                        <h6 class="nome_grupo_notificacao">
                            {{ $grupo->ds_grupo_grn }}
                            <span class="editar_grupo_notificacao" data-id="{{ $grupo->cd_grupo_notificacao_grn }}" data-tipo="{{ $grupo->cd_tipo_processo_tpo }}" data-nome="{{ $grupo->ds_grupo_grn }}" style="padding: 1px 8px; font-weight: 400; color: #3276b1;"><i class="fa fa-edit"></i> Editar </span>
                            <span class="label bg-color-darken txt-color-white label-grupo-notificacao">{{ $grupo->tipoProcesso->nm_tipo_processo_tpo }}</span>
                        </h6>
                        @if(count($grupo->emails))
                            <strong>Endereços de Notificação</strong>:
                            @foreach($grupo->emails as $key => $email)
                                <div class="d-inline"><span>{{ $email->ds_email_egn }}</span><i data-id="{{ $email->cd_email_grupo_notificacao_egn }}" class="fa fa-trash text-danger icon-excluir-email"></i></div>
                                @if($key < count($grupo->emails)-1)
                                    ,
                                @endif
                            @endforeach
                        @else
                            <label class="text-danger"><i class="fa fa-info-circle"></i> Atenção! Nenhum email cadastrado</label>
                        @endif
                        <p class="mt-5"><span class="btn btn-primary btn-xs btn-add-email-grupo" data-id="{{ $grupo->cd_grupo_notificacao_grn }}"><i class="fa fa-plus"></i> Adicionar Email</span></p>
                    </div>
                    @if($key < count($grupos)-1)
                        <hr/>
                    @endif
                @empty  
                    <h6>Nenhum grupo de notificação cadastrado</h6>
                @endforelse
            </div>
        </article>
    </div>
</div>
<div class="modal fade" id="add_email_grupo" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Adicionar Email ao Grupo
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-grupo', 'url' => 'notificacao/grupo/email', 'class' => 'smart-form']) !!}
                    <fieldset>
                        <input type="hidden" name="id_grupo_email" id="id_grupo_email">
                        <section class="col col-xs-12 col-lg-12"> 
                            <div>
                                <label class="label">Email<span class="text-danger">Obrigatório</span></label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="ds_email_egn" id="ds_email_egn" required>
                                </label>
                            </div>
                        </section>                                
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-grupo"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_grupo" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Novo Grupo
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-grupo', 'url' => 'notificacao/novo-grupo', 'class' => 'smart-form']) !!}
                    <fieldset>
                        <input type="hidden" name="id_grupo" id="id_grupo">
                        <section class="col col-xs-12 col-lg-12"> 
                            <div>
                                <label class="label">Nome do Grupo <span class="text-danger">Obrigatório</span></label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="ds_grupo_grn" id="ds_grupo_grn" required>
                                </label>
                            </div>
                        </section>
                        <section class="col col-xs-12 col-lg-12">                                       
                            <label class="label">Tipo de Processo <span class="text-danger">Obrigatório</span></label>          
                            <label class="select">
                                <select name="cd_tipo_processo_tpo" id="cd_tipo_processo_tpo" required aria-required="true">
                                    <option selected="" value="">Selecione o tipo de processo</option>     
                                    @foreach($tipos as $tipo) 
                                        <option value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                    @endforeach
                                </select><i></i>   
                            </label>
                        </section>                                             
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-grupo"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modal_excluir_email" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> <strong> Excluir Email</strong></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                        <form id="frm_excluir_email" class="form-inline" action="{{ url('notificacao/grupo/email/remover') }}" method="POST">
                            {!! method_field('DELETE') !!}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <h4>Essa operação irá excluir o registro definitivamente.</h4>
                            <h4>Deseja continuar?</h4>
                        
                            <div class="center marginTop20">
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        $(".editar_grupo_notificacao").click(function(){

            var id = $(this).data("id");
            var tipo = $(this).data("tipo");
            var nome = $(this).data("nome");

            $("#id_grupo").val(id);
            $("#ds_grupo_grn").val(nome);
            $("#cd_tipo_processo_tpo").val(tipo);

            $("#add_grupo").modal('show');
        });

        $(".btn-add-email-grupo").click(function(){
            
            var id = $(this).data("id");

            $("#id_grupo_email").val(id);
            $("#add_email_grupo").modal('show');
        });

        $(".btn-novo-grupo").click(function(){
            $("#id_grupo").empty();
            $("#ds_grupo_grn").empty();
            $("#cd_tipo_processo_tpo").val("");

            $("#add_grupo").modal('show');
        });

        /*
        $("#frm-add-grupo").submit(function (e) {
            e.preventDefault();  
            $(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');
            $(this).submit();
        });
        */

        $(".icon-excluir-email").click(function(){

            var id  = $(this).data('id');
            var url = $("#modal_excluir_email #frm_excluir_email").attr('action');
            var url_final = url+'/'+id;

            $("#modal_excluir_email #frm_excluir_email").attr('action', url_final);
            $("#modal_excluir_email").modal('show');
        });

    });
</script>
@endsection