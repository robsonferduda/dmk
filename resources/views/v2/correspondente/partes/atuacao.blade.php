@if($flAdvogado === true)
    <span class="dmk-pill dmk-pill-advogado"><i class="bi bi-mortarboard"></i> Advogado</span>
@elseif($flAdvogado === false)
    <span class="dmk-pill dmk-pill-preposto"><i class="bi bi-person-badge"></i> Preposto</span>
@else
    <span class="dmk-pill dmk-pill-indefinido"><i class="bi bi-question-circle"></i> Não informado</span>
@endif
