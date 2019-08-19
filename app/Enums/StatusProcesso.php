<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StatusProcesso extends Enum
{
    const ANDAMENTO = 1;
    const AGUARDANDO_CORRESPONDENTE = 2;
    const ACEITO_CORRESPONDENTE = 3;
    const RECUSADO_CORRESPONDENTE = 4;
    const AGUARDANDO_CLIENTE = 5;
    const FINALIZADO = 6;
    const CANCELADO = 7;
    const FINALIZADO_CORRESPONDENTE = 8;

}