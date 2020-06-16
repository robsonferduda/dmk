<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>{{ env('APP_NAME') }}</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="token" content="{{ csrf_token() }}">
        <meta name="conta" content="{{ Session::get('SESSION_CD_CONTA') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <link rel="icon" href="{{ asset('img/favicon/favicon-32x32.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('img/favicon/favicon-32x32.png') }}" type="image/x-icon">
        
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/font-awesome.min.css') }}">
        
        <!-- Smartadmin-all é a junção dos smartadmin-production-plugins.min.css,smartadmin-production.min.css,smartadmin-skins.min.css -->
        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-all.css') }}">

        {!! Minify::stylesheet('/css/custom.css')->withFullUrl() !!}
    
        <!--others.css é a junção dos croppie.css,filepicker.css,css-loader.css,bootstrap-colorselector.css -->
         <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/others.css') }}">

        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('fonts/google/css.css') }}">
        @yield('stylesheet')
    </head>

    <body class="{{ (Session::get('menu_minify') == 'on') ? 'minified' : '' }}" id='body-principal'>
        <header id="header">
            <div id="logo-group">
                <span id="logo"> <img src="{{ asset('img/logo.png') }}" alt="DMK"> </span>

                @php
                    $mensagens_pendentes = (new \App\ProcessoMensagem)->getMensagensPendentesRemetente(Session::get('SESSION_CD_CONTA'));
                @endphp

                <span id="activity" class="activity-dropdown"> <i class="fa fa-bell"></i><b class="badge badge-count">{{ count($mensagens_pendentes) }}</b></span>

                <div class="ajax-dropdown">

                    <div class="btn-group center">
                        <strong>Mensagens não lidas</strong>
                    </div>

                    <div class="ajax-notifications custom-scroll" style="padding: 1px 0;">
                        @foreach($mensagens_pendentes as $mensagem)
                            <ul class="notification-body">
                                @if($mensagem->cd_tipo_mensagem_tim == \App\Enums\TipoMensagem::EXTERNA )
                                    <li>
                                        <span class="unread">
                                            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($mensagem->cd_processo_pro)) }}" class="msg">
                                                @if(file_exists('public/img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png'))                                                                           
                                                    <img src="{{ asset('img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png') }}" alt="" class="air air-top-left margin-top-5" width="40" height="40" />
                                                @else
                                                    <img src="{{ asset('img/users/user.png') }}" alt="" class="air air-top-left margin-top-5" width="40" height="40" />
                                                @endif
                                                
                                                <span class="txt-color-red">
                                                        {{ $mensagem->entidadeRemetente->nm_razao_social_con }}
                                                </span>
                                                
                                                <span class="subject">Processo {{ ($mensagem->processo) ? $mensagem->processo->nu_processo_pro : '' }}</span>
                                                <span>{{ date('H:i:s d/m/Y', strtotime($mensagem->created_at)) }}</span>
                                            </a>
                                        </span>
                                    </li>
                                @else
                                    <li>
                                        <span class="unread">
                                            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($mensagem->cd_processo_pro)) }}" class="msg">
                                                @if(file_exists('public/img/users/ent'.$mensagem->entidadeInterna->cd_entidade_ete.'.png'))                                                                           
                                                    <img src="{{ asset('img/users/ent'.$mensagem->entidadeInterna->cd_entidade_ete.'.png') }}" alt="" class="air air-top-left margin-top-5" width="40" height="40" />
                                                @else
                                                    <img src="{{ asset('img/users/user.png') }}" alt="" class="air air-top-left margin-top-5" width="40" height="40" />
                                                @endif
                                            
                                                <span class="txt-color-red">
                                                        {{ $mensagem->entidadeInterna->usuario->name }}
                                                </span>
                                                
                                                <span class="subject">Processo {{ $mensagem->processo->nu_processo_pro }}</span>
                                                <span>{{ date('H:i:s d/m/Y', strtotime($mensagem->created_at)) }}</span>
                                            </a>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        @endforeach
                    </div>

                    <span> Última atualização em {{ date('H:i:s d/m/Y') }}</span>

                </div>
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
                        <a href="{{ url("correspondente/perfil/".\Crypt::encrypt(Auth::user()->cd_entidade_ete)) }}">
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
                    <li class="">
                        <a href="{{ url('home') }}" title="blank_"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Mural</span></a>
                    </li>              

                    @can('agenda.index')    
                        <li class="">
                            <a href="{{ url('contatos') }}" title="blank_"><i class="fa fa-lg fa-fw fa-book"></i> <span class="menu-item-parent">Agenda</span></a>
                        </li>              
                    @endcan

                    @can('calendario.index')    
                        <li class="">
                            <a href="{{ url('calendario') }}" title="blank_"><i class="fa fa-lg fa-fw  fa-calendar"></i> <span class="menu-item-parent">Calendário</span></a>
                        </li>              
                    @endcan

                    @can('cliente.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'cliente') ? 'open' : '' }}">
                        <a href="#" title="Clientes" class="item_pai" id="cliente"><i class="fa fa-lg fa-fw fa-group"></i> <span class="menu-item-parent">Clientes</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'cliente') ? 'display: block;' : 'display: none;' }}">
                            @can('cliente.novo')
                                <li>
                                    <a href="{{ url('cliente/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                                </li>
                            @endcan
                            @can('cliente.listar')
                                <li>
                                    <a href="{{ url('clientes') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                                </li>
                            @endcan
                        </ul>   
                    </li>
                    @endcan

                    @can('correspondente.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'correspondente') ? 'open' : '' }}">
                        <a href="#" title="Correspondentes" class="item_pai" id="correspondente"><i class="fa fa-lg fa-fw fa-legal"></i> <span class="menu-item-parent">Correspondentes</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'correspondente') ? 'display: block;' : 'display: none;' }}">
                            @can('correspondente.categorias')
                                <li>
                                    <a href="{{ url('correspondente/categorias') }}" title="Categorias"><span class="menu-item-parent">Categorias</span></a>
                                </li>
                            @endcan
                            @can('correspondente.buscar')
                                <li>
                                    <a href="{{ url('correspondente/todos') }}" title="Buscar Correspondentes"><span class="menu-item-parent">Buscar</span></a>
                                </li>
                            @endcan
                            @can('correspondente.novo')
                                <li>
                                    <a href="{{ url('correspondente/novo') }}" title="Novo Correspondente"><span class="menu-item-parent">Novo</span></a>
                                </li>
                            @endcan
                            @can('correspondente.meus-correspondentes')
                                <li>
                                    <a href="{{ url('correspondentes') }}" title="Meus Correspondentes"><span class="menu-item-parent">Meus Correspondentes</span></a>
                                </li>
                            @endcan
                            @can('correspondente.relatorios')
                                <li>
                                    <a href="{{ url('correspondente/relatorios') }}" title="Relatórios de Correspondentes"><span class="menu-item-parent">Relatórios</span></a>
                                </li>
                            @endcan
                        </ul>   
                    </li>
                    @endcan

                    @can('usuario.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'usuarios') ? 'open' : '' }}">
                        <a href="#" title="Usuários" class="item_pai" id="usuarios"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">Usuários</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'usuarios') ? 'display: block;' : 'display: none;' }}">
                            @can('usuario.novo')
                                <li>
                                    <a href="{{ url('usuarios/novo') }}" title="Dashboard"><span class="menu-item-parent">Novo</span></a>
                                </li>
                            @endcan
                            @can('usuario.listar')
                                <li>
                                    <a href="{{ url('usuarios') }}" title="Dashboard"><span class="menu-item-parent">Listar</span></a>
                                </li>
                            @endcan
                        </ul>   
                    </li>
                    @endcan

                    @can('processo.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'processos') ? 'open' : '' }}">
                        <a href="#" title="Processos" class="item_pai" id="processos"><i class="fa fa-lg fa-fw fa-archive"></i> <span class="menu-item-parent">Processos</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'processos') ? 'display: block;' : 'display: none;' }}">
                            @can('processo.novo')   
                                <li>
                                    <a href="{{ url('processos/novo') }}" title="Novo"><span class="menu-item-parent">Novo</span></a>
                                </li>
                            @endcan
                            @can('processo.listar')
                                <li>
                                    <a href="{{ url('processos') }}" title="Listar"><span class="menu-item-parent">Listar</span></a>
                                </li>
                            @endcan
                            @can('processo.acompanhamento')
                                <li>
                                    <a href="{{ url('processos/acompanhamento') }}" title="Acompanhamento"><span class="menu-item-parent">Acompanhamento</span></a>
                                </li>
                            @endcan
                            @can('processo.relatorios')
                                <li>
                                    <a href="{{ url('processos/relatorios') }}" title="Relatórios"><span class="menu-item-parent">Relatórios</span></a>
                                </li>
                            @endcan
                        </ul>   
                    </li>
                    @endcan

                    @can('financeiro.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'financeiro') ? 'open' : '' }}">
                        <a href="#" title="Financeiro" class="item_pai" id="financeiro"><i class="fa fa-lg fa-fw fa-line-chart"></i> <span class="menu-item-parent">Financeiro</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'financeiro') ? 'display: block;' : 'display: none;' }}">
                            @can('financeiro.balanco')     
                                <li>
                                    <a href="{{ url('financeiro/dashboard') }}" title="Dashboard"><span class="menu-item-parent">Dashboard</span></a>
                                </li>
                            @endcan
                            @can('financeiro.balanco')     
                                <li>
                                    <a href="{{ url('financeiro/balanco') }}" title="Dashboard"><span class="menu-item-parent">Balanço</span></a>
                                </li>
                            @endcan
                            @can('financeiro.entradas')     
                                 <li>
                                    <a href="{{ url('financeiro/entradas') }}" title="Entradas"><span class="menu-item-parent">Entradas</span></a>
                                </li>
                            @endcan
                            @can('financeiro.saidas') 
                                <li>
                                    <a href="{{ url('financeiro/saidas') }}" title="Saídas"><span class="menu-item-parent">Saídas</span></a>
                                </li>
                            @endcan
                            @can('financeiro.relatorios') 
                                <li>
                                    <a href="{{ url('financeiro/relatorios') }}" title="Relatórios"><span class="menu-item-parent">Relatórios</span></a>
                                </li>
                            @endcan
                        </ul>   
                        
                    </li>
                    @endcan

                    @can('despesas.index') 
                    <li class="menu {{ (Session::get('menu_pai') == 'despesas') ? 'open' : '' }}">
                        <a href="#" title="Despesas"  class="item_pai" id="despesas"><i class="fa fa-lg fa-fw fa-usd"></i> <span class="menu-item-parent">Despesas</span></a>
                        <ul style="{{ (Session::get('menu_pai') == 'despesas') ? 'display: block;' : 'display: none;' }}">
                            @can('despesas.novo')
                                <li>
                                    <a href="{{ url('despesas/novo') }}" title="Cadastrar"><span class="menu-item-parent">Novo</span></a>
                                </li>
                            @endcan
                            @can('despesas.lancamentos')
                                <li>
                                    <a href="{{ url('despesas/lancamentos') }}" title="Despesas"><span class="menu-item-parent">Lançamentos</span></a>
                                </li>
                            @endcan
                        </ul>   
                    </li>
                    @endcan

                    @can('configuracoes.index') 
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
                                <a href="{{ url('configuracoes/prazos') }}" title="Prazos"><span class="menu-item-parent">Prazos</span></a>
                            </li>
                            <li>
                                <a href="{{ url('configuracoes/notificacoes') }}" title="Notificações"><span class="menu-item-parent">Notificações</span></a>
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
                    @endcan

                    @role('super-user')
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

                    @role('correspondente')    
                        <li class="">
                            <a href="{{ url('correspondente/clientes') }}" title="blank_"><i class="fa fa-lg fa-fw fa-group"></i> <span class="menu-item-parent">Clientes</span></a>
                        </li>                 
                        <li class="menu {{ (Session::get('menu_pai') == 'processos') ? 'open' : '' }}">
                            <a href="#" title="Processos" class="item_pai" id="processos"><i class="fa fa-lg fa-fw fa-archive"></i> <span class="menu-item-parent">Processos</span></a>
                            <ul style="{{ (Session::get('menu_pai') == 'processos') ? 'display: block;' : 'display: none;' }}">
                                <li>
                                    <a href="{{ url('processos') }}" title="Listar"><span class="menu-item-parent">Arquivo</span></a>
                                </li>
                                <li>
                                    <a href="{{ url('correspondente/processos') }}" title="Acompanhamento"><span class="menu-item-parent">Acompanhamento</span></a>
                                </li>
                            </ul>   
                        </li>              
                        <li class="">
                            <a href="{{ url('correspondente/painel/relatorios') }}" title="blank_"><i class="fa fa-lg fa-fw fa-file-o"></i> <span class="menu-item-parent">Relatórios</span></a>
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
                    <span class="txt-color-white">Easyjuris <span class="hidden-xs"> </span> © 2019</span>
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

        <div class="modal fade in" id="upload-image" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 800px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-image-o"></i> <strong>Atualizar Foto de Perfil</strong></h4>
                    </div>
                    <div class="row" style="margin:0; padding: 5px;">
                        <div class="col-md-12" style="padding: 5px 45px;">
                            <strong>Selecione uma imagem</strong>
                            <br/>
                            <input type="file" id="upload" class="btn btn-default">                                  
                        </div>
                            <div class="col-md-6 text-center">
                                <div id="upload-demo" style="width:350px"></div>
                            </div>
                            <div class="col-md-6 text-center" style="">
                                <div id="upload-demo-i" style="background:#e1e1e1;width:350px;padding:5px;height:350px;margin-top:30px"></div>
                            </div>
                        <div class="col-md-12 center" style="padding-bottom: 15px;">
                            <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-remove"></i> Fechar</a>
                            <button class="btn btn-primary upload-result"><i class="fa fa-upload"></i> Enviar Imagem</button>
                            <button class="btn btn-success upload-finalizar"><i class="fa fa-check"></i> Finalizar</button>
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
                        <h4 class="msg_extra text-danger"></h4>
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
                                        Essa operação irá remover o corresponde da sua lista de correspondentes, mas o mesmo poderá ser adicionado novamente, se desejar.
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
        
        {!!Minify::javascript(asset('js/geral.js'))->withFullUrl()!!}
        <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>

        <script src="{{ asset('js/plugin/moment/moment.min.js') }}"></script>

        <script src="{{ asset('js/app.config.js') }}"></script>
        <script src="{{ asset('js/data-table-custom.js') }}"></script>


        <!--[if IE 8]>
            <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
        <![endif]-->

        <script src="{{ asset('js/smartwidgets/jarvis.widget.min.js') }}"></script>
        
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

        {!!Minify::javascript(asset('js/socket.io-1.2.0.js'))->withFullUrl()!!}
        {!!Minify::javascript(asset('js/css-loader.js'))->withFullUrl()!!}

        <script src="{{ asset('js/app.min.js') }}"></script>
        <script src="{{ asset('js/plugin/jquery-form/jquery-form.min.js') }}"></script>
        <script src="{{ asset('js/plugin/bootstrap-colorselector.js') }}"></script>

        {!!Minify::javascript(asset('js/filepicker.js'))->withFullUrl()!!}
        {!!Minify::javascript(asset('js/filepicker-ui.js'))->withFullUrl()!!}
        {!!Minify::javascript(asset('js/filepicker-drop.js'))->withFullUrl()!!}
        {!!Minify::javascript(asset('js/filepicker-crop.js'))->withFullUrl()!!}
        {!!Minify::javascript(asset('js/filepicker-camera.js'))->withFullUrl()!!}
        
        <script src="{{ asset('js/plugin/morris/raphael.min.js') }}"></script>
        <script src="{{ asset('js/plugin/morris/morris.min.js') }}"></script>
        
        @yield('script')
        <script type="text/javascript">
        
        $(document).ready(function() {
        
        {{--

            var hostname = document.location.hostname;  

            var socket = io.connect('https://'+hostname+':3000',{secure: true},verify=false);
            socket.on("notificacao:App\\Events\\EventNotification", function(message){

                cod_conta = $('meta[name="conta"]').attr('content');
                path = window.location.protocol + "//" + window.location.host + "/dmk/";

                if(message.data.canal == 'notificacao'){

                    if(message.data.conta == cod_conta){
                        
                        $('.badge-count').html(message.data.total);
                        $(".notification-body > li").remove();

                        console.log(message.data.mensagens.length);

                        for (var i = 0; i < message.data.mensagens.length; i++) {
                            
                            item = '<li>'+
                                                    '<span class="unread">'+
                                                        '<a href="'+message.data.mensagens[i].url+'" class="msg">'+
                                                            '<img src="'+path+'public/img/users/'+message.data.mensagens[i].img+'" alt="" class="air air-top-left margin-top-5" width="40" height="40" />' +
                                                            '<span class="from">'+message.data.mensagens[i].remetente+'<i class="icon-paperclip"></i></span>'+
                                                            '<time>'+message.data.mensagens[i].data+'</time>'+
                                                            '<span class="subject">Processo '+message.data.mensagens[i].processo+' </span>'+
                                                            '<span class="msg-body">'+message.data.mensagens[i].mensagem+'</span>' +
                                                        '</a>' +
                                                    '</span>'+
                                                '</li>';
                                                   
                            $('.notification-body').append(item);

                        }
                    }
                }

            });

            --}}

        });

        // DO NOT REMOVE : GLOBAL FUNCTIONS!

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            }
        });


        $uploadCrop = $('#upload-demo').croppie({
            enableExif: true,
            viewport: {
                width: 340,
                height: 340,
                type: 'circle'
            },
            boundary: {
                width: 350,
                height: 350
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


        })

        </script>

    </body>
</html>
