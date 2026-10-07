@php
    $assinatura = $assinaturaPainel['assinatura'];
    $emAndamento = $assinaturaPainel['emAndamento'];
    $impedimentos = $assinaturaPainel['impedimentos'];
    $idCryptAssinatura = \Crypt::encrypt($correspondente->cd_correspondente_cor);
    $coresClassico = ['warning' => 'warning', 'success' => 'success', 'danger' => 'danger', 'info' => 'info', 'secondary' => 'default'];
@endphp
<fieldset style="margin-bottom: 15px;">
    <legend>
        <i class="fa fa-pencil-square-o"></i> <strong>Assinatura eletrônica</strong>
        @if($assinaturaPainel['sandbox'])
            <span class="label label-warning" style="font-size: 11px;" title="Documentos do ambiente de testes da Autentique não têm validade jurídica">Testes</span>
        @endif
    </legend>
    <div class="row" style="margin-left: 5px;">
        @if($assinatura)
            <ul class="list-unstyled" style="margin-bottom: 12px;">
                <li>
                    <strong>Status: </strong>
                    <span class="label label-{{ $coresClassico[$assinatura->corSituacao()] }}">{{ $assinatura->rotuloSituacao() }}</span>
                </li>
                <li style="margin-top: 8px;">
                    <strong>Enviado em: </strong>
                    {{ $assinatura->dt_envio_cas ? $assinatura->dt_envio_cas->format('d/m/Y H:i') : '—' }} por {{ $assinatura->rotuloCanal() }}
                </li>
                @if($assinatura->dt_concluido_cas)
                    <li style="margin-top: 8px;"><strong>Concluído em: </strong>{{ $assinatura->dt_concluido_cas->format('d/m/Y H:i') }}</li>
                @endif
            </ul>

            @if($assinatura->signatarios->isNotEmpty() && $assinatura->dc_status_cas !== \App\ContratoAssinatura::ERRO)
                <table class="table table-condensed table-bordered" style="max-width: 720px; margin-bottom: 12px;">
                    <thead>
                        <tr><th>Parte</th><th>Enviado para</th><th>Situação</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                        @foreach($assinatura->signatarios as $signatario)
                            <tr>
                                <td>{{ $signatario->rotulo() }}</td>
                                <td>
                                    {{ $signatario->destino() }}
                                    @if($signatario->dc_motivo_csi)
                                        <br><small class="text-danger">{{ $signatario->dc_motivo_csi }}</small>
                                    @endif
                                </td>
                                <td><span class="label label-{{ $coresClassico[$signatario->corSituacao()] }}">{{ $signatario->rotuloSituacao() }}</span></td>
                                <td>{{ $signatario->dataSituacao() ? $signatario->dataSituacao()->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($assinatura->dc_erro_cas)
                <div class="alert alert-danger" style="margin-bottom:12px; padding:10px 12px;">{{ $assinatura->dc_erro_cas }}</div>
            @endif
        @else
            <p class="text-muted">O contrato ainda não foi enviado para assinatura.</p>
        @endif

        @if($emAndamento)
            @foreach([
                ['reenviar', 'btn-primary', 'fa-paper-plane', 'Reenviar', null],
                ['sincronizar', 'btn-default', 'fa-refresh', 'Atualizar situação', null],
                ['cancelar', 'btn-danger', 'fa-ban', 'Cancelar envio', 'Cancelar o envio? Ninguém mais conseguirá assinar este documento na Autentique.'],
            ] as [$acao, $classe, $icone, $rotulo, $confirmacao])
                <form method="POST" action="{{ url('correspondente/contrato/assinatura/'.$acao.'/'.$idCryptAssinatura) }}" style="display:inline-block; margin-right:8px;">
                    {{ csrf_field() }}
                    <button type="submit" class="btn {{ $classe }} btn-sm" @if($confirmacao) onclick="return confirm('{{ $confirmacao }}')" @endif>
                        <i class="fa {{ $icone }}"></i> {{ $rotulo }}
                    </button>
                </form>
            @endforeach
        @else
            @if($assinatura && $assinatura->dc_status_cas === \App\ContratoAssinatura::ASSINADO && $assinatura->dc_caminho_assinado_cas)
                <a href="{{ url('correspondente/contrato/assinatura/baixar/'.$idCryptAssinatura) }}" class="btn btn-success btn-sm" style="margin-right:8px;">
                    <i class="fa fa-download"></i> Baixar contrato assinado
                </a>
            @endif

            @if(empty($impedimentos))
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-assinatura-classico">
                    <i class="fa fa-paper-plane"></i>
                    {{ $assinatura && $assinatura->dc_status_cas !== \App\ContratoAssinatura::ERRO ? 'Enviar novamente para assinatura' : 'Enviar para assinatura' }}
                </button>
            @else
                <ul class="text-muted" style="margin: 0 0 0 18px; padding: 0;">
                    @foreach($impedimentos as $motivo)
                        <li>{{ $motivo }}</li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
</fieldset>

@if(!$emAndamento && empty($impedimentos))
    <div class="modal fade" id="modal-assinatura-classico" tabindex="-1" role="dialog" aria-labelledby="modal-assinatura-classico-titulo">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ url('correspondente/contrato/assinatura/enviar/'.$idCryptAssinatura) }}" class="modal-content"
                  onsubmit="this.querySelector('[type=submit]').disabled = true;">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal-assinatura-classico-titulo">Enviar contrato para assinatura</h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        O correspondente assina primeiro. Em seguida a DMK assina automaticamente e as testemunhas recebem o pedido por e-mail.
                        Revise o PDF antes de enviar.
                    </p>
                    <div class="form-group">
                        <label>Enviar ao correspondente por</label><br>
                        <label class="radio-inline"><input type="radio" name="canal" value="email" checked> <i class="fa fa-envelope"></i> E-mail</label>
                        <label class="radio-inline"><input type="radio" name="canal" value="whatsapp"> <i class="fa fa-whatsapp"></i> WhatsApp</label>
                    </div>
                    <div class="form-group" data-canal="email">
                        <label for="assinatura-email-classico">E-mail do correspondente</label>
                        <input type="email" class="form-control" id="assinatura-email-classico" name="email" value="{{ $assinaturaPainel['contato']['email'] }}">
                    </div>
                    <div class="form-group" data-canal="whatsapp" style="display:none;">
                        <label for="assinatura-telefone-classico">Celular com DDD</label>
                        <input type="tel" class="form-control" id="assinatura-telefone-classico" name="telefone" value="{{ $assinaturaPainel['contato']['telefone'] }}" placeholder="(48) 99999-9999">
                        <p class="help-block">O correspondente recebe o link de assinatura no WhatsApp.</p>
                    </div>
                    @if($assinaturaPainel['sandbox'])
                        <div class="alert alert-warning" style="margin-bottom:0;">
                            Ambiente de testes da Autentique: os e-mails são enviados normalmente, mas o documento não tem validade jurídica e é apagado em poucos dias.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            var modal = document.getElementById('modal-assinatura-classico');
            Array.prototype.forEach.call(modal.querySelectorAll('input[name=canal]'), function (radio) {
                radio.addEventListener('change', function () {
                    Array.prototype.forEach.call(modal.querySelectorAll('[data-canal]'), function (bloco) {
                        bloco.style.display = bloco.getAttribute('data-canal') === radio.value ? '' : 'none';
                    });
                });
            });
        })();
    </script>
@endif
