@if(Session::get('SESSION_NIVEL') and Session::get('SESSION_NIVEL') != 3)
<div class="col-sm-12 col-md-4 col-lg-4">
    <h4 class="text-success"><i class="fa fa-comments-o marginBottom5"></i> Chat Cliente</h4>
    <div class="messaging">
        <div class="inbox_msg">
            <div class="mesgs">
                <div class="msg_history msg_history_cliente">
                    @if(count($mensagens_cliente) > 0)
                        @foreach($mensagens_cliente as $mensagem)
                            @if($mensagem->remetente_prm == $processo->cd_cliente_cli)
                                <div class="incoming_msg">
                                    <div class="incoming_msg_img">
                                        <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="user_profile">
                                    </div>
                                    <div class="received_msg">
                                        <div class="received_withd_msg">
                                            @if($mensagem->deleted_at)
                                                <p style="background: #e8e7e7 !important; color: #686868;">
                                                    Mensagem excluída
                                                </p>
                                            @else
                                                <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                <span class="time_date"><strong>{{ ($mensagem->cliente) ? $mensagem->cliente->nm_razao_social_cli : 'Não identificado' }}</strong> disse em {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="outgoing_msg">
                                    <div class="sent_msg">
                                        @if($mensagem->deleted_at)
                                            <p style="background: #e8e7e7 !important; color: #686868;">
                                                Mensagem excluída
                                            </p>
                                        @else
                                            <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                            <span class="time_date">
                                                <a href="#" data-url="{{ url('processo/mensagem/excluir/'.\Crypt::encrypt($mensagem->cd_processo_mensagem_prm)) }}" class="excluir_registro_msg"><i class="fa fa-trash"></i> Excluir</a>
                                                {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}
                                            </span>
                                        @endif
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
                <div class="checkbox">
                    @if(\App\Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first()->fl_envio_enter_con == 'S')
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_cliente" id="fl_envio_enter_cliente" value="S" checked="checked">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @else
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_cliente" id="fl_envio_enter_cliente" value="S">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @endif
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <textarea id="texto_mensagem_cliente" rows="3" class="write_msg" placeholder="Escrever mensagem"></textarea>
                        <button class="msg_send_btn msg_send_cliente" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif