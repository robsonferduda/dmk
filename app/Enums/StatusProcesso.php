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
    const CONTRATAR_CORRESPONDENTE = 9;
    const AGUARDANDO_DOCS_CORRESPONDENTE = 10;
    const AGUARDANDO_CUMPRIMENTO = 11;
    const AGUARDANDO_DADOS = 12;
    const DADOS_ENVIADOS = 13;
    const PENDENTE_ANALISE = 14;
    const CADASTRADO_CLIENTE = 15;
    const ALTERADO_PELO_CLIENTE = 16;
    const CANCELADO_PELO_CLIENTE = 7;
}