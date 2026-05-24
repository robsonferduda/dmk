<?php

namespace App\Enums;

class StatusPagamentoCorrespondente
{
    const GERADO             = 1;
    const ENVIADO_APROVACAO  = 2;
    const APROVADO           = 3;
    const PAGO               = 4;

    public static function labels(): array
    {
        return [
            self::GERADO            => 'Gerado',
            self::ENVIADO_APROVACAO => 'Enviado para Aprovação',
            self::APROVADO          => 'Aprovado',
            self::PAGO              => 'Pago',
        ];
    }

    public static function label(int $status): string
    {
        return self::labels()[$status] ?? 'Desconhecido';
    }

    public static function badgeClass(int $status): string
    {
        return [
            self::GERADO            => 'label-default',
            self::ENVIADO_APROVACAO => 'label-warning',
            self::APROVADO          => 'label-info',
            self::PAGO              => 'label-success',
        ][$status] ?? 'label-default';
    }
}
