<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>{{ "DMK" }}</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="token" content="{{ csrf_token() }}">
            
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        
        <!-- #CSS Links -->
        <!-- Basic Styles -->
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/font-awesome.min.css') }}">

        <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production-plugins.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-skins.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/custom.css') }}">

        <!-- We recommend you use "your_style.css" to override SmartAdmin
             specific styles this will also ensure you retrain your customization with each SmartAdmin update.
        <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

        <!-- #FAVICONS -->
        <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
        <link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">

        <!-- #GOOGLE FONT -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

        <!-- #APP SCREEN / ICONS -->
        <!-- Specifying a Webpage Icon for Web Clip 
             Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
        <link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="76x76" href="img/splash/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="120x120" href="img/splash/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="152x152" href="img/splash/touch-icon-ipad-retina.png">
        
        <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        
        <!-- Startup image for web apps -->
        <link rel="apple-touch-startup-image" href="img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
        <link rel="apple-touch-startup-image" href="img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
        <link rel="apple-touch-startup-image" href="img/splash/iphone.png" media="screen and (max-device-width: 320px)">

    </head>

    <body class="{{ (Session::get('menu_minify') == 'on') ? 'minified' : '' }}">

        <!-- #HEADER -->
        <header id="header">
            <div id="logo-group">

                <!-- PLACE YOUR LOGO HERE -->
                <span id="logo"> <img src="{{ asset('img/logo.png') }}" alt="DMK"> </span>
                <!-- END LOGO PLACEHOLDER -->

                <!-- Note: The activity badge color changes when clicked and resets the number to 0
                     Suggestion: You may want to set a flag when this happens to tick off all checked messages / notifications -->
                <span id="activity" class="activity-dropdown"> <i class="fa fa-user"></i> <b class="badge"> 21 </b> </span>

                <!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
                <div class="ajax-dropdown">

                    <!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="activity" id="ajax/notify/mail.html">
                            Msgs (14) </label>
                        <label class="btn btn-default">
                            <input type="radio" name="activity" id="ajax/notify/notifications.html">
                            notify (3) </label>
                        <label class="btn btn-default">
                            <input type="radio" name="activity" id="ajax/notify/tasks.html">
                            Tasks (4) </label>
                    </div>

                    <!-- notification content -->
                    <div class="ajax-notifications custom-scroll">

                        <div class="alert alert-transparent">
                            <h4>Click a button to show messages here</h4>
                            This blank page message helps protect your privacy, or you can show the first message here automatically.
                        </div>

                        <i class="fa fa-lock fa-4x fa-border"></i>

                    </div>
                    <!-- end notification content -->

                    <!-- footer: refresh area -->
                    <span> Last updated on: 12/12/2013 9:43AM
                        <button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
                            <i class="fa fa-refresh"></i>
                        </button> </span>
                    <!-- end footer -->

                </div>
                <!-- END AJAX-DROPDOWN -->
            </div>

            <div class="project-context hidden-xs">
                <span class="label">Notificações</span>
                <span class="project-selector dropdown-toggle"><i class="fa fa-warning"></i> Você possui novas notificações</span>
            </div>
           
            <div class="pull-right">
                <div id="hide-menu" class="btn-header pull-right">
                    <span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
                </div>
                
                <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
                    <li class="">
                        <a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown"> 
                            <img src="img/avatars/sunny.png" alt="Usuário"/>  
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#ajax/profile.html" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="login.html" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong><u>L</u>ogout</strong></a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- logout button -->
                <div id="logout" class="btn-header transparent pull-right">
                    <span> <a href="{{ route('logout') }}" title="Sair" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
                </div>
                <!-- end logout button -->

                <div id="fullscreen" class="btn-header transparent pull-right">
                    <span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
                </div>

                <!-- search mobile button (this is hidden till mobile view port) -->
                <div id="search-mobile" class="btn-header transparent pull-right">
                    <span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
                </div>
                <!-- end search mobile button -->
                
                <!-- #SEARCH -->
                <!-- input: search field -->
                <form action="#ajax/search.html" class="header-search pull-right">
                    <input id="search-fld" type="text" name="param" placeholder="Busca Rápida de Processo">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                    <a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"><i class="fa fa-times"></i></a>
                </form>
            </div>
        </header>
    
        <aside id="left-panel">

            <!-- User info -->
            <div class="login-info">
                <span> <!-- User image size is adjusted inside CSS, it should stay as is --> 
                    
                    <a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
                        <img src="{{ asset('img/users/user.png') }}" alt="Usuário" /> 
                        <span>
                            {{ (Auth::user()) ? Auth::user()->name : 'Indefinido' }} 
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a> 
                    
                </span>
            </div>
            <nav>
                <ul>
                    <li class="">
                        <a href="{{ url('home') }}" title="blank_"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Início</span></a>
                    </li>
                    <li class="menu {{ (Session::get('menu_pai') == 'cliente') ? 'open' : '' }}">
                        <a href="#" title="Clientes" class="item_pai" id="cliente"><i class="fa fa-lg fa-fw fa-group"></i> <span class="menu-item-parent">Clientes</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'cliente') ? 'display: block;' : 'display: none;' }}">
                            <li>
                                <a href="{{ url('cliente/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('clientes') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                            <li>
                                <a href="dashboard-social.html" title="Dashboard"><span class="menu-item-parent">Agenda</span></a>
                            </li>
                            <li>
                                <a href="dashboard-social.html" title="Dashboard"><span class="menu-item-parent">Relatórios</span></a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                        <a href="#" title="Dashboard"><i class="fa fa-lg fa-fw fa-legal"></i> <span class="menu-item-parent">Correspondentes</span></a>
                        <ul>
                            <li>
                                <a href="index.html" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="dashboard-marketing.html" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                        <a href="#" title="Dashboard"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">Usuários</span></a>
                        <ul>
                            <li>
                                <a href="{{ url('usuarios/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('usuarios') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                        <a href="#" title="Dashboard"><i class="fa fa-lg fa-fw fa-archive"></i> <span class="menu-item-parent">Processos</span></a>
                        <ul>
                            <li>
                                <a href="{{ url('processos/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                            </li>
                            <li>
                                <a href="{{ url('processos') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                            </li>
                            <li>
                                <a href="dashboard-marketing.html" title="Dashboard"><span class="menu-item-parent">Acompanhar</span></a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                        <a href="#" title="Dashboard"><i class="fa fa-lg fa-fw fa-line-chart"></i> <span class="menu-item-parent">Financeiro</span></a>
                        <ul>
                            <li>
                                <a href="index.html" title="Dashboard"><span class="menu-item-parent">Balanço</span></a>
                            </li>
                        </ul>   
                    </li>
                    <li>
                        <a href="#" title="Dashboard"><i class="fa fa-lg fa-fw fa-usd"></i> <span class="menu-item-parent">Despesas</span></a>
                        <ul>
                            <li>
                                <a href="index.html" title="Dashboard"><span class="menu-item-parent">Balanço</span></a>
                            </li>
                        </ul>   
                    </li>
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
                                    
                                </ul>
                               
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/grupos-de-cidades') }}" title="Tipos de Serviço"><span class="menu-item-parent">Grupos de Cidades</span></a>
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
                </ul>
            </nav>

            <span class="minifyme"> 
                <a href="{{ url('configuracao/minify') }}"><i class="fa fa-arrow-circle-left hit" style="color: white;" data-toggle="modal" data-target="#redefinir_tela"></i></a> 
            </span>

        </aside>

        <div id="main" role="main">
            @yield('content')
        </div>
       
        <div class="page-footer">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <span class="txt-color-white">Nome do Sistema <span class="hidden-xs"> - Slogan</span> © 2019</span>
                </div>
            </div>
        </div>
    
        <div id="shortcut">
            <ul>
                <li>
                    <a href="index.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-envelope fa-4x"></i> <span>Mail <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
                </li>
            </ul>
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
                        <h2><i class="fa fa-spinner fa-spin"></i> Agurade, processando requisição...</h2>
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

        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
            if (!window.jQuery) {
                document.write('<script src="js/libs/jquery-3.2.1.min.js"><\/script>');
            }
        </script>

        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script>
            if (!window.jQuery.ui) {
                document.write('<script src="js/libs/jquery-ui.min.js"><\/script>');
            }
        </script>
        <script src="{{ asset('js/libs/jquery.mask.min.js') }}"></script>

        <script src="{{ asset('js/geral.js') }}?v={{ date('YmdHis') }}"></script>
        <script src="{{ asset('js/menu.js') }}"></script>
        
        <!-- IMPORTANT: APP CONFIG -->
        <script src="{{ asset('js/app.config.js') }}"></script>

        <!-- BOOTSTRAP JS -->
        <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>

        <!--[if IE 8]>
            <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
        <![endif]-->

        <!-- MAIN APP JS FILE -->
        <script src="{{ asset('js/app.min.js') }}"></script>
        <script src="{{ asset('js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.colVis.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.tableTools.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatables/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/plugin/datatable-responsive/datatables.responsive.min.js') }}"></script>
        <script src="{{ asset('js/plugin/select2/select2.min.js') }}"></script>
        <script src="{{ asset('js/plugin/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
        @yield('script')
        <script>
        
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        
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