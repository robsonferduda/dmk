@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Painel</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Painel</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>
    <div class="row">
        <article class="col-xs-12 col-sm-4 sortable-grid ui-sortable">
                            
            <div class="jarviswidget">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-archive"></i> </span>
                    <h2>Meus Processos</h2>                
                </header>
                <div role="content">
                    <div class="widget-body">
                        <p class="text-info"><i class="fa fa-exclamation"></i> Atenção para os prazos dos processo</p>
                    </div>  
                </div>
            </div>

        </article>

        <article class="col-xs-12 col-sm-4 sortable-grid ui-sortable">
                            
            <div class="jarviswidget">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-envelope"></i> </span>
                    <h2>Meus Convites</h2>                
                </header>
                <div role="content">
                    <div class="widget-body">
                        <ul class="list-unstyled">
                            @foreach($convites as $convite)
                                <li><i class="fa fa-check-square-o"></i> {{ date('H:i:s d/m/Y', strtotime($convite->created_at)) }} - {{ $convite->conta->nm_razao_social_con }}</li>
                            @endforeach
                        </ul>
                    </div>  
                </div>
            </div>
            
        </article>
        
        <article class="col-xs-12 col-sm-4 sortable-grid ui-sortable">
            <div class="jarviswidget">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-comments text-info"></i> </span>
                    <h2>Últimas Mensagens</h2>              
                </header>
                <div role="content">
                    <div class="widget-body no-padding">
                        <div class="chat-body custom-scroll" style="">
                            <ul>
                                @php
                                    $mensagens_pendentes = (new \App\ProcessoMensagem)->getMensagensPendentesRemetente(session::get('SESSION_CD_CONTA'));
                                @endphp
                                @forelse($mensagens_pendentes as $mensagem)
                                    <li class="message margin-bottom-10">
                                        @if(file_exists('public/img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png'))                                                                           
                                            <img src="{{ asset('img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png') }}" style="width: 50px;" alt="" class="img-circle img-responsive"/>
                                        @else
                                            <img src="{{ asset('img/users/user.png') }}" style="width: 50px;" alt="" class="img-circle img-responsive"/>
                                        @endif 
                                        <div class="message-text">
                                            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($mensagem->cd_processo_pro)) }}" class="username txt-color-blueDark">Processo {{ $mensagem->processo->nu_processo_pro }}</a> 
                                            <span class="font-xs">
                                                Mensagem enviada por {{ $mensagem->entidadeRemetente->nm_razao_social_con }}
                                            </span>
                                            <time class="p-relative d-block margin-top-5"> {{ date('H:i:s d/m/Y', strtotime($mensagem->created_at)) }}</time> 
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-success"><i class="fa fa-check"></i> Nenhuma mensagem não lida</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection