@extends('layouts.logado')
@section('content')
    <div style="padding: 15px 10px;">
        <div class="well">
            <h5>Por questões de segurança, pedimos que informe a senha cadastrada para o usário informado e seu perfil de <strong>{{ $nivel->dc_nome_padronizado_niv }}</strong> </h5>
        </div>   

        <form class="smart-form client-form" method="POST" action="{{ route('login-perfil') }}">
        {{ csrf_field() }}
            <fieldset style="padding-top: 8px;"> 
                <input type="hidden" name="cd_nivel_niv" value="{{ $nivel->cd_nivel_niv }}">       
                <section>
                    <label class="label">Senha</label>
                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                        <input type="password" name="password">
                        <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Digite sua senha</b> </label>
                </section>
            </fieldset>
            <footer>
                <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> Validar Senha</button>
            </footer>
        </form>    
       
    </div>
@endsection