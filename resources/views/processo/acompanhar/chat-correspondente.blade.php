<div class="col-sm-12 col-md-4 col-lg-4">
    @if(Session::get('SESSION_NIVEL') != 3)
        <h4 class="nobreak text-info"><i class="fa fa-comments-o marginBottom5"></i> Chat Correspondente</h4>
    @else
        <h4><i class="fa fa-comments-o marginBottom5"></i> Histórico de Mensagens</h4>
    @endif

    <div class="messaging">
        <div class="inbox_msg">
            <div class="mesgs">
                <div class="msg_history msg_history_externo">
                    @if(count($mensagens_externas) > 0)
                        @foreach($mensagens_externas as $mensagem)
                            @if($mensagem->remetente_prm == Auth::user()->cd_conta_con)
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
                            @else
                                <div class="incoming_msg">
                                    <div class="incoming_msg_img">
                                        @if($mensagem->entidadeRemetenteColaborador)
                                            <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="user_profile">
                                        @else
                                            @if($mensagem->entidadeRemetente and file_exists('public/img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png'))
                                                <img class="img_msg" src="{{ asset('img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png') }}" alt="user_profile">
                                            @else
                                                <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="user_profile">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="received_msg">
                                        <div class="received_withd_msg">
                                            @if($mensagem->deleted_at)
                                                <p style="background: #e8e7e7 !important; color: #686868;">
                                                    Mensagem excluída
                                                </p>
                                            @else
                                                <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                <span class="time_date">
                                                    <strong>
                                                        @if($mensagem->entidadeRemetenteColaborador)
                                                            {{ ($mensagem->entidadeRemetenteColaborador and $mensagem->entidadeRemetenteColaborador->usuario) ? $mensagem->entidadeRemetenteColaborador->usuario->name : "Não definido" }}
                                                        @else
                                                            {{ $mensagem->entidadeRemetente->nm_razao_social_con }}
                                                        @endif
                                                    </strong>
                                                    disse em {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}
                                                </span>
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
                <div class="checkbox">
                    @if(\App\Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first()->fl_envio_enter_con == 'S')
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter" id="fl_envio_enter" value="S" checked="checked">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @else
                        <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter" id="fl_envio_enter" value="S">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                    @endif
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <textarea id="texto_mensagem" rows="3" class="write_msg" placeholder="Escrever mensagem"></textarea>
                        <button class="msg_send_btn msg_send_externo" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>