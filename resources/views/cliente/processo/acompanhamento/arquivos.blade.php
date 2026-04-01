  <fieldset style="margin-bottom: 15px;">
                                    <legend>
                                        <i class="fa fa-files-o"></i> <strong>Arquivos do Processo</strong>
                                        @if(count($processo->anexos))
                                            <a href="{{ url('processos/arquivos/download/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><span>Baixar Todos</span></a>
                                        @endif
                                    </legend>

                                    
                                        <h6>Arquivos do Processo</h6>
                                        <p>Caso preferir, informe um link com os arquivos do processo. Para fazer isso <a id="informarLink">Clique Aqui</a>.</p>

                                        @if($processo->ds_link_dados_pro)
                                            <p>Dados do processo disponíveis em: <a href="{{ $processo->ds_link_dados_pro }}" target="_blank">{{ $processo->ds_link_dados_pro }}</a></p>                                       
                                        @endif
                                        
                                            <div id="filepicker">
                                                <!-- Button Bar -->
                                                <div class="button-bar">

                                                    <div class="btn btn-success btn-upload-plugin fileinput">
                                                        <i class="fa fa-files-o"></i> Buscar Arquivos
                                                        <input type="file" name="files[]" id="input-file" multiple>
                                                    </div>   

                                                    <button type="button" class="btn btn-primary start-all btn-upload-plugin">
                                                        <i class="fa fa-upload"></i> Enviar Todos
                                                    </button>                  

                                                </div>

                                                <!-- Listar Arquivos -->
                                                <div class="table-responsive div-table">
                                                    <table class="table table-upload">
                                                        <thead>
                                                            <tr>
                                                                <th class="column-name">Nome do Arquivo</th>
                                                                <th class="column-size center">Tamanho</th>                                                            
                                                                <th class="center">Opções</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="files">

                                                        </tbody>                        
                                                    </table>
                                                </div>

                                                <!-- Drop Zone -->
                                                <div class="drop-window">
                                                    <div class="drop-window-content">
                                                        <h3><i class="fa fa-upload"></i> Drop files to upload</h3>
                                                    </div>
                                                </div>
                                            </div>

                                        @role('cliente')
                                        <div class="onoffswitch-container" style="margin-top: 15px;">
                                            <span class="onoffswitch-title">Todos os documentos para a realização do ato foram anexados?</span>
                                            <span class="onoffswitch">
                                                <input type="checkbox" {{ ($processo->fl_documentacao_cliente_pro) ? 'checked' : '' }}
                                                    name="fl_documentacao_cliente_pro"
                                                    class="onoffswitch-checkbox"
                                                    id="fl_documentacao_cliente_pro">
                                                <label class="onoffswitch-label" for="fl_documentacao_cliente_pro">
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </span>
                                            <span class="erro_atualiza_status text-danger" style="margin-left: 10px;"></span>
                                        </div>
                                        <p class="text-muted" style="margin-top: 8px; font-size: 12px;">
                                            <i class="fa fa-info-circle"></i>
                                            Ao marcar como <strong>SIM</strong>, o escritório será notificado automaticamente para dar continuidade ao processo.
                                        </p>
                                        @endrole
                                                                     
                                  

                                        </fieldset>