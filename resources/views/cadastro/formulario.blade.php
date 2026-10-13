@extends('layouts.v2-guest')

@section('title', 'Atualização cadastral')
@section('box_class', 'dmk-auth-box-largo')

@section('stylesheet')
  <link href="{{ asset('v2/vendor/choices.js/choices.min.css') }}" rel="stylesheet">
@endsection

@php
    $campo = function ($nome) use ($errors) { return $errors->has($nome) ? ' is-invalid' : ''; };
    $estadoSigla = $estados->pluck('sg_estado_est', 'cd_estado_est');
@endphp

@section('content')
<form id="form-cadastro" method="POST" action="{{ url('atualizar-dados/' . $chave) }}" novalidate
      data-cidades="{{ url('atualizar-dados/' . $chave . '/cidades') }}"
      data-estados='@json($estadoSigla)'
      data-comarcas='@json($comarcas)'>
    @csrf

    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h1 class="dmk-auth-title">Atualização cadastral</h1>
            <p class="dmk-auth-subtitle mb-0">
                Confira os dados abaixo, complete o que faltar e clique em <strong>Enviar dados</strong> no final da página.
                Eles serão usados no contrato de prestação de serviços e nos pagamentos.
            </p>
        </div>
    </div>

    {{-- Identificação --}}
    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h2 class="dmk-form-secao"><i class="bi bi-person-vcard"></i> Identificação</h2>
            <div class="dmk-foto-perfil mb-3">
                <div class="dmk-foto-perfil-imagem" id="foto-previa" data-atual="{{ $dados['foto_atual'] }}">
                    @if(!empty($dados['foto']) || $dados['foto_atual'])
                        <img src="{{ ($dados['foto'] ?? '') ?: $dados['foto_atual'] }}" alt="Foto de perfil">
                    @else
                        <i class="bi bi-person"></i>
                    @endif
                </div>
                <div>
                    <span class="form-label d-block mb-1">Foto de perfil <span class="text-muted small">(opcional)</span></span>
                    <label class="btn btn-outline-primary btn-sm mb-0" for="foto-arquivo">
                        <i class="bi bi-camera me-1"></i> {{ $dados['foto_atual'] ? 'Trocar foto' : 'Escolher foto' }}
                    </label>
                    <input type="file" id="foto-arquivo" accept="image/jpeg,image/png,image/webp" class="d-none">
                    <button type="button" class="btn btn-link btn-sm text-danger {{ empty($dados['foto']) ? 'd-none' : '' }}" id="foto-desfazer">Desfazer</button>
                    <input type="hidden" name="foto" id="foto" value="{{ $dados['foto'] ?? '' }}">
                    <div class="form-text" id="foto-situacao">Uma foto de rosto, em JPG ou PNG. Aparece no seu cadastro no sistema do DMK.</div>
                    @if($errors->has('foto'))<div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>@endif
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome completo ou razão social</label>
                    <input type="text" id="nome" name="nome" value="{{ $dados['nome'] }}" maxlength="150" class="form-control{{ $campo('nome') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('nome') }}</div>
                </div>
                <div class="col-md-6">
                    <label for="documento" class="form-label">CPF ou CNPJ</label>
                    <input type="text" id="documento" name="documento" value="{{ $dados['documento'] }}" inputmode="numeric" data-mascara="documento" class="form-control{{ $campo('documento') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('documento') }}</div>
                </div>
                <div class="col-md-6">
                    <label for="rg" class="form-label">RG <span class="text-muted small">(se for CNPJ, do responsável)</span></label>
                    <input type="text" id="rg" name="rg" value="{{ $dados['rg'] }}" maxlength="30" class="form-control{{ $campo('rg') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('rg') }}</div>
                </div>
                <div class="col-12">
                    <span class="form-label d-block">Você atua como</span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="atuacao" id="atuacao-advogado" value="advogado" {{ $dados['atuacao'] === 'advogado' ? 'checked' : '' }}>
                        <label class="form-check-label" for="atuacao-advogado">Advogado(a)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="atuacao" id="atuacao-preposto" value="preposto" {{ $dados['atuacao'] === 'preposto' ? 'checked' : '' }}>
                        <label class="form-check-label" for="atuacao-preposto">Preposto(a)</label>
                    </div>
                    @if($errors->has('atuacao'))<div class="invalid-feedback d-block">{{ $errors->first('atuacao') }}</div>@endif
                </div>
                <div class="col-md-4">
                    <label for="oab_uf" class="form-label">Seccional da OAB <span class="text-muted small" data-oab-opcional>(opcional para preposto)</span></label>
                    <select id="oab_uf" name="oab_uf" class="form-select{{ $campo('oab_uf') }}">
                        <option value="">Selecione</option>
                        @foreach($ufs as $uf)
                            <option value="{{ $uf }}" {{ $dados['oab_uf'] === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ $errors->first('oab_uf') }}</div>
                </div>
                <div class="col-md-8">
                    <label for="oab_numero" class="form-label">Número da OAB <span class="text-muted small" data-oab-opcional>(opcional para preposto)</span></label>
                    <input type="text" id="oab_numero" name="oab_numero" value="{{ $dados['oab_numero'] }}" maxlength="20" placeholder="Ex.: 12.345" class="form-control{{ $campo('oab_numero') }}">
                    <div class="invalid-feedback">{{ $errors->first('oab_numero') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Endereço --}}
    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h2 class="dmk-form-secao"><i class="bi bi-geo-alt"></i> Endereço</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" id="cep" name="cep" value="{{ $dados['cep'] }}" inputmode="numeric" data-mascara="cep" class="form-control{{ $campo('cep') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('cep') }}</div>
                    <div class="form-text" id="cep-situacao">O endereço é preenchido pelo CEP.</div>
                </div>
                <div class="col-md-8">
                    <label for="logradouro" class="form-label">Endereço</label>
                    <input type="text" id="logradouro" name="logradouro" value="{{ $dados['logradouro'] }}" maxlength="200" placeholder="Rua, avenida..." class="form-control{{ $campo('logradouro') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('logradouro') }}</div>
                </div>
                <div class="col-md-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" id="numero" name="numero" value="{{ $dados['numero'] }}" maxlength="20" class="form-control{{ $campo('numero') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('numero') }}</div>
                </div>
                <div class="col-md-4">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" id="complemento" name="complemento" value="{{ $dados['complemento'] }}" maxlength="100" class="form-control{{ $campo('complemento') }}">
                    <div class="invalid-feedback">{{ $errors->first('complemento') }}</div>
                </div>
                <div class="col-md-5">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" id="bairro" name="bairro" value="{{ $dados['bairro'] }}" maxlength="100" class="form-control{{ $campo('bairro') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('bairro') }}</div>
                </div>
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="">Selecione</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->cd_estado_est }}" {{ (string) $dados['estado'] === (string) $estado->cd_estado_est ? 'selected' : '' }}>{{ $estado->nm_estado_est }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="cidade" class="form-label">Cidade</label>
                    <div class="{{ $campo('cidade') }}">
                        <select id="cidade" name="cidade" data-selecionada="{{ $dados['cidade'] }}"></select>
                    </div>
                    <div class="invalid-feedback">{{ $errors->first('cidade') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contato --}}
    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h2 class="dmk-form-secao"><i class="bi bi-telephone"></i> Contato</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="telefone" class="form-label">Telefone com DDD</label>
                    <input type="text" id="telefone" name="telefone" value="{{ $dados['telefone'] }}" inputmode="tel" data-mascara="telefone" class="form-control{{ $campo('telefone') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('telefone') }}</div>
                </div>
                <div class="col-md-4">
                    <label for="whatsapp" class="form-label">WhatsApp <span class="text-muted small">(se for outro)</span></label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ $dados['whatsapp'] }}" inputmode="tel" data-mascara="telefone" class="form-control{{ $campo('whatsapp') }}">
                    <div class="invalid-feedback">{{ $errors->first('whatsapp') }}</div>
                    <div class="form-text">Usado nos lembretes de audiência.</div>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ $dados['email'] }}" maxlength="150" class="form-control{{ $campo('email') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dados bancários --}}
    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h2 class="dmk-form-secao"><i class="bi bi-bank"></i> Dados bancários</h2>
            <div class="row g-3">
                <div class="col-md-7">
                    <label for="titular" class="form-label">Titular da conta</label>
                    <input type="text" id="titular" name="titular" value="{{ $dados['titular'] }}" maxlength="150" class="form-control{{ $campo('titular') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('titular') }}</div>
                </div>
                <div class="col-md-5">
                    <label for="titular_documento" class="form-label">CPF ou CNPJ do titular</label>
                    <input type="text" id="titular_documento" name="titular_documento" value="{{ $dados['titular_documento'] }}" inputmode="numeric" data-mascara="documento" class="form-control{{ $campo('titular_documento') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('titular_documento') }}</div>
                </div>
                <div class="col-md-8">
                    <label for="banco" class="form-label">Banco</label>
                    <div class="{{ $campo('banco') }}">
                        <select id="banco" name="banco">
                            <option value="">Selecione o banco</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco->cd_banco_ban }}" {{ (string) $dados['banco'] === (string) $banco->cd_banco_ban ? 'selected' : '' }}>{{ $banco->cd_banco_ban }} - {{ $banco->nm_banco_ban }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="invalid-feedback">{{ $errors->first('banco') }}</div>
                </div>
                <div class="col-md-4">
                    <span class="form-label d-block">Tipo de conta</span>
                    @foreach([\App\Enums\TipoConta::CORRENTE => 'Corrente', \App\Enums\TipoConta::POUPANCA => 'Poupança'] as $valor => $rotulo)
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="radio" name="tipo_conta" id="tipo-conta-{{ $valor }}" value="{{ $valor }}" {{ (string) $dados['tipo_conta'] === (string) $valor ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipo-conta-{{ $valor }}">{{ $rotulo }}</label>
                        </div>
                    @endforeach
                    @if($errors->has('tipo_conta'))<div class="invalid-feedback d-block">{{ $errors->first('tipo_conta') }}</div>@endif
                </div>
                <div class="col-md-4">
                    <label for="agencia" class="form-label">Agência</label>
                    <input type="text" id="agencia" name="agencia" value="{{ $dados['agencia'] }}" maxlength="20" class="form-control{{ $campo('agencia') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('agencia') }}</div>
                </div>
                <div class="col-md-4">
                    <label for="conta" class="form-label">Conta com dígito</label>
                    <input type="text" id="conta" name="conta" value="{{ $dados['conta'] }}" maxlength="30" class="form-control{{ $campo('conta') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('conta') }}</div>
                </div>
                <div class="col-md-4">
                    <label for="pix" class="form-label">Chave PIX</label>
                    <input type="text" id="pix" name="pix" value="{{ $dados['pix'] }}" maxlength="150" class="form-control{{ $campo('pix') }}" required>
                    <div class="invalid-feedback">{{ $errors->first('pix') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Comarcas --}}
    <div class="card dmk-auth-card mb-3">
        <div class="card-body">
            <h2 class="dmk-form-secao"><i class="bi bi-map"></i> Comarcas de atendimento</h2>

            <p class="small text-muted mb-2">Comarca de origem (onde você reside ou tem escritório):</p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="origem_estado" class="form-label">Estado</label>
                    <select id="origem_estado" name="origem_estado" class="form-select">
                        <option value="">Selecione</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->cd_estado_est }}" {{ (string) $dados['origem_estado'] === (string) $estado->cd_estado_est ? 'selected' : '' }}>{{ $estado->nm_estado_est }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="origem" class="form-label">Comarca de origem</label>
                    <div class="{{ $campo('origem') }}">
                        <select id="origem" name="origem" data-selecionada="{{ $dados['origem'] }}"></select>
                    </div>
                    <div class="invalid-feedback">{{ $errors->first('origem') }}</div>
                </div>
            </div>

            <p class="small text-muted mb-2">Demais comarcas em que pode atender pelos valores já acordados na parceria:</p>
            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-4">
                    <label for="comarca_estado" class="form-label">Estado</label>
                    <select id="comarca_estado" class="form-select">
                        <option value="">Selecione</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->cd_estado_est }}">{{ $estado->nm_estado_est }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="comarca_cidade" class="form-label">Comarca</label>
                    <select id="comarca_cidade"></select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="button" class="btn btn-outline-primary" id="comarca-adicionar"><i class="bi bi-plus-lg me-1"></i> Adicionar</button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-link btn-sm p-0" id="comarca-todas" disabled>
                        <i class="bi bi-plus-circle me-1"></i> Adicionar todas as comarcas do estado selecionado
                    </button>
                </div>
            </div>

            <div id="comarcas-lista" class="dmk-comarcas"></div>
            <input type="hidden" id="comarcas-campos" name="comarcas" value="{{ implode(',', $dados['comarcas']) }}">
            @if($errors->has('comarcas'))<div class="invalid-feedback d-block">{{ $errors->first('comarcas') }}</div>@endif
        </div>
    </div>

    <div class="card dmk-auth-card">
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input{{ $campo('confirmacao') }}" type="checkbox" name="confirmacao" id="confirmacao" value="1" required>
                <label class="form-check-label" for="confirmacao">
                    Confirmo que os dados acima estão corretos e autorizo seu uso no contrato de prestação de serviços com o DMK Advogados.
                </label>
                <div class="invalid-feedback">{{ $errors->first('confirmacao') }}</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2" id="enviar">
                <i class="bi bi-send me-1"></i> Enviar dados
            </button>
            <p class="small text-muted text-center mt-2 mb-0">
                Dúvidas? Responda o e-mail que recebeu ou escreva para
                <a href="mailto:{{ config('cadastro.responder_para.email') }}">{{ config('cadastro.responder_para.email') }}</a>.
            </p>
        </div>
    </div>
</form>
@endsection

@section('script')
  <script src="{{ asset('v2/vendor/choices.js/choices.min.js') }}"></script>
  <script src="{{ asset('v2/js/dmk.js') }}"></script>
  <script src="{{ asset('v2/js/formulario-cadastro.js') }}?v={{ filemtime(public_path('v2/js/formulario-cadastro.js')) }}"></script>
@endsection
