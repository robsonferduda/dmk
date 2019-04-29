<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Roles extends Enum
{
	const ADMINISTRADOR = 1;
    const COLABORADOR = 2;    
    const CORRESPONDENTE = 4;
}

