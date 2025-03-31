@extends('layouts.register')
@section('content')
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin: 0 auto;">
        <style>
            .banner {
                text-align: center;
                padding: 50px 20px;
                margin: 20px 0;
                border-radius: 10px;
                color: white;
            }
            .office { background-color: rgba(51, 122, 183, 0.8); } /* Azul com opacidade */
            .client { background-color: rgba(92, 184, 92, 0.8); } /* Verde com opacidade */
            .correspondent { background-color: rgba(240, 173, 78, 0.8); } /* Laranja com opacidade */
            .btn-custom {
                margin-top: 20px;
                padding: 10px 20px;
                font-size: 16px;
            }
            .btn-office { background-color: #337ab7; border-color: #337ab7; } /* Azul sólido */
            .btn-client { background-color: #5cb85c; border-color: #5cb85c; } /* Verde sólido */
            .btn-correspondent { background-color: #f0ad4e; border-color: #f0ad4e; } /* Laranja sólido */
        </style>
		<div class="container">
            <h1 class="text-center" style="margin-top: 30px;">Escolha seu Perfil</h1>
            
            <!-- Banner para Escritório -->
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="banner office">
                        <h2><strong>Escritório</strong></h2>
                        <p>Você é um escritório de advocacia? Clique abaixo para acessar.</p>
                        <a href="{{ url('conta/perfil/escritorio') }}" class="btn btn-primary btn-custom"><i class="fa fa-lg fa-fw fa-archive"></i> Acessar como Escritório</a>
                    </div>
                </div>
        
                <!-- Banner para Cliente -->
                <div class="col-md-4 col-sm-12">
                    <div class="banner client">
                        <h2><strong>Cliente</strong></h2>
                        <p>Você é um cliente? Clique abaixo para acessar seus serviços.</p>
                        <a href="{{ url('conta/perfil/cliente') }}" class="btn btn-success btn-custom"><i class="fa fa-lg fa-fw fa-user"></i> Acessar como Cliente</a>
                    </div>
                </div>
        
                <!-- Banner para Correspondente -->
                <div class="col-md-4 col-sm-12">
                    <div class="banner correspondent">
                        <h2><strong>Correspondente</strong></h2>
                        <p>Você é um correspondente jurídico? Clique abaixo para acessar.</p>
                        <a href="{{ url('conta/perfil/correspondente') }}" class="btn btn-warning btn-custom"><i class="fa fa-lg fa-fw fa-legal"></i> Acessar como Correspondente</a>
                    </div>
                </div>
            </div>
        </div>		
	</div>
@endsection