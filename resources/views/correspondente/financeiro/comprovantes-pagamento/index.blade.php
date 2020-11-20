@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Financeiro</li>
        <li>Comprovantes de Pagamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Financeiro <span> > Comprovantes de Pagamento</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">            
            <div class="well">
                <form action="{{ url('correspondente/financeiro/comprovantes-de-pagamento/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}                
                    <div class="row">
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Número do Processo</label><br />
                            <input style="width: 100%" class="form-control" type="text" name="processo" id="nu_processo_pro" placeholder="Nº Processo" value="{{ !empty($processo) ? $processo : '' }}" >         
                        </section>   
                        <section class="col col-md-4 col-lg-2">
                            <label class="label label-black">Mês</label><br />
                            <select name="mes" class="form-control" style="width: 100%">
                                <option  selected value="">Selecione</option>
                                @foreach($meses as $key => $mes)
                                    <option {!! !empty($mesParam) && $key == $mesParam ? 'selected' : '' !!} value="{{ $key}} ">{{ $mes }}</option>
                                @endforeach
                            </select>    
                        </section>   
                        <section class="col col-md-4 col-lg-4">                           
                            <label class="label label-black">Cliente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_conta_con" value="{{(old('cd_conta_con') ? old('cd_conta_con') : (\Session::get('conta') ? \Session::get('conta') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_conta_con" placeholder="Digite 3 caracteres para busca" type="text" id="conta_auto_complete" value="{{(old('nm_conta_con') ? old('nm_conta_con') : (\Session::get('nmConta') ? \Session::get('nmConta') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-conta" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>  
                     
                        <section class="col col-md-3">
                            <br />
                            <button class="btn btn-default" type="submit"><i class="fa fa-file-pdf-o"></i> Buscar </button>
                        </section>                                   
                    </div>
                    <div class="row">

                    </div>
                </form>
            </div>           
             <label class="text-primary"><i class="fa fa-info-circle"></i> Efetue uma busca para mostrar os comprovantes.</label> 
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Comprovantes de Pagamento</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th style="text-align: center;">Cliente</th>
                                    <th style="text-align: center;">Comprovante</th>                        
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($comprovantes as $comprovante)    
                                    @php
                                        $url = 'saida/'.$comprovante['conta'].'/'.$comprovante['id'].'/anexo/'.$comprovante['nome'];
                                    @endphp                            
                                    <tr>
                                        <td>{{$comprovante['cliente']}}</td>
                                        <td><a href="{{ url($url)}}">{{$comprovante['nome']}}</a></td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        $( "#conta_auto_complete" ).focusout(function(){
           if($("input[name='cd_conta_con']").val() == ''){
                $("#conta_auto_complete").val('');
           }
        });

        var pathConta = "{{ url('autocompleteConta') }}";

        $( "#conta_auto_complete" ).autocomplete({
          source: pathConta,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_conta_con']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-conta').click(function(){
            $("input[name='cd_conta_con']").val('');
            $("input[name='nm_conta_con']").val('');

        });
    
    });
</script>

@endsection