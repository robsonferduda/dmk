@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Mural</a></li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-desktop"></i> Correspondentes <span> > Mural</span>
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

        <article class="col-xs-12 col-sm-4">

            <div id="box-grafico" class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #a90329; margin: 0px;"><i class="fa fa-archive"></i> Meus Processos</h2>
                <div id="donut-graph" class="chart no-padding"></div>
                <a class="center" style="position: absolute; bottom: 20px;" href="{{ url('correspondente/processos') }}">Ver todos</a>
            </div>

        </article>

        <article class="col-xs-12 col-sm-4">            

            <div class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #009688; margin: 0px;"><i class="fa fa-envelope"></i> Meus Convites</h2>

                
                <div class="center">
                    <h1 style="font-size: 60px; margin-top: 50px; color: #bbbbbb;"><i class="fa fa-frown-o"></i></h1>
                    <h6 style="color: #bbbbbb;"><span>Você não possui convites</span></h6>
                    <a href="javascript:void(0);"><i class="fa fa-share-square-o"></i> Aumentar Visibilidade</a>
                </div>
                
               
               <!-- 
                <div style="margin: 8px 0px;">
                    <p style="margin: 0px">21/03/2020 - <span class="text-info">Escritório A</span> enviou um convite</p>
                <a href="javascript:void(0);"><i class="fa fa-check"></i> Aceitar</a>
                    <a class="text-danger" href="javascript:void(0);"><i class="fa fa-times"></i> Recusar</a>
                </div>

                <div style="margin: 8px 0px;">
                    <p style="margin: 0px">21/03/2020 - <span class="text-info">Escritório B</span> enviou um convite</p>
                   <a href="javascript:void(0);"><i class="fa fa-check"></i> Aceitar</a>
                    <a class="text-danger" href="javascript:void(0);"><i class="fa fa-times"></i> Recusar</a>
                </div>

                <div style="margin: 8px 0px;">
                    <p style="margin: 0px">21/03/2020 - <span class="text-info">Escritório F</span> enviou um convite </p>
                    <a href="javascript:void(0);"><i class="fa fa-check"></i> Aceitar</a>
                    <a class="text-danger" href="javascript:void(0);"><i class="fa fa-times"></i> Recusar</a>
                </div>

                <div style="margin: 8px 0px;">
                    <p style="margin: 0px">21/03/2020 - <span class="text-info">Escritório Advocacia</span> enviou um convite</p>
                    <a href="javascript:void(0);"><i class="fa fa-check"></i> Aceitar</a>
                    <a class="text-danger" href="javascript:void(0);"><i class="fa fa-times"></i> Recusar</a>
                </div>
                -->

                <a class="center" style="position: absolute; bottom: 20px;" href="{{ url('correspondente/processos') }}">Ver todos</a>
            </div>

        </article>

        <article class="col-xs-12 col-sm-4">

            <div class="well box-loader" style="min-height: 340px;">
                <h2 style="color: #3276b1; margin: 0px;"><i class="fa fa-comments"></i> Últimas Mensagens</h2>

                <div role="content">
                    <div class="no-padding">
                        <div class="chat-body custom-scroll" style="background: none; margin-top: 10px;">
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
                                            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($mensagem->cd_processo_pro)) }}" class="username txt-color-blueDark">Processo {{ ($mensagem->processo) ? $mensagem->processo->nu_processo_pro : '' }}</a> 
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
@section('script')
<script type="text/javascript">

    $(document).ready(function(){

        $.ajax({
            url: "../../api/processo/situacao/total",
            type: 'GET',
            dataType: "JSON",
            beforeSend: function()
            {
                $('#box-grafico').loader('show');
            },
            success: function(response)
            {                  
                $('#box-grafico').loader('hide');  

                if ($('#donut-graph').length) {
                    Morris.Donut({
                        element : 'donut-graph',
                        data : [{
                            value : 45,
                            label : 'No Prazo'
                        }, {
                            value : 35,
                            label : 'Data Limite'
                        }, {
                                value : 20,
                                label : 'Atrasado'
                        }
                        ],
                        colors: ['#8ec9bb', '#f2cf59', '#fb8e7e'],
                            formatter : function(x) {
                                return x + "%"
                            }
                    });
                }                
            },
            error: function(response)
            {
                $('#box-grafico').loader('hide');
                $('#donut-graph').html('<h1 class="center" style="font-size: 60px; margin-top: 50px; color: #d84e44;"><i class="fa fa-times"></i></h1><h4 class="center">Erro ao carregar dados</h4>'); 
            }
        });

    });
</script>
@endsection