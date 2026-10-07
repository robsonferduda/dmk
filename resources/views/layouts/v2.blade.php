<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'DMK') · {{ env('APP_NAME') }}</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="token" content="{{ csrf_token() }}">
  <meta name="base-url" content="{{ url('/') }}">

  <link href="{{ asset('img/favicon/favicon-32x32.png') }}" rel="icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Nunito+Sans:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

  <link href="{{ asset('v2/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/vendor/choices.js/choices.min.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/css/main.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/css/dmk.css') }}?v={{ filemtime(public_path('v2/css/dmk.css')) }}" rel="stylesheet">
  @yield('stylesheet')
</head>

@php
    $usuarioV2 = Auth::user();
    $contaV2 = \App\Conta::find(Session::get('SESSION_CD_CONTA'));
    $fotoV2 = ($usuarioV2 && file_exists(public_path('img/users/ent'.$usuarioV2->cd_entidade_ete.'.png')))
        ? asset('img/users/ent'.$usuarioV2->cd_entidade_ete.'.png')
        : asset('img/users/user.png');
    $menuAtivoV2 = trim($__env->yieldContent('menu'));
@endphp

<body>
  <header class="header">
    <div class="header-left">
      <button class="sidebar-toggle" title="Menu" aria-label="Abrir/fechar menu">
        <span class="menu-lines" aria-hidden="true">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>

      <a href="{{ url('v2/correspondentes') }}" class="header-brand" aria-label="DMK">
        <span class="header-logo">
          <img src="{{ asset('img/legal.png') }}" alt="DMK">
        </span>
        <span class="header-context">
          <strong class="header-context-title">DMK <span class="dmk-v2-badge">v2</span></strong>
        </span>
      </a>
    </div>

    <div class="header-right">
      <div class="header-actions-desktop">
        <a href="{{ url($__env->yieldContent('classico', 'home')) }}" class="header-action dmk-classic-link" title="Voltar para a versão clássica">
          <i class="bi bi-box-arrow-up-left"></i>
          <span class="d-none d-xl-inline ms-1">Versão clássica</span>
        </a>

        <button class="header-action theme-toggle" title="Alternar tema claro/escuro">
          <i class="bi bi-moon-stars theme-icon-dark"></i>
          <i class="bi bi-sun theme-icon-light"></i>
        </button>

        <div class="header-action-wrap dropdown user-dropdown">
          <button class="dropdown-toggle user-trigger" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $fotoV2 }}" alt="Usuário" class="user-avatar">
            <div class="user-brief">
              <span class="user-name">{{ $usuarioV2 ? $usuarioV2->name : '' }}</span>
              <span class="user-role">{{ $contaV2 ? $contaV2->nm_razao_social_con : '' }}</span>
            </div>
          </button>

          <div class="dropdown-menu dropdown-menu-end user-menu">
            <div class="user-menu-header">
              <img src="{{ $fotoV2 }}" alt="Usuário" class="user-menu-avatar">
              <div class="user-menu-info">
                <div class="user-menu-name">{{ $usuarioV2 ? $usuarioV2->name : '' }}</div>
                <div class="user-menu-email">{{ $usuarioV2 ? $usuarioV2->email : '' }}</div>
              </div>
            </div>
            <div class="user-menu-body">
              <a class="user-menu-item" href="{{ url('home') }}">
                <i class="bi bi-house"></i>
                <span>Mural (versão clássica)</span>
              </a>
            </div>
            <div class="user-menu-footer">
              <a class="user-menu-logout" href="{{ route('logout') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="header-actions-mobile">
        <button class="header-action mobile-menu-toggle" title="Mais">
          <i class="bi bi-three-dots"></i>
        </button>
      </div>
    </div>
  </header>

  <div class="mobile-header-menu">
    <div class="mobile-header-menu-content">
      <button class="mobile-menu-item theme-toggle" title="Alternar tema">
        <i class="bi bi-moon-stars theme-icon-dark"></i>
        <i class="bi bi-sun theme-icon-light"></i>
        <span class="mobile-menu-label">Tema</span>
      </button>

      <a href="{{ url($__env->yieldContent('classico', 'home')) }}" class="mobile-menu-item">
        <i class="bi bi-box-arrow-up-left"></i>
        <span class="mobile-menu-label">Clássica</span>
      </a>

      <a href="{{ route('logout') }}" class="mobile-menu-item mobile-menu-item-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span class="mobile-menu-label">Sair</span>
      </a>
    </div>
  </div>

  <aside class="sidebar">
    <div class="sidebar-shell">
      <button class="sidebar-close" type="button" aria-label="Fechar menu">
        <i class="bi bi-x-lg"></i>
      </button>

      <nav class="sidebar-nav">
        <ul class="nav-menu">
          <li class="nav-item">
            <a class="nav-link" href="{{ url('home') }}">
              <span class="nav-icon"><i class="bi bi-display"></i></span>
              <span class="nav-text">Mural</span>
              <span class="nav-meta">Clássico</span>
            </a>
          </li>

          @can('correspondente.meus-correspondentes')
          <li class="nav-item">
            <a class="nav-link {{ $menuAtivoV2 === 'correspondentes' ? 'active' : '' }}" href="{{ url('v2/correspondentes') }}">
              <span class="nav-icon"><i class="bi bi-briefcase"></i></span>
              <span class="nav-text">Correspondentes</span>
              <span class="nav-meta">v2</span>
            </a>
          </li>
          @endcan

          @can('correspondente.meus-correspondentes')
          <li class="nav-item">
            <a class="nav-link {{ $menuAtivoV2 === 'atualizacao-cadastral' ? 'active' : '' }}" href="{{ url('v2/atualizacao-cadastral') }}">
              <span class="nav-icon"><i class="bi bi-envelope-check"></i></span>
              <span class="nav-text">Atualização cadastral</span>
              <span class="nav-meta">v2</span>
            </a>
          </li>
          @endcan

          @can('correspondente.categorias')
          <li class="nav-item">
            <a class="nav-link" href="{{ url('correspondente/categorias') }}">
              <span class="nav-icon"><i class="bi bi-tags"></i></span>
              <span class="nav-text">Categorias</span>
              <span class="nav-meta">Clássico</span>
            </a>
          </li>
          @endcan

          <li class="nav-item">
            <a class="nav-link" href="{{ url('processos') }}">
              <span class="nav-icon"><i class="bi bi-folder2-open"></i></span>
              <span class="nav-text">Processos</span>
              <span class="nav-meta">Clássico</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="sidebar-overlay"></div>

  <main class="main">
    <div class="main-content @yield('page-class')">
      @include('layouts.v2-messages')
      @yield('content')
    </div>

    <footer class="footer">
      <div class="footer-content">
        <div class="footer-links">
          <a href="{{ url($__env->yieldContent('classico', 'home')) }}">Versão clássica</a>
        </div>

        <div class="footer-credits">
          <div class="footer-copyright">
            &copy; {{ date('Y') }} <a href="{{ url('home') }}">DMK</a>
          </div>
          <div class="footer-copyright">
            <div class="credits">
              <!-- All the links in the footer should remain intact. -->
              <!-- You can delete the links only if you purchased the pro version. -->
              <!-- Licensing information: https://bootstrapmade.com/license/ -->
              Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </main>

  <a href="#" class="back-to-top">
    <i class="bi bi-arrow-up"></i>
  </a>

  <script src="{{ asset('v2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('v2/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('v2/vendor/choices.js/choices.min.js') }}"></script>
  <script src="{{ asset('v2/js/theme.js') }}"></script>
  <script src="{{ asset('v2/js/main.js') }}"></script>
  <script src="{{ asset('v2/js/dmk.js') }}?v={{ filemtime(public_path('v2/js/dmk.js')) }}"></script>
  @yield('script')
</body>
</html>
