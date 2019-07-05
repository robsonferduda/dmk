<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>{{ "DMK" }}</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
        
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production-plugins.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production.min.css') }}?v={{ date('YmdHis') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-skins.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/croppie.css') }}">        
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('fonts/google/css.css') }}">
    </head>

    <body class="{{ (Session::get('menu_minify') == 'on') ? 'minified' : '' }}">
        <header id="header">
            <div id="logo-group">
                <span id="logo"> <img src="{{ asset('img/logo.png') }}" alt="DMK"> </span>
            </div>
           
            <div class="pull-right">
                <div id="hide-menu" class="btn-header pull-right">
                    <span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
                </div>
                <div id="logout" class="btn-header transparent pull-right">
                    <span> <a href="{{ route('logout') }}" title="Sair" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
                </div>
                <div id="fullscreen" class="btn-header transparent pull-right">
                    <span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
                </div>
            </div>
        </header>
    
        <aside id="left-panel">
            <div class="login-info">
                <span> 
                    @role('correspondente') 
                        <a href="{{ url("correspondente/perfil/".Auth::user()->cd_entidade_ete) }}">
                            @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                                <img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="Foto de Perfil">
                            @else
                                <img src="{{ asset('img/users/user.png') }}" alt="Foto de Perfil">
                            @endif
                            <span>
                                {{ (Auth::user()) ? Auth::user()->name : 'Indefinido' }} 
                            </span>
                        </a> 
                    @endrole
                    @role('administrator')
                        <a href="{{ url("conta/detalhes/".\Crypt::encrypt(Auth::user()->cd_conta_con)) }}">
                            @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                                <img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="Foto de Perfil">
                            @else
                                <img src="{{ asset('img/users/user.png') }}" alt="Foto de Perfil">
                            @endif
                            <span>
                                {{ (Auth::user()) ? Auth::user()->name : 'Indefinido' }} 
                            </span>
                        </a> 
                    @endrole 
                    @role('colaborador')
                        <a href="{{ url("usuarios/".\Crypt::encrypt(Auth::user()->id)) }}">
                            @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                                <img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="Foto de Perfil">
                            @else
                                <img src="{{ asset('img/users/user.png') }}" alt="Foto de Perfil">
                            @endif
                            <span>
                                {{ (Auth::user()) ? Auth::user()->name : 'Indefinido' }} 
                            </span>
                        </a> 
                    @endrole                    
                </span>
            </div>
            <nav>
                <ul>
                    @role('administrator|colaborador|correspondente')    
                        <li class="">
                            <a href="{{ url('home') }}" title="blank_"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Início</span></a>
                        </li>              
                    @endrole
                    @role('administrator|colaborador')    
                        <li class="">
                            <a href="{{ url('contatos') }}" title="blank_"><i class="fa fa-lg fa-fw fa-book"></i> <span class="menu-item-parent">Agenda</span></a>
                        </li>              
                    @endrole
                    @role('correspondente')    
                        <li class="">
                            <a href="{{ url('correspondente/clientes') }}" title="blank_"><i class="fa fa-lg fa-fw fa-group"></i> <span class="menu-item-parent">Clientes</span></a>
                        </li>              
                    @endrole
                    @role('correspondente')    
                        <li class="">
                            <a href="{{ url('correspondente/processos') }}" title="blank_"><i class="fa fa-lg fa-fw fa-archive"></i> <span class="menu-item-parent">Processos</span></a>
                        </li>              
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'cliente') ? 'open' : '' }}">
                        <a href="#" title="Clientes" class="item_pai" id="cliente"><i class="fa fa-lg fa-fw fa-group"></i> <span class="menu-item-parent">Clientes</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'cliente') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('cliente/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('clientes') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'correspondente') ? 'open' : '' }}">
                        <a href="#" title="Correspondentes" class="item_pai" id="correspondente"><i class="fa fa-lg fa-fw fa-legal"></i> <span class="menu-item-parent">Correspondentes</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'correspondente') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('correspondente/novo') }}" title="Correspondente Novo"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('correspondentes') }}" title="Correspondentes"><span class="menu-item-parent">Listar</span></a>
                            </li>
                            <li>
                                <a href="{{ url('correspondente/relatorios') }}" title="Relatórios"><span class="menu-item-parent">Relatórios</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'usuarios') ? 'open' : '' }}">
                        <a href="#" title="Usuários" class="item_pai" id="usuarios"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">Usuários</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'usuarios') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('usuarios/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('usuarios') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'processos') ? 'open' : '' }}">
                        <a href="#" title="Processos" class="item_pai" id="processos"><i class="fa fa-lg fa-fw fa-archive"></i> <span class="menu-item-parent">Processos</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'processos') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('processos/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('processos') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                            <li>
                                <a href="{{ url('processos/acompanhamento') }}" title="Dashboard"><span class="menu-item-parent">Acompanhamento</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'financeiro') ? 'open' : '' }}">
                        <a href="#" title="Financeiro" class="item_pai" id="financeiro"><i class="fa fa-lg fa-fw fa-line-chart"></i> <span class="menu-item-parent">Financeiro</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'financeiro') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="index.html" title="Dashboard"><span class="menu-item-parent">Balanço</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'despesas') ? 'open' : '' }}">
                        <a href="#" title="Despesas"  class="item_pai" id="despesas"><i class="fa fa-lg fa-fw fa-usd"></i> <span class="menu-item-parent">Despesas</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'despesas') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="index.html" title="Dashboard"><span class="menu-item-parent">Balanço</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'configuracao') ? 'open' : '' }}">
                        <a href="#" title="Dashboard" class="item_pai" id="configuracao" ><i class="fa fa-lg fa-fw fa-cog"></i> <span class="menu-item-parent">Configurações</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'configuracao') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('configuracoes/areas') }}" title="Dashboard"><span class="menu-item-parent">Áreas de Direito</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/cargos') }}" title="Dashboard"><span class="menu-item-parent">Cargos</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/departamentos') }}" title="Dashboard"><span class="menu-item-parent">Departamentos</span></a>
                            </li>
                            <li>
                                <a href="#" title="Depesas"><span class="menu-item-parent">Despesas</span></a>
                                <ul>
                                    <li>
                                        <a href="{{ url('configuracoes/categorias-de-despesas') }}" title="Categorias"><span class="menu-item-parent">Categorias</span></a>
                                    </li>
                                    <li>
                                        <a href="{{ url('configuracoes/tipos-de-despesa') }}" title="Tipos de Despesa"><span class="menu-item-parent">Tipos de Despesas</span></a>
                                    </li>
                                    <li>
                                        <a href="{{ url('configuracoes/despesas-valores') }}" title="Tipos de Despesa"><span class="menu-item-parent">Valores</span></a>
                                    </li>
                                    
                                </ul>
                               
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/grupos-de-cidades') }}" title="Tipos de Serviço"><span class="menu-item-parent">Grupos de Cidades</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/tipos-de-contato') }}" title="Tipos de Serviço"><span class="menu-item-parent">Tipos de Contato</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/tipos-de-processo') }}" title="Tipos de Serviço"><span class="menu-item-parent">Tipos de Processo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/tipos-de-servico') }}" title="Tipos de Serviço"><span class="menu-item-parent">Tipos de Serviço</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/varas') }}" title="Varas"><span class="menu-item-parent">Varas</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    @role('administrator') 
                    <li class="menu {{ (Session::get('menu_pai') == 'permissoes') ? 'open' : '' }}">
                        <a href="#" title="Permissões"  class="item_pai" id="permissoes"><i class="fa fa-lg fa-fw fa-lock"></i> <span class="menu-item-parent">Permissões</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'permissoes') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('roles') }}" title="Perfis"><span class="menu-item-parent">Perfis</span></a>
                            </li>
                            <li>
                                <a href="{{ url('permissoes') }}" title="Perfis"><span class="menu-item-parent">Permissões</span></a>
                            </li>
                            <li>
                                <a href="{{ url('users') }}" title="Perfis"><span class="menu-item-parent">Usuários</span></a>
                            </li>
                        </ul>   
                    </li>
                    @endrole
                    <li class="">
                        <a href="{{ url('logout') }}" title="blank_"><i class="fa fa-lg fa-fw fa-sign-out"></i> <span class="menu-item-parent">Sair</span></a>
                    </li> 
                    <li>
                        <span class="minifyme"> 
                            <a href="{{ url('configuracao/minify') }}"><i class="fa fa-arrow-circle-left hit" style="color: white;" data-toggle="modal" data-target="#redefinir_tela"></i></a> 
                        </span>
                    </li>
                </ul>
            </nav>          
        </aside>

        <div id="main" role="main">
            @yield('content')
        </div>

        <div style="clear: both;"></div>
       
        <div class="page-footer">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <span class="txt-color-white">Nome do Sistema <span class="hidden-xs"> - Slogan</span> © 2019</span>
                </div>
            </div>
        </div>    

        <div class="modal fade in modal_top_alto" id="redefinir_tela" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Mensagem do Sistema</h4>
                     </div>
                    <div class="modal-body center">
                        <h2><i class="fa fa-spinner fa-spin"></i> Redefinindo tela</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade in modal_top_alto" id="processamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Mensagem do Sistema</h4>
                     </div>
                    <div class="modal-body center">
                        <h2><i class="fa fa-gear fa-spin"></i> Aguarde, processando requisição...</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modal_erro" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> Erro de Processamento<strong></strong></h4>
                    </div>
                        <div class="modal-body" style="text-align: center;">
                                <h4 class="text-danger"><i class="fa fa-times"></i> Ops...</h4>
                                <h4>Ocorreu um erro ao processar sua operação. Tente novamente ou entre em contato com nosso suporte técnico.</h4>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                        </div>
                </div>
            </div>
        </div>

        <div class="modal fade in" id="upload-image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 800px;">
                <div class="modal-content">
                    <div class="row" style="margin:0; padding: 5px;">

                                <div class="col-md-12">
                                    <h2>Atualizar Foto de Perfil</h2>
                                    <strong>Selecione uma imagem:</strong>
                                    <br/>
                                    <input type="file" id="upload">
                                                                       
                                </div>

                                <div class="col-md-6 text-center">
                                    <div id="upload-demo" style="width:350px"></div>
                                </div>

                                <div class="col-md-6 text-center" style="">
                                    <div id="upload-demo-i" style="background:#e1e1e1;width:300px;padding:30px;height:300px;margin-top:30px"></div>
                                </div>

                                <div class="col-md-12 center" style="padding-bottom: 15px;">
                                    <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Fechar</a>
                                    <button class="btn btn-success upload-result"><i class="fa fa-upload"></i> Enviar Imagem</button>
                                </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modal_exclusao" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-trash"></i> <strong>Excluir Registro</strong></h4>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <h4>Essa operação irá excluir o registro definitivamente.</h4>
                        <h4>Deseja continuar?</h4>
                        <input type="hidden" name="id" id="id_exclusao">
                        <input type="hidden" name="url" id="url">
                        <div class="msg_retorno"></div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" id="btn_confirma_exclusao" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</a>
                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modal_exclusao_honorario" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-trash"></i> <strong>Excluir Registro</strong></h4>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <h4>Essa operação irá excluir todas as ocorrências <span id="txt_exclusao_honorario"></span>. Para excluir somente um valor, apague o valor numérico e pressione o botão <strong>Atualizar Valores</strong></h4>
                        <h4>Deseja continuar?</h4>
                        <input type="hidden" name="id" id="id_exclusao_honorario">
                        <input type="hidden" name="url" id="url_honorario">
                        <div class="msg_retorno_honorario"></div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" id="btn_confirma_exclusao_honorario" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</a>
                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modal_cancela_correspondente" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> <strong> Remover Correspondente</strong></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 center">
                                {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/remover', 'class' => 'form-inline']) !!}
                                    <p style="font-size: 14px;">
                                        Essa operação irá remover o corresponde da sua lista de correspondentes.
                                    </p>
                                    <h6>Confirma a remoção na sua lista de Correspondentes?</h6>
                                    <input type="hidden" name="id" id="id_correspondente">
                                    <input type="hidden" name="url" id="url">
                                    <div class="msg_retorno"></div>

                                    <div class="center marginTop20">
                                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('js/libs/jquery.mask.min.js') }}"></script>
        <script src="{{ asset('js/geral.js') }}?v={{ date('YmdHis') }}"></script>
        <script src="{{ asset('js/menu.js') }}"></script>
        <script src="{{ asset('js/app.config.js') }}"></script>
        <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>

        <!--[if IE 8]>
            <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
        <![endif]-->
        
        <script src="{{ asset('js/app.min.js') }}"></script>
        <script src="{{ asset('js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.colVis.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.tableTools.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatable-responsive/datatables.responsive.min.js') }}"></script>
        <script src="{{ asset('js/plugin/select2/select2.min.js') }}"></script>
        <script src="{{ asset('js/plugin/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
        <script src="{{ asset('js/plugin/croppie.js') }}"></script>
        <script src="{{ asset('js/plugin/jquery.form.js') }}"></script>
        <script src="{{ asset('js/plugin/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('js/plugin/x-editable/x-editable.min.js') }}"></script>
        @yield('script')
        <script>
        
        // DO NOT REMOVE : GLOBAL FUNCTIONS!

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            }
        });


        $uploadCrop = $('#upload-demo').croppie({
            enableExif: true,
            viewport: {
                width: 200,
                height: 200,
                type: 'circle'
            },
            boundary: {
                width: 300,
                height: 300
            }
        });


        $('#upload').on('change', function () { 
            var reader = new FileReader();
            reader.onload = function (e) {
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
        });
        
        $(document).ready(function() {
            
            pageSetUp();

        /* BASIC ;*/
                var responsiveHelper_dt_basic = undefined;
                var responsiveHelper_datatable_fixed_column = undefined;
                var responsiveHelper_datatable_col_reorder = undefined;
                var responsiveHelper_datatable_tabletools = undefined;
                
                var breakpointDefinition = {
                    tablet : 1024,
                    phone : 480
                };
    
                $('#dt_basic').dataTable({
                    "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                        "t"+
                        "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
                    "autoWidth" : true,
                    "ordering": false,
                    "oLanguage": {
                        "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>',
                        "sEmptyTable": "Nenhum registro encontrado",
                        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sInfoThousands": ".",
                        "sLengthMenu": "_MENU_ resultados por página",
                        "sLoadingRecords": "Carregando...",
                        "sProcessing": "Processando...",
                        "sZeroRecords": "Nenhum registro encontrado",
                        
                        "oPaginate": {
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                            "sFirst": "Primeiro",
                            "sLast": "Último"
                        },
                        "oAria": {
                            "sSortAscending": ": Ordenar colunas de forma ascendente",
                            "sSortDescending": ": Ordenar colunas de forma descendente"
                        },
                    },
                    "preDrawCallback" : function() {
                        // Initialize the responsive datatables helper once.
                        if (!responsiveHelper_dt_basic) {
                            responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#dt_basic'), breakpointDefinition);
                        }
                    },
                    "rowCallback" : function(nRow) {
                        responsiveHelper_dt_basic.createExpandIcon(nRow);
                    },
                    "drawCallback" : function(oSettings) {
                        responsiveHelper_dt_basic.respond();
                    }
                });
    
            /* END BASIC */
            })

        </script>

    </body>
</html>