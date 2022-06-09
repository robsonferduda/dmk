<table>
    <thead>
	    <tr>
            @foreach($estados as $estado)
                <td style="font-weight: bold;">{{ $estado->sg_estado_est }}</td>
            @endforeach
	    </tr>
    </thead>
    <tbody>
        @for ($i = 0; $i <= 1000; $i++)
            <tr>
                @foreach($estados as $estado)
                    @php
                        $cidade = $cidades[$estado->cd_estado_est];
                    @endphp
                    <td>{{ !empty($cidade[$i]) ?  $cidade[$i] : '' }}</td>
                @endforeach     
            </tr>
        @endfor
    </tbody>
</table>