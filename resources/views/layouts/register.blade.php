<!DOCTYPE html>
<html lang="en-us" id="extr-page"  style="background: #f9f9f9;">
	<head>
		<meta charset="utf-8">
		<title>{{ env('APP_NAME') }}</title>
		<meta name="robots" content="noindex">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		
		<!-- #CSS Links -->
		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/font-awesome.min.css') }}">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production-plugins.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-production.min.css') }}">
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-skins.min.css') }}">
		{!! Minify::stylesheet('/css/custom.css') !!}

		<!-- SmartAdmin RTL Support -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/smartadmin-rtl.min.css') }}"> 

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/demo.min.css') }}">

		<!-- #FAVICONS -->
		<link rel="icon" href="{{ asset('img/favicon/favicon-32x32.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('img/favicon/favicon-32x32.png') }}" type="image/x-icon">

		<!-- #GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">


	</head>
	
	<body id="login" style="background: #f9f9f9 !important;">
	
		<header id="header" style="text-align: right; padding-top: 20px;" class="header-register">
			<!--
			<span id="btn_correspondentes"><a href="{{ url('/correspondente') }}" class="btn btn-default link-cadastro-correspondente"><i class="fa fa-legal"></i> CADASTRO CORRESPONDENTES</a> </span>
			<span id="btn_login"><a href="{{ url('login') }}" class="btn btn-danger"><i class="fa fa-sign-in"></i> ACESSAR O SISTEMA</a> </span>
			-->
		</header>

		<div id="main" role="main" style="background: #f9f9f9 !important;">
			<div id="content" class="container">
				<div class="row" style="margin: 0 auto;">					
					@yield('content')
				</div>
			</div>
		</div>

		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">Termos e Condições</h4>
					</div>
					<div class="modal-body custom-scroll terms-body">
						
 						<div id="left">
            				<h1>TERMOS E CONDIÇÕES</h1>
            				<h2><strong>Introdução</strong></h2>
				            <p>
				            	Nulla dapibus leo eu purus tempor facilisis. Nullam fringilla aliquet consequat. Duis bibendum ipsum eget sapien porta, sed ultricies neque tincidunt. Maecenas eleifend metus eu nisi sodales, sit amet sagittis purus iaculis. Donec auctor et enim sit amet varius. Morbi eu nisi iaculis, blandit tortor eget, aliquam urna. Nulla scelerisque porta nulla, sed sagittis turpis. Curabitur nec pharetra nunc. Sed pretium nisi quis sem malesuada rutrum quis eu ante. Mauris massa mauris, ornare a volutpat vel, facilisis ut elit. Nullam scelerisque tortor vel mi finibus, vitae ultrices turpis fermentum. Donec porttitor quam eget tincidunt accumsan. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse hendrerit vitae nulla sit amet placerat.
				            </p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">
								<i class="fa fa-times"></i> Recusar e Sair
							</button>
							<button type="button" class="btn btn-primary" id="i-agree">
								<i class="fa fa-check"></i> Eu Aceito!
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="js/plugin/pace/pace.min.js"></script>

	    <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

		<!-- IMPORTANT: APP CONFIG -->
		<script src="js/app.config.js"></script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events 		
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->		
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>
		
		<!-- JQUERY MASKED INPUT -->
		<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>
		
		<!--[if IE 8]>
			
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
			
		<![endif]-->

		<!-- MAIN APP JS FILE -->
		<script src="js/app.min.js"></script>

		<script>
			runAllForms();
			
			// Model i agree button
			$("#i-agree").click(function(){
				$this=$("#terms");
				if($this.checked) {
					$('#myModal').modal('toggle');
				} else {
					$this.prop('checked', true);
					$('#myModal').modal('toggle');
				}
			});
			
			// Validation
			$(function() {
				// Validation
				$("#frm-add-conta").validate({

					// Rules for form validation
					rules : {
						nm_razao_social_con : {
							required : true
						},
						email : {
							required : true,
							email : true
						},
						password : {
							required : true,
							minlength : 3,
							maxlength : 20
						},
						passwordConfirm : {
							required : true,
							minlength : 3,
							maxlength : 20,
							equalTo : '#password'
						},
						terms : {
							required : true
						}
					},

					// Messages for form validation
					messages : {
						nm_razao_social_con : {
							required : 'Digite seu nome'
						},
						email : {
							required : 'Email obrigatório',
							email : 'Digite um email válido'
						},
						password : {
							required : 'Digite sua senha'
						},
						passwordConfirm : {
							required : 'Confirme a senha',
							equalTo : 'As senhas informadas são diferentes'
						},
						terms : {
							required : 'Você deve aceitar os Termos e Condições'
						}
					},

					// Ajax form submition
					submitHandler : function(form) {
						$(form).ajaxSubmit({
							success : function() {
								$("#smart-form-register").addClass('submited');
							}
						});
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});

			});
		</script>
	</body>
</html>