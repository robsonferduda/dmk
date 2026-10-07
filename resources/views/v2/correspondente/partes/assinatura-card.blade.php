@php
    $assinatura = $assinaturaPainel['assinatura'];
    $emAndamento = $assinaturaPainel['emAndamento'];
    $impedimentos = $assinaturaPainel['impedimentos'];
@endphp
<div class="card uv-panel mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-pen me-1"></i> Assinatura eletrônica</h5>
        @if($assinaturaPainel['sandbox'])
            <span class="badge bg-warning-subtle text-warning-emphasis" title="Documentos enviados no ambiente de testes da Autentique não têm validade jurídica">Testes</span>
        @endif
    </div>
    <div class="card-body">
        @if($assinatura)
            <div class="uv-meta-item">
                <span>Status</span>
                <strong class="text-{{ $assinatura->corSituacao() }}">{{ $assinatura->rotuloSituacao() }}</strong>
            </div>
            <div class="uv-meta-item">
                <span>Enviado em</span>
                <strong>{{ $assinatura->dt_envio_cas ? $assinatura->dt_envio_cas->format('d/m/Y H:i') : '—' }} · {{ $assinatura->rotuloCanal() }}</strong>
            </div>
            @if($assinatura->dt_concluido_cas)
                <div class="uv-meta-item">
                    <span>Concluído em</span>
                    <strong>{{ $assinatura->dt_concluido_cas->format('d/m/Y H:i') }}</strong>
                </div>
            @endif

            @if($assinatura->signatarios->isNotEmpty() && $assinatura->dc_status_cas !== \App\ContratoAssinatura::ERRO)
                <ul class="list-unstyled small mt-3 mb-0">
                    @foreach($assinatura->signatarios as $signatario)
                        <li class="d-flex justify-content-between align-items-start py-1 border-bottom">
                            <div class="me-2 text-break">
                                <div class="fw-semibold">{{ $signatario->rotulo() }}</div>
                                <div class="text-muted">{{ $signatario->destino() }}</div>
                                @if($signatario->dc_motivo_csi)
                                    <div class="text-{{ $signatario->corSituacao() }}">{{ $signatario->dc_motivo_csi }}</div>
                                @endif
                            </div>
                            <div class="text-end text-nowrap">
                                <span class="badge bg-{{ $signatario->corSituacao() }}">{{ $signatario->rotuloSituacao() }}</span>
                                @if($signatario->dataSituacao())
                                    <div class="text-muted">{{ $signatario->dataSituacao()->format('d/m H:i') }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($assinatura->dc_erro_cas)
                <div class="alert alert-danger small mt-3 mb-0">{{ $assinatura->dc_erro_cas }}</div>
            @endif
        @else
            <p class="text-muted small mb-0">O contrato ainda não foi enviado para assinatura.</p>
        @endif

        @if($emAndamento)
            <div class="d-flex flex-wrap gap-2 mt-3">
                <form method="POST" action="{{ url('correspondente/contrato/assinatura/reenviar/'.$idCrypt) }}" class="flex-fill">
                    {{ csrf_field() }}
                    <input type="hidden" name="v2" value="1">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-send me-1"></i> Reenviar</button>
                </form>
                <form method="POST" action="{{ url('correspondente/contrato/assinatura/sincronizar/'.$idCrypt) }}" class="flex-fill">
                    {{ csrf_field() }}
                    <input type="hidden" name="v2" value="1">
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-arrow-clockwise me-1"></i> Atualizar</button>
                </form>
                <form method="POST" action="{{ url('correspondente/contrato/assinatura/cancelar/'.$idCrypt) }}" class="flex-fill"
                      onsubmit="return confirm('Cancelar o envio? Ninguém mais conseguirá assinar este documento na Autentique.')">
                    {{ csrf_field() }}
                    <input type="hidden" name="v2" value="1">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
                </form>
            </div>
        @else
            @if($assinatura && $assinatura->dc_status_cas === \App\ContratoAssinatura::ASSINADO && $assinatura->dc_caminho_assinado_cas)
                <a href="{{ url('correspondente/contrato/assinatura/baixar/'.$idCrypt) }}?v2=1" class="btn btn-success btn-sm w-100 mt-3">
                    <i class="bi bi-file-earmark-check me-1"></i> Baixar contrato assinado
                </a>
            @endif

            @if(empty($impedimentos))
                <button type="button" class="btn btn-primary btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modal-assinatura">
                    <i class="bi bi-send me-1"></i> {{ $assinatura && $assinatura->dc_status_cas !== \App\ContratoAssinatura::ERRO ? 'Enviar novamente para assinatura' : 'Enviar para assinatura' }}
                </button>
            @else
                <ul class="text-muted small mt-3 mb-0 ps-3">
                    @foreach($impedimentos as $motivo)
                        <li>{{ $motivo }}</li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
</div>
