@extends('layouts.v2')

@php
    $nome = trim($vinculo->nm_conta_correspondente_ccr ?: 'Sem nome');
    $tipoPessoa = (int) old('cd_tipo_pessoa_tpp', $vinculo->cd_tipo_pessoa_tpp ?: 1);
    $cidadeEndereco = old('cd_cidade_cde', optional($endereco)->cd_cidade_cde);
    $estadoEndereco = old('cd_estado_est', optional(optional($endereco)->cidade)->cd_estado_est);
@endphp

@section('title', 'Editar '.$nome)
@section('menu', 'correspondentes')
@section('classico', 'correspondente/ficha/'.\Crypt::encrypt($vinculo->cd_correspondente_cor))
@section('page-class', 'page-users-edit')

@section('content')
<div class="page-users-edit">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes') }}">Correspondentes</a></li>
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes/'.$idSafe) }}">{{ $nome }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <div class="ue-shell-head mb-3">
        <div>
            <h1 class="page-title mb-1">Editar correspondente</h1>
            <p class="ue-shell-subtitle">Os dados ficam no vínculo com o escritório e também são vistos pelo correspondente.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ url('v2/correspondentes/'.$idSafe) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
            <button type="submit" form="form-correspondente" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Salvar alterações</button>
        </div>
    </div>

    <form id="form-correspondente" method="POST" action="{{ url('correspondente/editar') }}" autocomplete="off">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="v2" value="1">
        <input type="hidden" name="conta" value="{{ $vinculo->cd_correspondente_cor }}">
        <input type="hidden" name="entidade" value="{{ $vinculo->cd_entidade_ete }}">
        <input type="hidden" name="telefones" id="telefones" value="[]">
        <input type="hidden" name="emails" id="emails" value="[]">
        <input type="hidden" name="registrosBancarios" id="registrosBancarios" value="[]">

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card ue-shell-card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-1"></i> Dados básicos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label d-block">Tipo de pessoa</label>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="cd_tipo_pessoa_tpp" id="tp-fisica" value="1" {{ $tipoPessoa !== 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="tp-fisica"><i class="bi bi-person me-1"></i> Pessoa física</label>
                                    <input type="radio" class="btn-check" name="cd_tipo_pessoa_tpp" id="tp-juridica" value="2" {{ $tipoPessoa === 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="tp-juridica"><i class="bi bi-building me-1"></i> Pessoa jurídica</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Atuação</label>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="fl_advogado_con" id="at-advogado" value="1" {{ $flAdvogado === true ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success btn-sm" for="at-advogado"><i class="bi bi-mortarboard me-1"></i> Advogado</label>
                                    <input type="radio" class="btn-check" name="fl_advogado_con" id="at-preposto" value="0" {{ $flAdvogado === false ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning btn-sm" for="at-preposto"><i class="bi bi-person-badge me-1"></i> Preposto</label>
                                </div>
                                @if($flAdvogado === null)
                                    <div class="form-text">Ainda não informado.</div>
                                @endif
                            </div>

                            <div class="col-md-8">
                                <label class="form-label" for="nm_conta_correspondente_ccr">Razão social / Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nm_conta_correspondente_ccr" name="nm_conta_correspondente_ccr" required value="{{ old('nm_conta_correspondente_ccr', $vinculo->nm_conta_correspondente_ccr) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="categoria">Categoria</label>
                                <select id="categoria" name="cd_categoria_correspondente_cac" class="form-select">
                                    <option value="0">Sem categoria</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->cd_categoria_correspondente_cac }}" {{ old('cd_categoria_correspondente_cac', $vinculo->cd_categoria_correspondente_cac) == $categoria->cd_categoria_correspondente_cac ? 'selected' : '' }}>{{ $categoria->dc_categoria_correspondente_cac }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 campo-pf">
                                <label class="form-label" for="cpf">CPF</label>
                                <input type="text" class="form-control dmk-mono" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf', optional(optional($entidade)->cpf)->nu_identificacao_ide) }}">
                            </div>
                            <div class="col-md-4 campo-pj">
                                <label class="form-label" for="cnpj">CNPJ</label>
                                <input type="text" class="form-control dmk-mono" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" value="{{ old('cnpj', optional(optional($entidade)->cnpj)->nu_identificacao_ide) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="oab">Nº OAB</label>
                                <input type="text" class="form-control dmk-mono" id="oab" name="oab" placeholder="Ex.: SC12345" value="{{ old('oab', optional(optional($entidade)->oab)->nu_identificacao_ide) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="rg">RG</label>
                                <input type="text" class="form-control dmk-mono" id="rg" name="rg" value="{{ old('rg', optional(optional($entidade)->rg)->nu_identificacao_ide) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card ue-shell-card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-geo me-1"></i> Endereço</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label" for="nu_cep_ede">CEP</label>
                                <input type="text" class="form-control dmk-mono" id="nu_cep_ede" name="nu_cep_ede" placeholder="00000-000" value="{{ old('nu_cep_ede', optional($endereco)->nu_cep_ede) }}">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label" for="dc_logradouro_ede">Logradouro</label>
                                <input type="text" class="form-control" id="dc_logradouro_ede" name="dc_logradouro_ede" value="{{ old('dc_logradouro_ede', optional($endereco)->dc_logradouro_ede) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="nu_numero_ede">Nº</label>
                                <input type="text" class="form-control" id="nu_numero_ede" name="nu_numero_ede" value="{{ old('nu_numero_ede', optional($endereco)->nu_numero_ede) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="nm_bairro_ede">Bairro</label>
                                <input type="text" class="form-control" id="nm_bairro_ede" name="nm_bairro_ede" value="{{ old('nm_bairro_ede', optional($endereco)->nm_bairro_ede) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="dc_complemento_ede">Complemento</label>
                                <input type="text" class="form-control" id="dc_complemento_ede" name="dc_complemento_ede" value="{{ old('dc_complemento_ede', optional($endereco)->dc_complemento_ede) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="endereco-estado">Estado</label>
                                <select id="endereco-estado" name="cd_estado_est" class="form-select">
                                    <option value="">Selecione</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->cd_estado_est }}" {{ $estadoEndereco == $estado->cd_estado_est ? 'selected' : '' }}>{{ $estado->nm_estado_est }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="endereco-cidade">Cidade</label>
                                <select id="endereco-cidade" name="cd_cidade_cde" class="form-select"></select>
                            </div>
                        </div>
                        <div class="form-text mt-2">O endereço só é gravado quando o logradouro está preenchido.</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <div class="card ue-shell-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-telephone me-1"></i> Telefones</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group input-group-sm dmk-inline-add mb-2">
                                    <input type="text" class="form-control" id="novo-fone" placeholder="(99) 99999-9999">
                                    <select class="form-select" id="novo-fone-tipo" style="max-width: 130px;">
                                        <option value="">Tipo</option>
                                        @foreach($tiposFone as $tipo)
                                            <option value="{{ $tipo->cd_tipo_fone_tfo }}">{{ $tipo->dc_tipo_fone_tfo }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-success" id="add-fone"><i class="bi bi-plus-lg"></i></button>
                                </div>
                                <div class="invalid-feedback d-block small mb-2" id="erro-fone"></div>
                                <table class="table table-sm dmk-table-sm mb-0" id="tabela-fones">
                                    <tbody>
                                        @foreach($fones as $fone)
                                            <tr>
                                                <td class="dmk-mono">{{ $fone->nu_fone_fon }}</td>
                                                <td class="text-muted">{{ optional($fone->tipo)->dc_tipo_fone_tfo }}</td>
                                                <td class="text-end"><button type="button" class="dmk-remove" data-excluir="{{ url('fones/excluir/'.$fone->cd_fone_fon) }}" title="Excluir"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card ue-shell-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-envelope me-1"></i> Emails</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group input-group-sm dmk-inline-add mb-2">
                                    <input type="email" class="form-control" id="novo-email" placeholder="email@exemplo.com">
                                    <select class="form-select" id="novo-email-tipo" style="max-width: 140px;">
                                        <option value="">Tipo</option>
                                        @foreach($tiposEmail as $tipo)
                                            <option value="{{ $tipo->cd_tipo_endereco_eletronico_tee }}">{{ $tipo->dc_tipo_endereco_eletronico_tee }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-success" id="add-email"><i class="bi bi-plus-lg"></i></button>
                                </div>
                                <div class="invalid-feedback d-block small mb-2" id="erro-email"></div>
                                <table class="table table-sm dmk-table-sm mb-0" id="tabela-emails">
                                    <tbody>
                                        @foreach($emails as $email)
                                            <tr>
                                                <td class="text-break">{{ $email->dc_endereco_eletronico_ede }}</td>
                                                <td class="text-muted">{{ optional($email->tipo)->dc_tipo_endereco_eletronico_tee }}</td>
                                                <td class="text-end"><button type="button" class="dmk-remove" data-excluir="{{ url('email/excluir/'.$email->cd_endereco_eletronico_ele) }}" title="Excluir"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card ue-shell-card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-bank me-1"></i> Dados bancários</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label" for="banco-titular">Titular</label>
                                <input type="text" class="form-control form-control-sm" id="banco-titular">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="banco-cpf">CPF/CNPJ do titular</label>
                                <input type="text" class="form-control form-control-sm dmk-mono" id="banco-cpf" placeholder="Somente números">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="banco-tipo">Tipo de conta</label>
                                <select class="form-select form-select-sm" id="banco-tipo">
                                    <option value="">Selecione</option>
                                    @foreach($tiposConta as $tipo)
                                        <option value="{{ $tipo->cd_tipo_conta_tcb }}">{{ $tipo->nm_tipo_conta_tcb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 banco-conta">
                                <label class="form-label" for="banco-banco">Banco</label>
                                <select class="form-select form-select-sm" id="banco-banco">
                                    <option value="">Selecione</option>
                                    @foreach($listaBancos as $banco)
                                        <option value="{{ str_pad($banco->cd_banco_ban, 3, '0', STR_PAD_LEFT) }}">{{ str_pad($banco->cd_banco_ban, 3, '0', STR_PAD_LEFT) }} - {{ $banco->nm_banco_ban }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 banco-conta">
                                <label class="form-label" for="banco-agencia">Agência</label>
                                <input type="text" class="form-control form-control-sm dmk-mono" id="banco-agencia">
                            </div>
                            <div class="col-md-3 banco-conta">
                                <label class="form-label" for="banco-numero">Conta</label>
                                <input type="text" class="form-control form-control-sm dmk-mono" id="banco-numero">
                            </div>
                            <div class="col-md-8 banco-pix d-none">
                                <label class="form-label" for="banco-pix">Chave PIX</label>
                                <input type="text" class="form-control form-control-sm dmk-mono" id="banco-pix">
                            </div>
                            <div class="col-md-2 ms-auto">
                                <button type="button" class="btn btn-success btn-sm w-100" id="add-banco"><i class="bi bi-plus-lg me-1"></i> Adicionar</button>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block small mt-1" id="erro-banco"></div>

                        <div class="table-responsive mt-2">
                            <table class="table table-sm dmk-table-sm mb-0" id="tabela-bancos">
                                <thead>
                                    <tr>
                                        <th>Titular</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Tipo</th>
                                        <th>Dados</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bancos as $banco)
                                        <tr>
                                            <td>{{ $banco->nm_titular_dba }}</td>
                                            <td class="dmk-mono">{{ $banco->nu_cpf_cnpj_dba }}</td>
                                            <td>{{ optional($banco->tipoConta)->nm_tipo_conta_tcb }}</td>
                                            <td>
                                                @if($banco->cd_tipo_conta_tcb == \App\Enums\TipoConta::PIX)
                                                    PIX: <span class="dmk-mono">{{ $banco->dc_pix_dba }}</span>
                                                @else
                                                    {{ optional($banco->banco)->nm_banco_ban }} · Ag. {{ $banco->nu_agencia_dba }} · Conta {{ $banco->nu_conta_dba }}
                                                @endif
                                            </td>
                                            <td class="text-end"><button type="button" class="dmk-remove" data-excluir="{{ url('registro-bancario/excluir/'.$banco->cd_dados_bancarios_dba) }}" title="Excluir"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card ue-shell-card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-whatsapp me-1 text-success"></i> WhatsApp</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label" for="nu_telefone_whatsapp_con">Número com DDD</label>
                        <input type="text" class="form-control dmk-mono" id="nu_telefone_whatsapp_con" name="nu_telefone_whatsapp_con" placeholder="48999999999" value="{{ old('nu_telefone_whatsapp_con', optional($vinculo->correspondente)->nu_telefone_whatsapp_con) }}">
                        <div class="form-text">Usado para lembretes e comunicados automáticos.</div>
                    </div>
                </div>

                <div class="card ue-shell-card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-journal-text me-1"></i> Observações</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="6" name="obs_ccr" id="obs_ccr">{{ old('obs_ccr', trim(html_entity_decode(strip_tags(str_ireplace(['<br>', '<br/>', '<br />', '</p>'], "\n", (string) $vinculo->obs_ccr))))) }}</textarea>
                        <div class="form-text">Visível apenas para o escritório.</div>
                    </div>
                </div>

                <div class="card ue-shell-card">
                    <div class="card-body small text-muted">
                        <p class="mb-2"><i class="bi bi-info-circle me-1"></i> Telefones, emails e contas novos só são gravados ao clicar em <strong>Salvar alterações</strong>.</p>
                        <p class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i> A exclusão de itens já cadastrados (lixeira) acontece na hora.</p>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Salvar alterações</button>
                    <a href="{{ url('v2/correspondentes/'.$idSafe) }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const PIX = {{ \App\Enums\TipoConta::PIX }};
    const novos = { telefones: [], emails: [], registrosBancarios: [] };

    DMK.cidadesPorEstado(
        document.getElementById('endereco-estado'),
        document.getElementById('endereco-cidade'),
        { selecionada: @json((string) $cidadeEndereco), rotuloVazio: 'Selecione a cidade' }
    );
    DMK.select(document.getElementById('categoria'));
    DMK.select(document.getElementById('banco-banco'));

    function alternarTipoPessoa() {
        const juridica = document.getElementById('tp-juridica').checked;
        document.querySelectorAll('.campo-pj').forEach(function (el) { el.classList.toggle('d-none', !juridica); });
        document.querySelectorAll('.campo-pf').forEach(function (el) { el.classList.toggle('d-none', juridica); });
    }
    document.querySelectorAll('input[name="cd_tipo_pessoa_tpp"]').forEach(function (el) {
        el.addEventListener('change', alternarTipoPessoa);
    });
    alternarTipoPessoa();

    function sincronizar(chave) {
        document.getElementById(chave).value = JSON.stringify(novos[chave]);
    }

    function textoCelula(texto, classe) {
        const td = document.createElement('td');
        if (classe) { td.className = classe; }
        td.textContent = texto;
        return td;
    }

    function adicionarLinha(tabela, celulas, chave, item) {
        const tr = document.createElement('tr');
        tr.className = 'dmk-new-row';
        celulas.forEach(function (td) { tr.appendChild(td); });

        const acao = document.createElement('td');
        acao.className = 'text-end';
        acao.innerHTML = '<span class="badge bg-success-subtle text-success me-1">novo</span>' +
            '<button type="button" class="dmk-remove" title="Remover"><i class="bi bi-x-lg"></i></button>';
        acao.querySelector('button').addEventListener('click', function () {
            novos[chave].splice(novos[chave].indexOf(item), 1);
            sincronizar(chave);
            tr.remove();
        });
        tr.appendChild(acao);

        document.querySelector(tabela + ' tbody').appendChild(tr);
    }

    function textoSelecionado(select) {
        return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '';
    }

    document.getElementById('add-fone').addEventListener('click', function () {
        const numero = document.getElementById('novo-fone');
        const tipo = document.getElementById('novo-fone-tipo');
        const erro = document.getElementById('erro-fone');
        erro.textContent = '';

        if (!numero.value.trim()) { erro.textContent = 'Informe o número.'; return; }
        if (!tipo.value) { erro.textContent = 'Informe o tipo do telefone.'; return; }

        const item = { tipo: tipo.value, numero: numero.value.trim(), descricao: textoSelecionado(tipo) };
        novos.telefones.push(item);
        sincronizar('telefones');
        adicionarLinha('#tabela-fones', [textoCelula(item.numero, 'dmk-mono'), textoCelula(item.descricao, 'text-muted')], 'telefones', item);

        numero.value = '';
        tipo.selectedIndex = 0;
        numero.focus();
    });

    document.getElementById('add-email').addEventListener('click', function () {
        const email = document.getElementById('novo-email');
        const tipo = document.getElementById('novo-email-tipo');
        const erro = document.getElementById('erro-email');
        erro.textContent = '';

        if (!email.value.trim() || !email.checkValidity()) { erro.textContent = 'Informe um email válido.'; return; }
        if (!tipo.value) { erro.textContent = 'Informe o tipo do email.'; return; }

        const item = { tipo: tipo.value, email: email.value.trim(), descricao: textoSelecionado(tipo) };
        novos.emails.push(item);
        sincronizar('emails');
        adicionarLinha('#tabela-emails', [textoCelula(item.email, 'text-break'), textoCelula(item.descricao, 'text-muted')], 'emails', item);

        email.value = '';
        tipo.selectedIndex = 0;
        email.focus();
    });

    const bancoTipo = document.getElementById('banco-tipo');
    function alternarTipoConta() {
        const pix = parseInt(bancoTipo.value, 10) === PIX;
        document.querySelectorAll('.banco-conta').forEach(function (el) { el.classList.toggle('d-none', pix); });
        document.querySelectorAll('.banco-pix').forEach(function (el) { el.classList.toggle('d-none', !pix); });
    }
    bancoTipo.addEventListener('change', alternarTipoConta);
    alternarTipoConta();

    document.getElementById('add-banco').addEventListener('click', function () {
        const erro = document.getElementById('erro-banco');
        const campo = function (id) { return document.getElementById(id); };
        erro.textContent = '';

        const tipo = bancoTipo.value;
        const pix = parseInt(tipo, 10) === PIX;
        const item = {
            titular: campo('banco-titular').value.trim(),
            cpf: campo('banco-cpf').value.replace(/[^\d\/]/g, ''),
            tipo: tipo,
            banco: pix ? '' : campo('banco-banco').value,
            agencia: pix ? '' : campo('banco-agencia').value.trim(),
            conta: pix ? '' : campo('banco-numero').value.trim(),
            pix: pix ? campo('banco-pix').value.trim() : ''
        };

        if (!item.titular) { erro.textContent = 'Informe o titular.'; return; }
        if (!item.cpf) { erro.textContent = 'Informe o CPF/CNPJ do titular.'; return; }
        if (!item.tipo) { erro.textContent = 'Informe o tipo de conta.'; return; }
        if (pix && !item.pix) { erro.textContent = 'Informe a chave PIX.'; return; }
        if (!pix && (!item.banco || !item.agencia || !item.conta)) { erro.textContent = 'Informe banco, agência e conta.'; return; }

        const dados = pix
            ? 'PIX: ' + item.pix
            : textoSelecionado(campo('banco-banco')) + ' · Ag. ' + item.agencia + ' · Conta ' + item.conta;

        novos.registrosBancarios.push(item);
        sincronizar('registrosBancarios');
        adicionarLinha('#tabela-bancos', [
            textoCelula(item.titular),
            textoCelula(item.cpf, 'dmk-mono'),
            textoCelula(textoSelecionado(bancoTipo)),
            textoCelula(dados)
        ], 'registrosBancarios', item);

        ['banco-titular', 'banco-cpf', 'banco-agencia', 'banco-numero', 'banco-pix'].forEach(function (id) { campo(id).value = ''; });
    });

    document.querySelectorAll('[data-excluir]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            if (!confirm('Excluir este item agora? Essa ação não depende do botão Salvar.')) {
                return;
            }

            botao.disabled = true;
            fetch(botao.dataset.excluir, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(function (resposta) {
                    if (!resposta.ok) { throw new Error(); }
                    botao.closest('tr').remove();
                })
                .catch(function () {
                    botao.disabled = false;
                    alert('Não foi possível excluir. Atualize a página e tente novamente.');
                });
        });
    });
});
</script>
@endsection
