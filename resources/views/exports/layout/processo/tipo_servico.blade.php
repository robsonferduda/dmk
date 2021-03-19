<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">Tipo de Serviço</td>
	    </tr>
    </thead>
    <tbody>
        @foreach($ts as $t)
        <tr>
            <td>{{ $t->nm_tipo_servico_tse  }} -----{{$t->nu_tipo_servico_tse}}-----</td>     
        </tr>
        @endforeach
    </tbody>
</table>