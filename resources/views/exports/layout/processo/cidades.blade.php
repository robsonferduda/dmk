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
            // Criar lista consolidada apenas com o nome da cidade (sem UF)
            $todasCidades = [];
            foreach($estados as $estado) {
                if(isset($cidades[$estado->cd_estado_est])) {
                    foreach($cidades[$estado->cd_estado_est] as $cidade) {
                        // Adiciona apenas o nome da cidade, sem duplicatas
                        if (!in_array($cidade, $todasCidades)) {
                            $todasCidades[] = $cidade;
                        }
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