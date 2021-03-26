<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">Advogados</td>
	    </tr>
    </thead>
    <tbody>
        @foreach($advogados as $advogado)
        <tr>
            <td>{{ $advogado->nm_contato_cot  }} ---{{$advogado->nu_contato_cot}}---</td>     
        </tr>
        @endforeach
    </tbody>
</table>