@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Financeiro</li>
        <li>Balanço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i> Financeiro <span> > Balanço</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">              
                <form action="{{ url('financeiro/balanco/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data de Início</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data Fim</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  >                            
                        </section>
                        <section class="col col-md-6">     
                            <label class="label label-black">Categoria</label><br />                
                            <select multiple style="width: 100%" name="cd_categoria_despesa_cad[]" class="select2 categoria_despesa_multple" data-placeholder="Selecione uma categoria">   
                                <option></option>                            
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->cd_categoria_despesa_cad }}" {{ (!empty(\Session::get('categoria') and in_array($cat->cd_categoria_despesa_cad,\Session::get('categoria'))) ? 'selected' : '') }} >{{ $cat->nm_categoria_despesa_cad }}</option>
                                @endforeach
                            </select>
                        </section>
                    </div>
                    <div class=" row">
                        <section class="col col-md-10">
                            <label class="label label-black">Tipo de Despesa<span id='span-tipo-despesa' class="text-danger"></span></label><br />
                            <select multiple style="width: 100%" name="cd_tipo_despesa_tds[]" class="select2 tipo_despesa" data-placeholder="Selecione um tipo de despesa" >
                                @foreach($tiposDespesa as $despesa)
                                    <option value="{{ $despesa->cd_tipo_despesa_tds }}" {{ (!empty(\Session::get('tipoDespesa') and in_array($despesa->cd_tipo_despesa_tds,\Session::get('tipoDespesa'))) ? 'selected' : '') }} data-categoria="{{ $despesa->cd_categoria_despesa_cad }}">{{ $despesa->nm_tipo_despesa_tds }}</option>
                                @endforeach

                            </select>
                        </section>
                        <section class="col col-md-1">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar </button>
                        </section>   
                    </div>
                                    
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                    <h2>Balanço</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                      <table class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th></th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>                               
                                <tr>                                    
                                    <td style="font-weight: bold">Despesas</td>
                                    <td>{{ 'R$ '.number_format($despesas,2,',','') }}</td>
                                </tr>   
                                <tr>                                    
                                    <td style="font-weight: bold">Saídas</td>
                                    <td>{{ 'R$ '.number_format($saidas,2,',','') }}</td>
                                </tr>   
                                <tr>                                    
                                    <td style="font-weight: bold">Entradas</td>
                                    <td>{{ 'R$ '.number_format($entradas,2,',','') }}</td>
                                </tr> 
                                <tr>                                    
                                    <td style="font-weight: bold">Saldo</td>
                                    <td>{{ 'R$ '.number_format($saldo,2,',','') }}</td>
                                </tr>                               
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
        
    });
</script>

@endsection