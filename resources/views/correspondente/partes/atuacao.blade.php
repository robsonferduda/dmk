@php
    $valorAtuacao = $flAdvogado ?? null;
    if (is_string($valorAtuacao)) {
        $valorAtuacao = in_array(strtolower($valorAtuacao), ['t', 'true', '1', 's'], true)
            ? true
            : (in_array(strtolower($valorAtuacao), ['f', 'false', '0', 'n'], true) ? false : null);
    }
@endphp
@if($valorAtuacao === true)
    <span class="label label-success">Advogado</span>
@elseif($valorAtuacao === false)
    <span class="label label-warning">Preposto</span>
@else
    <span class="label label-default">Não informado</span>
@endif
