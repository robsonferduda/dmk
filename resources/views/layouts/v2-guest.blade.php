<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Acesso') · Operia Law</title>
  <meta name="robots" content="noindex, nofollow">

  <link href="{{ asset('marca/operia/favicon-32.png') }}" rel="icon" sizes="32x32">
  <link href="{{ asset('marca/operia/favicon-180.png') }}" rel="apple-touch-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

  <link href="{{ asset('v2/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  @yield('stylesheet')
  <link href="{{ asset('v2/css/main.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/css/dmk.css') }}?v={{ filemtime(public_path('v2/css/dmk.css')) }}" rel="stylesheet">
</head>

<body class="dmk-auth">
  <main class="dmk-auth-main">
    <div class="dmk-auth-box @yield('box_class')">
      <div class="dmk-auth-brand">
        <span class="dmk-auth-logo"><img src="{{ asset('marca/operia/horizontal.svg') }}" alt="Operia Law"></span>
      </div>

      @include('layouts.v2-messages')
      @yield('content')

      <p class="dmk-auth-footer">&copy; {{ date('Y') }} Operia Law</p>
    </div>
  </main>

  <script src="{{ asset('v2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('v2/js/theme.js') }}"></script>
  @yield('script')
</body>
</html>
