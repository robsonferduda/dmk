@extends('layouts.guest')
@section('content')
    <form class="smart-form client-form" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
        <header><i class="fa fa-lock"></i> Login</header>
        <fieldset style="padding-top: 8px;">  
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="img/logo.png" style="width: 25%;" alt="Sistema DMK"> 
            <div>      
            <section>
                <label class="label">E-mail</label>
                <label class="input"> <i class="icon-append fa fa-user"></i>
                    <input type="email" name="email">
                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i>  Digite seu usuário (email)</b></label>

                    @if ($errors->has('email'))
                        <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
            </section>
            <section>
                <label class="label">Senha</label>
                <label class="input"> <i class="icon-append fa fa-lock"></i>
                    <input type="password" name="password">
                    <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Digite sua senha</b> </label>

                    @if ($errors->has('password'))
                        <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                <div class="note"><a href="{{ url('password/reset') }}">Esqueceu sua senha?</a></div>
            </section>
        </fieldset>
        <footer>
            <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> Entrar</button>
        </footer>
    </form>
@endsection