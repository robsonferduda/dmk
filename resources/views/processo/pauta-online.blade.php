@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Pauta Online</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i>Processos <span> > Pauta Online</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 box-button-xs" >
            <div class="sub-box-button-xs">
                <a  title="Novo" data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
                <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
                <a title="Pauta Online" href="{{ url('processos/pauta/online') }}" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-globe fa-lg"></i> Pauta Online</a>
            </div>
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="well" style="margin-left: 1px; margin-right: 1px; border-radius: 10px; background: #f5f5f5;">
                <form action="{{ url('processos/pauta/online') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-2">                                       
                                <label class="" >Prazo Fatal</label>          
                                <input style="width: 100%" class="form-control date-mask" type="text" name="dt_prazo_fatal_pro" id="dt_prazo_fatal_pro" placeholder="___/___/____" value="{{ !empty($prazo_fatal) ? date('d/m/Y', strtotime($prazo_fatal)) : '' }}" > 
                            </section>
                            <section class="col col-md-4">                                       
                                <label class="" >Responsável</label>          
                                <select id="cd_responsavel_pro" name="cd_responsavel_pro" class="select2">
                                    <option selected value="">Selecione um responsável</option>
                                    @foreach($responsaveis as $usuario)
                                        <option value="{{ $usuario->id }}" {{ ($responsavel == $usuario->id or Auth::user()->id == $usuario->id) ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                    @endforeach
                                </select> 
                            </section>
                            <section class="col col-md-4"> 
                                <button class="btn btn-primary" style="width: 20%; margin-top: 22px;"  type="submit"><i class="fa fa-search"></i> Buscar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    @forelse($processos as $key => $processo)
                        <div class="well box-acompanhamento" style="padding: 10px 15px; border: none; border-radius: 10px; background: white; display: block;">
                            <div class="row box-processo">
                                <div class="hidden-xs hidden-sm hidden-md col-lg-12 box-content">
                                    <h6 style="margin: 0px; font-size: 13px;">{{ $processo->nu_processo_pro ? $processo->nu_processo_pro : ' '}}  
                                        <strong>
                                            <span style="background-color: {{ $processo->status ? $processo->status->ds_color_stp : '' }}" class="label label-default pull-right">{{ $processo->status ? $processo->status->nm_status_processo_conta_stp : '' }}</span>
                                        </strong>
                                    </h6>
                                </div>
                                <div class="col-xs-12 col-sm-8 col-md-8 hidden-lg box-content">
                                    <h6 style="margin: 0px; font-size: 13px;"><strong>Aceito pelo correspondente</strong></h6>
                                </div>
                                <div class="col-md-4 box-content">
                                    <h6><strong>Documento de Representação</strong>: 
                                        @if($processo->fl_documento_representacao_pro == 'S')
                                            <strong style="color: #739e73;">Protocolado</strong>
                                        @else
                                            <strong style="color: #a90329;">Pendente</strong>
                                        @endif
                                    </h6>
                                    <h6><strong>Correspondente</strong>: {{ ($processo->correspondente and $processo->correspondente->contaCorrespondente) ? $processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  : '' }}</h6>
                                    <h6><strong>Responsável</strong>: {{ $processo->responsavel ? $processo->responsavel->name : ''}}</h6>
                                    <h6><strong>Prazo Fatal </strong>: 
                                        {{ $processo->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : ' '}} 
                                        {{ $processo->hr_audiencia_pro ? date('H:i', strtotime($processo->hr_audiencia_pro)) : ' '}}
                                    </h6>
                                    <h6><strong>Parte Adversa</strong>: {{ $processo->nm_autor_pro ? $processo->nm_autor_pro  : ' '}}</h6>
                                    <h6><strong>Réu</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                </div>
                                <div class="col-md-4 box-content">
                                    <h6><strong>Comarca</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                    <h6><strong>Serviço</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                    <h6><strong>Cliente</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                    <h6><strong>Foro</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                    <h6><strong>Tipo de Processo</strong>: {{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</h6>
                                </div>
                                <div class="col-md-4 box-content">
                                    <h6><strong>Dados Audiencistas</strong>
                                        <p><strong>Advogado</strong></p>
                                        {!! $processo->nm_advogado_pro ? $processo->nm_advogado_pro  : '<span class="text-danger">Não informado</span>' !!}
                                        <p><strong>Preposto</strong></p>
                                        {!! $processo->nm_preposto_pro ? $processo->nm_preposto_pro  : '<span class="text-danger">Não informado</span>' !!}
                                    </h6>
                                </div>
                                <div class="col-md-12 box-content">
                                    <h6><strong>Observação</strong>: {{ $processo->ds_observacao_pro ? $processo->ds_observacao_pro : ' '}}</h6>
                                </div>
                            </div>
                        </div>
                    @empty
                        <h5 class="center">Nenhum dado para ser exibido</h5>    
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

    });

</script>
@endsection