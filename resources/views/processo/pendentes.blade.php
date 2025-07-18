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
    @forelse($processos as $processo)
        @php
            $dtEnvio = Carbon::parse($processo->updated_at);
            $agora = Carbon::now();

            // Calcula diferença total em minutos
            $diffInMinutes = $dtEnvio->diffInMinutes($agora);

            // Converte para dias, horas e minutos
            $dias = floor($diffInMinutes / 1440); // 1440 minutos por dia
            $horas = floor(($diffInMinutes % 1440) / 60);
            $minutos = $diffInMinutes % 60;

            // Cálculo das horas totais
            $totalHoras = floor($diffInMinutes / 60);

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
            <div class="card mb-3 position-relative well">
                <div class="card-body">
                    <h5 class="card-title mb-2">Processo {{ $processo->nu_processo_pro }}</h5>
                    <p class="mb-1"><strong>Última Atualização:</strong> {{ $dtEnvio->format('d/m/Y H:i') }}</p>
                    <p class="mb-0"><strong>Tempo transcorrido:</strong> {{ $tempoTranscorrido }}</p>
                </div>
                <span class="label label-{{ $cor }} pull-right" style="top: 10px; right: 10px;">
                    {{ ucfirst($aviso) }}
                </span>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">Nenhum processo pendente de aceite encontrado.</div>
        </div>
    @endforelse
</div>
@endsection