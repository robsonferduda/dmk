<table>
    <thead>
	    <tr>
            @if($formato === 'google_sheets')
                <td style="font-weight: bold;">TODAS AS COMARCAS</td>
            @endif
            @foreach($estados as $estado)
                <td style="font-weight: bold;">{{ $estado->sg_estado_est }}</td>
            @endforeach
	    </tr>
    </thead>
    <tbody>
        @if($formato === 'google_sheets')
            @php
                // Google Sheets: Criar lista consolidada com prefixo de estado
                $todasCidades = [];
                foreach($estados as $estado) {
                    if(isset($cidades[$estado->cd_estado_est])) {
                        foreach($cidades[$estado->cd_estado_est] as $cidade) {
                            $todasCidades[] = $estado->sg_estado_est . ' - ' . $cidade;
                        }
                    }
                }
                sort($todasCidades);
            @endphp
            @for ($i = 0; $i < max(count($todasCidades), 1000); $i++)
                <tr>
                    <td>{{ isset($todasCidades[$i]) ? $todasCidades[$i] : '' }}</td>
                    @foreach($estados as $estado)
                        @php
                            $cidade = $cidades[$estado->cd_estado_est];
                        @endphp
                        <td>{{ !empty($cidade[$i]) ?  $cidade[$i] : '' }}</td>
                    @endforeach     
                </tr>
            @endfor
        @else
            {{-- Excel/LibreOffice: Apenas cidades separadas por estado --}}
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
        @endif
    </tbody>
</table>