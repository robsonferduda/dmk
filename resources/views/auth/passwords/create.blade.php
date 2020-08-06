@extends('layouts.guest')
@section('content')
    <form class="smart-form client-form" method="POST" action="{{ route('password.novo') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
            <header><i class="fa fa-lock"></i> Recuperar Senha</header>
            <fieldset style="padding-top: 8px;">  
                <section>
                    @if ($errors->has('email'))
                        <span class="help-block text-danger"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                    <label class="label">Senha</label>
                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Digite sua senha</b> </label>

                        @if ($errors->has('password'))
                            <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                        @endif
                </section>
                <section>
                    <label class="label">Confirmação de Senha</label>
                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Por segurança, confirme sua senha</b> </label>
                </section>
            </fieldset>
            <footer style="text-align: center;">
                <button style="float: none" type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar</button>
            </footer>
    </form>
@endsection