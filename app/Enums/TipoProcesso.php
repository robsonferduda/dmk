<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TipoProcesso extends Enum
{
    const AUDIENCIA = 18;
    const DILIGENCIA_GERAL = 19;
    const PROCESSO_PARTICULAR = 20;
    const PROTOCOLO = 21;
}
