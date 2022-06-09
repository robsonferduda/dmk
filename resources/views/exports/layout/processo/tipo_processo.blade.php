<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">Tipo de Processo</td>
	    </tr>
    </thead>
    <tbody>
        @foreach($tp as $t)
        <tr>
            <td>{{ $t->nm_tipo_processo_tpo  }} ---{{$t->nu_tipo_processo_tpo}}---</td>     
        </tr>
        @endforeach
    </tbody>
</table>