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
            <a href="{{ url('notificacao/grupo/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus"></i> Novo Grupo</a>   
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
                        <h6>{{ $grupo->ds_grupo_grn }}<span class="label bg-color-darken txt-color-white label-grupo-notificacao">{{ $grupo->tipoProcesso->nm_tipo_processo_tpo }}</span></h6>
                        @if(count($grupo->emails))
                            <strong>Endereços de Notificação</strong>:
                            @foreach($grupo->emails as $key => $email)
                                <div class="d-inline"><span>{{ $email->ds_email_egn }}</span><i data-id="{{ $email->cd_email_grupo_notificacao_egn }}" class="fa fa-trash text-danger icon-excluir-email remover_cliente"></i></div>
                                @if($key < count($grupo->emails)-1)
                                    ,
                                @endif
                            @endforeach
                        @else
                            <label class="text-danger"><i class="fa fa-info-circle"></i> Atenção! Nenhum email cadastrado</label>
                        @endif
                        <p class="mt-5"><a href="" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Adicionar Email</a></p>
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

        $(".remover_cliente").click(function(){

            var id  = $(this).data('id');
            var url = $("#modal_excluir_email #frm_excluir_email").attr('action');
            var url_final = url+'/'+id;

            $("#modal_excluir_email #frm_excluir_email").attr('action', url_final);
            $("#modal_excluir_email").modal('show');
        });

    });
</script>
@endsection