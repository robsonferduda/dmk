@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url("conta/detalhes/".\App\Entidade::where('cd_entidade_ete',Auth::user()->cd_entidade_ete)->first()->cd_conta_con) }}">Conta</a></li>
        <li>Editar Dados</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-user"></i> Conta <span>> Editar Dados</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Editar Conta </h2>             
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                    <div role="content">
                        <div class="widget-body no-padding">
                            {!! Form::open(['id' => 'frm-update-conta', 'url' => 'conta/update', 'class' => 'smart-form']) !!}
                                <header>
                                    Dados Básicos
                                </header>

                                <fieldset>
                                   
                                    <div class="row">
                                        <input type="hidden" name="cd_conta_con" value="{{ $conta->cd_conta_con }}">
                                        <section class="col col-6">
                                            <label class="label">Razão Social</label>
                                            <label class="input">
                                                <input required type="text" name="nm_razao_social_con" placeholder="Razão Social" value="{{ ($conta) ? $conta->nm_razao_social_con : old('nm_razao_social_con') }}">
                                            </label>
                                        </section>

                                        <section class="col col-6">
                                            <label class="label">Nome Fantasia</label>
                                            <label class="input">
                                                <input required type="text" name="nm_fantasia_con" placeholder="Nome Fantasia" value="{{ ($conta) ? $conta->nm_fantasia_con: old('nm_fantasia_con') }}">
                                            </label>
                                        </section>
                                        
                                    </div> 

                                    <div class="row">                                  
                                       
                                        <section class="col col-3">
                                            <label class="label">Tipo de Pessoa</label>
                                            <label class="select"> 
                                                <select name="cd_tipo_pessoa_tpp">
                                                    <option value="0">Selecione</option>
                                                    @foreach(\App\TipoPessoa::all() as $tipoPessoa)
                                                        <option value="{{ $tipoPessoa->cd_tipo_pessoa_tpp }}" {{ ($conta->cd_tipo_pessoa_tpp == $tipoPessoa->cd_tipo_pessoa_tpp or old('cd_tipo_pessoa_tpp') == $tipoPessoa->cd_tipo_pessoa_tpp) ? "selected" : ""  }}>{{ $tipoPessoa->nm_tipo_pessoa_tpp }}</option>
                                                    @endforeach                                                  
                                                </select>
                                            <i></i></label>
                                        </section>
                                                                            
                                    </div>                                   
                                    
                                </fieldset>

                                <header>
                                    <i class="fa fa-phone"></i> Telefones
                                    <a style="padding: 1px 8px;" data-toggle="modal" data-target="#modalFone"><i class="fa fa-plus-circle"></i> Novo
                                    </a>
                                </header>
                                <fieldset>
                                    @if( count($conta->fone()->get()) > 0)
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:25%">Tipo</th>
                                                    <th style="width:50%">Telefone</th>
                                                    <th style="width:25%">Opções</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($conta->fone()->get() as $fone)
                                                    <tr>
                                                        <td>{{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</td>
                                                        <td>{{ $fone->nu_fone_fon }}</td>
                                                        <td>
                                                            <a><i class="fa fa-edit"></i> Editar</a>
                                                            <a><i class="fa fa-trash"></i> Excluir</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <span>Nenhum telefone cadastrado</span>
                                    @endif                                 
                                </fieldset>

                                <header>
                                    <i class="fa fa-lock"></i> Autenticação 
                                </header>

                                <fieldset>
                                    <div class="row">
                                        <section class="col col-4">
                                            <label class="label">Usuário</label>
                                            <label class="input">
                                                <input required type="password" name="password" placeholder="Usuário">
                                            </label>
                                        </section>                                    

                                        <section class="col col-4">
                                            <label class="label">Senha</label>
                                            <label class="input">
                                                <input required type="password" name="password" placeholder="Senha">
                                            </label>
                                        </section>                                    
                                   
                                        <section class="col col-4">
                                            <label class="label">Repetir Senha</label>
                                            <label class="input">
                                                <input required type="password" name="password" placeholder="Repetir Senha">
                                            </label>
                                        </section>                                    
                                    </div> 
                                </fieldset>

                                <footer>
                                    <button type="submit" class="btn btn-success"><i class="fa-fw fa fa-save"></i> Salvar</button>
                                    <a href="{{ url('home') }}" class="btn btn-danger"><i class="fa-fw fa fa-times"></i> Cancelar</a>
                                </footer>
                            {!! Form::close() !!}                      
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalFone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                </button>
                                <h4 class="modal-title" id="myModalLabel"><i class="fa-fw fa fa-plus"></i> Adicionar Telefone</h4>
            </div>
            {!! Form::open(['id' => 'frm-update-conta', 'url' => 'conta/telefone/adicionar', 'class' => 'form form-inline']) !!}
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 center">
                        <div>
                            <input type="hidden" name="cd_conta_con" value="{{ $conta->cd_conta_con }}">
                            <div class="form-group">
                                <input type="text" class="form-control" name="nu_fone_fon" placeholder="(99) 999999999" value="{{old('nu_fone_fon')}}">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="cd_tipo_fone_tfo">
                                    <option value="0">Tipo</option>
                                    @foreach($tiposFone as $tipoFone)
                                        <option {!! (old('cd_tipo_fone_tfo') == $tipoFone->cd_tipo_fone_tfo ? 'selected' : '') !!}  value="{{ $tipoFone->cd_tipo_fone_tfo }}" >{{ $tipoFone->dc_tipo_fone_tfo }}</option>
                                    @endforeach   
                                </select>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa-fw fa fa-times"></i>  Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="fa-fw fa fa-save"></i> Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection