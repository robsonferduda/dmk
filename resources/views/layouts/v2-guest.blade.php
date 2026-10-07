<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Acesso') · DMK</title>
  <meta name="robots" content="noindex, nofollow">

  <link href="{{ asset('img/favicon/favicon-32x32.png') }}" rel="icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

  <link href="{{ asset('v2/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/css/main.css') }}" rel="stylesheet">
  <link href="{{ asset('v2/css/dmk.css') }}?v={{ filemtime(public_path('v2/css/dmk.css')) }}" rel="stylesheet">
</head>

<body class="dmk-auth">
  <main class="dmk-auth-main">
    <div class="dmk-auth-box">
      <div class="dmk-auth-brand">
        <span class="dmk-auth-logo"><img src="{{ asset('img/legal.png') }}" alt=""></span>
        <span>DMK Advogados</span>
      </div>

      @include('layouts.v2-messages')
      @yield('content')

      <p class="dmk-auth-footer">&copy; {{ date('Y') }} DMK Advogados</p>
    </div>
  </main>

  <script src="{{ asset('v2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('v2/js/theme.js') }}"></script>
</body>
</html>
