<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TipoEntidade extends Enum
{
    const USUARIO = 5;
    const CORRESPONDENTE = 6;
    const CONTA = 7;
    const CLIENTE = 8;
    const CONTATO = 9;
    const PROCESSO = 10;
    const CONTA_CORRESPONDENTE = 11;
}
