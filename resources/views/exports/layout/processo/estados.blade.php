<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">Sigla</td>
            <td style="font-weight: bold;">Estados</td>
	    </tr>
    </thead>
    <tbody>
        @foreach($estados as $estado)
        <tr>
            <td>{{ $estado->sg_estado_est  }}</td>  
            <td>{{ $estado->nm_estado_est  }}</td>        
        </tr>
        @endforeach
    </tbody>
</table>