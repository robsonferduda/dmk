<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TipoConta extends Enum
{
    const CORRENTE = 1;
    const POUPANCA = 2;
    const PIX = 3;

}