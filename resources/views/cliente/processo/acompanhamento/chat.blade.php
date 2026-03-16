<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
    <div class="well">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h4>
                <i class="fa fa-comments-o marginBottom5"></i> Chat Escritório
            </h4>
            <div class="messaging">
                <div class="inbox_msg">
                    <div class="mesgs">
                        <div class="msg_history msg_history_cliente">

                            @if(count($mensagens_cliente) > 0)
                                @foreach($mensagens_cliente as $mensagem)
                                    @if($mensagem->remetente_prm == $processo->cd_cliente_cli)
                                        <div class="outgoing_msg">
                                            <div class="sent_msg">
                                                @if($mensagem->deleted_at)
                                                    <p style="background: #e8e7e7 !important; color: #686868;">Mensagem excluída</p>
                                                @else
                                                    <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                    <span class="time_date"><strong>{{ ($mensagem->cliente) ? $mensagem->cliente->nm_razao_social_cli : 'Não Identificado' }}</strong> disse em {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="incoming_msg">
                                            <div class="incoming_msg_img">
                                                <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="escritorio">
                                            </div>
                                            <div class="received_msg">
                                                <div class="received_withd_msg">
                                                    @if($mensagem->deleted_at)
                                                        <p style="background: #e8e7e7 !important; color: #686868;">Mensagem excluída</p>
                                                    @else
                                                        <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                        <span class="time_date"><strong>Escritório</strong> disse em {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="outgoing_msg">
                                    <div class="sent_msg">
                                        <p>Nenhum histórico de mensagens</p>
                                        <span class="time_date"></span>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="type_msg" style="margin-top: 10px;">
                            <div class="input_msg_write">
                                <textarea id="texto_mensagem_cliente" rows="3" class="write_msg" placeholder="Escrever mensagem para o escritório"></textarea>
                                <button class="msg_send_btn msg_send_cliente" type="button">
                                    <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>
</article>