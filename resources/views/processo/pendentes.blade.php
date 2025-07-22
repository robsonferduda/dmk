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
    </div>
    @php use Carbon\Carbon; use Illuminate\Support\Str; @endphp
<div class="row">
    <div class="col-md-12 col-lg-12">
        <p>Existem <strong>{{ count($processos) }}</strong> processos com pendências de resposta</p>
    </div>
    @forelse($processos as $processo)
        @php
            Carbon::setLocale('pt_BR');

            $dtEnvio = Carbon::parse($processo->updated_at);
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
                            <i class="fa fa-bell mr-1"></i> Notificações Enviadas
                        </a>
                    </div>
                    <div class="collapse mt-3" id="detalhesProcesso{{ $processo->cd_processo_pro }}">
                        <div class="border-top pt-3">
                            @foreach($processo->notificacoes as $notificacao)
                                <p style="font-size: 11px; margin-bottom: 3px; margin-top: 3px;">
                                    {{ date('d/m/Y H:i:s', strtotime($notificacao->created_at)) }} - 
                                    {{ $notificacao->tipo->nm_tipo_notificacao_tin }} - 
                                    {{ App\Conta::where('cd_conta_con', $notificacao->cd_remetente)->first()->nm_razao_social_con }} >> 
                                    {{ App\Conta::where('cd_conta_con', $notificacao->cd_destinatario)->first()->nm_razao_social_con }}                                     
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
@endsection