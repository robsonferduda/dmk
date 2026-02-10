<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">TODAS AS COMARCAS</td>
            @foreach($estados as $estado)
                <td style="font-weight: bold;">{{ $estado->sg_estado_est }}</td>
            @endforeach
	    </tr>
    </thead>
    <tbody>
        @php
            // Padronizado: Criar lista consolidada com formato Cidade (UF) para todos os formatos
            $todasCidades = [];
            foreach($estados as $estado) {
                if(isset($cidades[$estado->cd_estado_est])) {
                    foreach($cidades[$estado->cd_estado_est] as $cidade) {
                        $todasCidades[] = $cidade . ' (' . $estado->sg_estado_est . ')';
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
    </tbody>
</table>