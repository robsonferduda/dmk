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
                <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
                <a title="Pauta Online" href="{{ url('processos/pauta/online') }}" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-globe fa-lg"></i> Pauta Online</a>
                
            </div>
        </div>
    </div>
    @php use Carbon\Carbon; use Illuminate\Support\Str; @endphp
<div class="row">
    <div class="col-md-12 col-lg-12">
        <p>Existem <strong>{{ count($processos) }}</strong> processos com pendências de resposta</p>
    </div>
    @forelse($processos as $processo)
        @php
            Carbon::setLocale('pt_BR');

            $dtEnvio = Carbon::parse($processo->dt_notificacao_pro);
            $agora = Carbon::now();

            // Calcula diferença total em minutos
            $diffInMinutes = $dtEnvio->diffInMinutes($agora);

            $diferenca = $dtEnvio->diffForHumans($agora, true);

            // Converte para dias, horas e minutos
            $dias = floor($diffInMinutes / 1440); // 1440 minutos por dia
            $horas = floor(($diffInMinutes % 1440) / 60);
            $minutos = $diffInMinutes % 60;

            // Cálculo das horas totais
            $totalHoras = $dtEnvio->diffInHours($agora);

            $aviso = '';

            // Define a cor com base no total de horas
            if ($totalHoras < 12) {
                $cor = 'success'; // Verde
                $aviso = 'Normal';
            } elseif ($totalHoras <= 48) {
                $cor = 'warning'; // Amarelo
                $aviso = 'Atenção';
            } else {
                $cor = 'danger'; // Vermelho
                $aviso = 'Perigo';
            }

            // Monta a string de tempo com pluralização
            $tempo = [];
            if ($dias) $tempo[] = $dias . ' ' . Str::plural('dia', $dias);
            if ($horas) $tempo[] = $horas . ' ' . Str::plural('hora', $horas);
            if ($minutos || empty($tempo)) $tempo[] = $minutos . ' ' . Str::plural('minuto', $minutos);

            $tempoTranscorrido = implode(' e ', array_slice($tempo, -2));
        @endphp

        <div class="col-md-12 col-lg-12">
            <div class="card mb-1 well" style="padding: 3px 8px; margin-bottom: 10px;">
                <div class="card-body">
                    <h5 style="margin-bottom: 2px; margin-top: 3px;" class="card-title mb-0">
                        <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" target="_blank">{{ $processo->nu_processo_pro }}</a>
                    </h5>
                    <p style="margin-bottom: 2px;"><strong>Correspondente</strong>: {{ $processo->correspondente->nm_razao_social_con }}</p>
                    <p style="margin-bottom: 2px;"><strong>Prazo Fatal</strong>: {{  date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }}</p>
                    <p style="margin-bottom: 2px;"><strong>Última Atualização:</strong> {{ $dtEnvio->format('d/m/Y H:i') }}</p>
                    <p style="margin-bottom: 5px;"><strong>Tempo transcorrido:</strong> {{ $diferenca }}</p>

                    <span style="background-color: {{ $processo->status->ds_color_stp }}; position: absolute; top: 10px; right: 8px;" class="label label-default pull-right">{{ $processo->status->nm_status_processo_conta_stp }}</span>
                    <span class="label label-{{ $cor }} pull-right" style="position: absolute; bottom: 8px; right: 8px;">
                        {{ ucfirst($aviso) }}
                    </span>
                    <div class="text-left">
                        <a href="#detalhesProcesso{{ $processo->cd_processo_pro }}"
                           data-toggle="collapse"
                           role="button"
                           style="margin-bottom: 5px;" 
                           aria-expanded="false"
                           aria-controls="detalhesProcesso{{ $processo->cd_processo_pro }}"
                           class="small">
                            <i class="fa fa-bell mr-1"></i> Notificações Enviadas - Processo cadastrado em {{  date('d/m/Y H:i:s', strtotime($processo->created_at))  }}
                        </a>
                    </div>
                    <div class="collapse mt-3" id="detalhesProcesso{{ $processo->cd_processo_pro }}">
                        <div class="border-top pt-3">
                            @foreach($processo->notificacoes as $notificacao)
                                <p style="font-size: 11px; margin-bottom: 3px; margin-top: 3px;">
                                    {{ date('d/m/Y H:i:s', strtotime($notificacao->created_at)) }} - 
                                    {{ $notificacao->tipo->nm_tipo_notificacao_tin }} - 
                                    {{ App\Conta::where('cd_conta_con', $notificacao->cd_remetente)->first()->nm_razao_social_con }} >> 
                                    {{ App\Conta::where('cd_conta_con', $notificacao->cd_destinatario)->first()->nm_razao_social_con }} -
                                    {{ $notificacao->email_destinatario }}                                     
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">Nenhum processo pendente de aceite encontrado.</div>
        </div>
    @endforelse
</div>
<div class="modal fade in modal_top_alto" id="modal_pauta" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Pauta Diária</h4>
                     </div>
                    <div class="modal-body">
                        <form method="POST" class="smart-form" id="frm-pauta" action="{{ url('processo/pauta-diaria') }}">
                        @csrf
                            <fieldset>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Tipo de Intervalo de Data</label>
                                        <label class="select">
                                            <input type="hidden" id="contatoAux" value="">
                                            <select id="cd_contato_cot" name="cd_contato_cot">
                                                <option value="">Selecione o tipo de intervalo</option>    
                                                <option value="">Data de Solicitação</option> 
                                                <option value="">Prazo Fatal</option>         
                                            </select><i></i>  
                                        </label>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-4">
                                        <label>Data prazo fatal início</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_inicio" id="dt_inicio" placeholder="___/___/____" class="mascara_data">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label>Data prazo fatal fim</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_fim" id="dt_fim" placeholder="___/___/____" class="mascara_data" >
                                        </label>
                                    </section>
                               
                                    <section class="col col-2">
                                        <label class="radio-inline" style="margin-top: 22px; margin-left: 10px;">
                                            <input type="radio" name="tipo" id="excel" value="excel" required> Excel
                                        </label>
                                    </section> 
                                    <section class="col col-2">     
                                        <label class="radio-inline" style="margin-top: 22px; margin-left: 10px;">
                                            <input type="radio" name="tipo" id="pdf" value="pdf" required> PDF
                                        </label>
                                    </section> 
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Responsável</label>
                                        <select style="width: 100%"  class="select2" name="responsavel" >
                                            <option value="">Todos</option>
                                            @foreach($responsaveis as $user)
                                                 <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Tipos de Processo</label>
                                        <select style="width: 100%"  class="select2" name="tipoProcesso" >
                                            <option value="">Todos</option>
                                            @foreach($tiposProcesso as $tipo)
                                                 <option value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">    
                                    <input type="hidden" name="cdCorrespondente" value="">           
                                    <section class="col col-sm-12">
                                        <label>Correspondente</label>
                                        <label class="input">
                                            <input class="form-control" name="nmCorrespondente" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete_pauta" value="">
                                        </label>
                                    </section>
                                </div> 
                                <div class="row">
                                    <section class="col col-sm-12"> 
                                        <label>Status do Processo</label> 
                                        <select id="cd_status_processo_stp" name="cd_status_processo_stp" class="select2">
                                            <option selected value="">Todos</option>
                                            @foreach($status as $st)
                                                <option value="{{ $st->cd_status_processo_stp }}">{{ $st->nm_status_processo_conta_stp }}</option>
                                            @endforeach
                                        </select> 
                                    </section>                                     
                                </div>
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Gerar Pauta</button>
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                            </footer>
                        </form>
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