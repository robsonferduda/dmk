<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TipoIdentificacao extends Enum
{
    const CPF = 1;
    const RG = 2;
    const OAB = 3;
    const PASSAPORTE = 4;
    const INSCRICAO_ESTADUAL = 5;
    const INSCRICAO_MUNICIPAL = 6;
    const CNPJ = 7;
}

