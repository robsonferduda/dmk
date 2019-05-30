@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Agenda</li>
        <li>Todos os Contatos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-book"></i> Agenda <span> > Todos os Contatos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('contato/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well center">
                @foreach(range('A', 'Z') as $letra) 
                    @if(!empty(session('inicial')) and session('inicial') == $letra)
                        <a class="btn btn-primary btn-xs btn_sigla" href="{{ url('contato/buscar/'.$letra) }}">{{ $letra }}</a>
                    @else
                        <a class="btn btn-default btn-xs btn_sigla" href="{{ url('contato/buscar/'.$letra) }}">{{ $letra }}</a>
                    @endif
                @endforeach
            </div>
            <div class="well">
                <form action="{{ url('contato/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}

                    <div class="input-group col-md-5">
                        <span class="input-group-addon">Cliente</span>
                        <input type="hidden" name="cd_cliente_cli" value="{{ (!empty($codCliente)) ? $codCliente: ''  }}" >
                        <input name="nm_cliente_cli" value="{{ (!empty($nomeCliente)) ? $nomeCliente: '' }}" class="form-control ui-autocomplete-input" placeholder="Digite 3 caracteres para busca" type="text" id="client" autocomplete="off">
                        <div style="clear: all;"></div>
                        <span id="limpar-cliente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                        <span id="novo-contato-cliente" title="Adicionar contato ao cliente" class="input-group-addon btn btn-success"><i class="fa fa-plus"></i></span>
                    </div>

                    <div class="form-group">
                        <select name="cd_tipo_contato_tct" class="form-control">
                            <option value="">Tipos de Contato</option>
                            @foreach($tiposContato as $tipo)

                                <option {{ (!empty($tipoContato) && $tipoContato == $tipo->cd_tipo_contato_tct) ? 'selected' : ''}} value="{{ $tipo->cd_tipo_contato_tct }}">{{ $tipo->nm_tipo_contato_tct }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <input type="hidden" id="entidade-cliente" value="{{ (!empty($entidade)) ? $entidade : '' }}">
                </form>
                <div style="clear: both;"></div>
            </div>

            <label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Utilize as opções de busca por "Letra" ou um dos campos de filtro para listar os contatos.</label>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-book"></i> </span>
                    <h2>Contatos</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if(isset($dados))
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>    
                                    <th>Nome</th>
                                    <th>Comarca</th> 
                                    <th>Tipo de Contato</th>                                
                                    <th>Telefone</th>
                                    <th class="center">Email</th>                               
                                    <th class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                @foreach($dados as $d)
                                   
                                        <tr>
                                            <td data-id="{{ $d->cd_contato_cot }}"><a href="{{ url('contato/detalhes/'.$d->cd_contato_cot) }}">{{ $d->nm_contato_cot }}</a></td>
                                            <td>{{ ($d->nm_cidade_cde) ? $d->nm_cidade_cde : 'Não informado' }}</td>
                                            <td>{{ $d->nm_tipo_contato_tct }}</td>
                                            <td>{!! ($d->nu_fone_fon) ? $d->nu_fone_fon. (($d->totalFone > 1 ) ? ' <i style="color: #305d8c;" class="fa  fa-plus-square-o"></i>' : '') : 'Não informado' !!}</td>
                                            <td>{!! ($d->dc_endereco_eletronico_ede) ? $d->dc_endereco_eletronico_ede. (($d->totalEmail > 1 ) ? ' <i style="color: #305d8c;" class="fa fa-plus-square-o"></i>' : '') : 'Não informado' !!}</td>
                                            <td class="center">
                                                <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('contato/detalhes/'.$d->cd_contato_cot) }}"><i class="fa fa-file-text-o"></i> </a>
                                                <a title="Editar" class="btn btn-primary btn-xs" href="{{ url('contato/editar/'.$d->cd_contato_cot) }}"><i class="fa fa-edit"></i> </a>
                                                <button title="Excluir" data-url="contatos/" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i> </button>
                                            </td>
                                        </tr>
                                    
                    
                                @endforeach                                
                            </tbody>
                        </table>
                        @else
                            <h5 class="center marginTop20"><i class="fa fa-info-circle"></i> Clique sobre a inicial do nome para mostrar as opções</h5>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {

        var _location = document.location.toString();
        var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
        var applicationName = _location.substring(0, applicationNameIndex) + '/';

        var path = "{{ url('autocompleteCliente') }}";

        $( "#client" ).autocomplete({
          source: path,
          minLength: 2,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);
            $('#entidade-cliente').val(ui.item.entidade);
             $("input[name='nm_cliente_cli']").css('background-color','#ffffff').focus();
          },
          open: function(event, ui){
            
          }
        });
    
        $('#novo-contato-cliente').click(function(){

            var entidade = $('#entidade-cliente').val();
            if(entidade != ''){
                window.location.href = applicationName +'cliente/'+entidade+'/contato/novo';
            }else{
                $("input[name='nm_cliente_cli']").css('background-color','#ffd8cc').focus();
            }

        });

        $('#limpar-cliente').click(function(){
            $('#entidade-cliente').val('');
            $("input[name='cd_cliente_cli']").val('');
            $("input[name='nm_cliente_cli']").val('');

        });


    });
   

    
</script>

@endsection