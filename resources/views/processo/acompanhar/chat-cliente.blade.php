<div class="col-sm-12 col-md-4 col-lg-4">
    <h4 class="text-success"><i class="fa fa-comments-o marginBottom5"></i> Chat Cliente</h4>
    <div class="messaging">
        <div class="inbox_msg">
            <div class="mesgs">
                <div class="msg_history msg_history_cliente">
                    <div class="outgoing_msg">
                        <div class="sent_msg">
                            <p>Nenhum histórico de mensagens</p>
                            <span class="time_date"></span>
                        </div>
                    </div>
                </div>
                <div class="checkbox">
                    @if(\App\Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first()->fl_envio_enter_con == 'S')
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_interno" id="fl_envio_enter_interno" value="S" checked="checked">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @else
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_interno" id="fl_envio_enter_interno" value="S">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @endif
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <textarea id="texto_mensagem_interno" rows="3" class="write_msg" placeholder="Escrever mensagem"></textarea>
                        <button class="msg_send_btn msg_send_interno" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>