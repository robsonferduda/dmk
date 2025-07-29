@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Listar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <button class="btn btn-success pull-right header-btn" data-toggle="modal" data-target="#modalNovoCorrespondente"><i class="fa fa-plus"></i> Novo</button>   
            <button class="btn btn-default pull-right header-btn" data-toggle="modal" data-target="#modalConviteCorrespondente"><i class="fa fa-send"></i> Enviar Convite</button> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="row">
            <div class="col-md-12">
            @if(isset($correspondentes))
                @foreach($correspondentes as $correspondente)
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="well shadow-hover" style="border-radius: 10px; padding: 15px; background: #fff; display: flex; gap: 15px;">
                            
                            {{-- Foto --}}
                            <div style="flex: 0 0 80px;">
                                <figure style="text-align: center;">
                                    <img src="{{ (!empty($correspondente->entidade) && file_exists('public/img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png')) 
                                            ? asset('img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png') 
                                            : asset('img/users/user.png') }}"
                                        alt="Foto de Perfil" 
                                        class="img-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #ccc;">
                                    <button class="btn btn-success btn-xs" style="margin-top: 8px;">
                                        <i class="fa fa-send"></i> Convidar
                                    </button>
                                </figure>
                            </div>

                            {{-- Informações --}}
                            <div style="flex: 1;">
                                <h5 style="margin-top: 0; margin-bottom: 5px;">
                                    <strong>{{ $correspondente->nm_razao_social_con }}</strong>
                                </h5>
                                <p style="margin: 0 0 5px; font-size: 13px;">
                                    <i class="fa fa-phone"></i> (99) 99999-9999
                                </p>
                                <p style="margin: 0 0 5px; font-size: 13px;">
                                    <i class="fa fa-envelope"></i> 
                                    {{ $correspondente->entidade->usuario->email ?? 'Não informado' }}
                                </p>
                                <p style="margin: 0; font-size: 13px;">
                                    <i class="fa fa-map-marker"></i> Comarca: Florianópolis
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
            @endif
            <div class="col-md-12">
        </article>

    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

    });
</script>
@endsection