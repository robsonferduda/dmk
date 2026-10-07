<div class="modal fade" id="modal-assinatura" tabindex="-1" aria-labelledby="modal-assinatura-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ url('correspondente/contrato/assinatura/enviar/'.$idCrypt) }}" class="modal-content"
              onsubmit="this.querySelector('[type=submit]').disabled = true;">
            {{ csrf_field() }}
            <input type="hidden" name="v2" value="1">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-assinatura-titulo">Enviar contrato para assinatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">
                    O correspondente assina primeiro. Em seguida a DMK assina automaticamente e as testemunhas recebem o pedido por e-mail.
                    Revise o PDF antes de enviar.
                </p>

                <label class="form-label fw-semibold">Enviar ao correspondente por</label>
                <div class="d-flex gap-3 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="canal" id="canal-email" value="email" checked>
                        <label class="form-check-label" for="canal-email"><i class="bi bi-envelope me-1"></i> E-mail</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="canal" id="canal-whatsapp" value="whatsapp">
                        <label class="form-check-label" for="canal-whatsapp"><i class="bi bi-whatsapp me-1"></i> WhatsApp</label>
                    </div>
                </div>

                <div data-canal="email">
                    <label for="assinatura-email" class="form-label">E-mail do correspondente</label>
                    <input type="email" class="form-control" id="assinatura-email" name="email" value="{{ $assinaturaPainel['contato']['email'] }}">
                </div>
                <div data-canal="whatsapp" class="d-none">
                    <label for="assinatura-telefone" class="form-label">Celular com DDD</label>
                    <input type="tel" class="form-control" id="assinatura-telefone" name="telefone" value="{{ $assinaturaPainel['contato']['telefone'] }}" placeholder="(48) 99999-9999">
                    <div class="form-text">O correspondente recebe o link de assinatura no WhatsApp.</div>
                </div>

                @if($assinaturaPainel['sandbox'])
                    <div class="alert alert-warning small mt-3 mb-0">
                        Ambiente de testes da Autentique: os e-mails são enviados normalmente, mas o documento não tem validade jurídica e é apagado em poucos dias.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Enviar</button>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        var modal = document.getElementById('modal-assinatura');
        modal.querySelectorAll('input[name=canal]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                modal.querySelectorAll('[data-canal]').forEach(function (bloco) {
                    bloco.classList.toggle('d-none', bloco.getAttribute('data-canal') !== radio.value);
                });
            });
        });
    })();
</script>
