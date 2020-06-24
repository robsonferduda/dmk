@extends('layouts.guest')
@section('content')
    <form class="smart-form client-form" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
        <header><i class="fa fa-lock"></i> Login</header>
        <fieldset style="padding-top: 8px;">  
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="{{ asset('img/users/user.png') }}" style="width: 25%;" alt="Imagem de Perfil"> 
            <div><hr style="margin: 10px 0px 10px 0px;" />
            <section>
                <label class="label">Selecione um perfil</label>
                <label class="select">
                    <select name="nivel" required="required">
                        <option value="">Selecione um perfil</option>
                        @foreach(\App\Nivel::all() as $nivel)
                            <option value="{{ $nivel->cd_nivel_niv }}" {{ ((old('nivel') and old('nivel') == $nivel->cd_nivel_niv) or (session('nivel_url') and session('nivel_url') == $nivel->cd_nivel_niv)) ? 'selected="selected"' : ''  }}>{{ $nivel->dc_nome_padronizado_niv }}</option>
                        @endforeach
                    </select><i></i>
                </label>
            </section>      
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

                <div class="note" style="font-size: 14px;">
                    <span class="text-warning">
                        @if(\Session::has('flash_notification'))
                            @foreach (Session::get('flash_notification') as $message)
                                {!! $message['message'] !!}
                            @endforeach
                        @endif 
                    </span>
                </div>

                <div class="note"><a href="{{ url('password/reset') }}">Esqueceu sua senha?</a></div>
                <div class="note"><a href="{{ url('conta/ativacao') }}">Primeiro acesso</a></div>
            </section>

        </fieldset>
        <footer>
            <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> Entrar</button>
        </footer>
    </form>
@endsection