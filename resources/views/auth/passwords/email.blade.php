@extends('layouts.guest')
@section('content')
    <form class="smart-form client-form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}
            <header><i class="fa fa-lock"></i> Recuperar Senha</header>
            <fieldset style="padding-top: 8px;">       
                <section>
                    <label class="label">Email</label>
                    <label class="input"> <i class="icon-append fa fa-envelope"></i>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                        <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Email informado no momento do cadastro</b> </label>

                        @if ($errors->has('email'))
                            <span class="help-block" style="padding: 5px 8px;"><strong>{{ $errors->first('email') }}</strong></span>
                        @endif

                    <div class="note" style="text-align: center;"><a href="{{ url('login') }}">Voltar para o sistema</a></div>
                </section>
            </fieldset>

            @if(session('status'))
                <div class="text-success" role="alert" style="margin: 0px 15px; margin-bottom: 15px;">
                    {{ session('status') }}
                </div>
            @endif
            <footer class="center" style="text-align: center;">
                <button style="float: none" type="submit" class="btn btn-success"><i class="fa fa-send"></i> Enviar</button>
            </footer>
    </form>
@endsection
