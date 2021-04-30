@extends('layouts.register')
@section('content')
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin: 0 auto; float: none;">
	<div class="well no-padding">
		{!! Form::open(['id' => 'frm-add-conta', 'url' => 'correspondente/cadastro', 'class' => 'smart-form client-form']) !!}
			<header>
				<strong>Cadastro de Correspondentes</strong>
			</header>
			<fieldset>
				<section>
					<div class="text-danger">
						@if ($errors->any())
							@foreach($errors->all() as $error)
								{!! $error !!} <br />
							@endforeach
						@endif
					</div>
					</section>

					@if(Session::get('flag_convite'))
						<input type="hidden" name="token" value="{{ Session::get('token') }}">
						<input type="hidden" name="conta" value="{{ Session::get('conta') }}">
					@endif
					
					<section>
						<label class="input"> <i class="icon-append fa fa-user"></i>
						<input type="text" name="nm_razao_social_con" placeholder="Nome" value="{{ old('nm_razao_social_con') }}">
						<b class="tooltip tooltip-bottom-right">Nome Completo</b> </label>
					</section>
					<section>
						<label class="input"> <i class="icon-append fa fa-envelope"></i>
						<input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
						<b class="tooltip tooltip-bottom-right">Informe seu email</b> </label>
					</section>
					<section>
						<label class="input"> <i class="icon-append fa fa-lock"></i>
						<input type="password" name="password" id="password" value="{{ old('password') }}" placeholder="Senha">
						<b class="tooltip tooltip-bottom-right">Informe uma senha segura</b> </label>
					</section>
					<section>
						<label class="input"> <i class="icon-append fa fa-lock"></i>
						<input type="password" name="passwordConfirm" id="passwordConfirm" value="{{ old('password') }}" placeholder="Confirmar Senha">
						<b class="tooltip tooltip-bottom-right">Por segurança, repita a senha escolhida</b> </label>
					</section>
				</fieldset>
				<fieldset>
					<section>
						<label class="checkbox">
						<input type="checkbox" name="terms" id="terms">
						<i></i>Eu aceito os <a href="#" data-toggle="modal" data-target="#myModal"> Termos e Condições</a></label>
					</section>
				</fieldset>
				<footer>
					{!! Captcha::display() !!}
					<button type="submit" class="btn btn-success">
						<i class="fa fa-check"></i> Cadastrar
					</button>
				</footer>
		{!! Form::close() !!} 
	</div>	
</div>
@endsection