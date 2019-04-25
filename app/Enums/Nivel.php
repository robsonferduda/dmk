<?php 

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Nivel extends Enum
{
    const ADMIN = 1;
    const COLABORADOR = 2;
    const CORRESPONDENTE = 3;
}