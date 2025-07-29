@foreach($processo->anexos as $key => $anexo)
    <div class="row" style="width:100%; background-color: #fff; margin-bottom: 10px; ">
        <div style="float: left; width: 8%; text-align: center;">
            <label class="text-default" style="margin-top: 8px;">
                <input type="checkbox" name="lista_arquivos[]" class="lista_arquivos" value="{{ $anexo->nm_local_anexo_processo_apr }}{{ $anexo->nm_anexo_processo_apr }}">
            </label>
        </div>
        <div style="float: left; width: 92%">
            <h4>{{ $anexo->nm_anexo_processo_apr }}</h4>
            <h6 style="margin: 0px; font-weight: 200;"><strong>{{ date('d/m/Y H:i:s', strtotime($anexo->created_at)) }}</strong> por <strong>{{ ($anexo->entidade and $anexo->entidade->usuario) ? $anexo->entidade->usuario->name : 'Sem responsável' }}</strong></h6>   
        </div> 
    </div>
    @if($key < count($processo->anexos)-1)
        <hr style="margin: 0" />
    @endif
@endforeach