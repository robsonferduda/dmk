<?php

namespace App\Enums;

class StatusPagamentoCorrespondente
{
    const GERADO             = 1;
    const ENVIADO_APROVACAO  = 2;
    const APROVADO           = 3;
    const PAGO               = 4;
    const RECUSADO           = 5;

    public static function labels(): array
    {
        return [
            self::GERADO            => 'Gerado',
            self::ENVIADO_APROVACAO => 'Enviado para Aprovação',
            self::APROVADO          => 'Aprovado',
            self::PAGO              => 'Pago',
            self::RECUSADO          => 'Recusado',
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
            self::RECUSADO          => 'label-danger',
        ][$status] ?? 'label-default';
    }

    /**
     * Classe CSS do badge no relatório PDF (inclui Parcialmente Pago).
     */
    public static function badgeRelatorio(int $status, bool $parcialmentePago = false): string
    {
        if ($parcialmentePago && $status === self::APROVADO) {
            return 'parcial';
        }

        return [
            self::GERADO            => 'gerado',
            self::ENVIADO_APROVACAO => 'enviado',
            self::APROVADO          => 'aprovado',
            self::PAGO              => 'pago',
            self::RECUSADO          => 'recusado',
        ][$status] ?? 'gerado';
    }

    /**
     * Estilos inline para badges no PDF (mPDF aplica melhor que classes compostas).
     */
    public static function badgeRelatorioEstilo(int $status, bool $parcialmentePago = false): string
    {
        $cls = self::badgeRelatorio($status, $parcialmentePago);

        $map = [
            'gerado'   => 'background-color:#95a5a6;color:#ffffff;',
            'enviado'  => 'background-color:#e67e22;color:#ffffff;',
            'aprovado' => 'background-color:#2980b9;color:#ffffff;',
            'parcial'  => 'background-color:#f0ad4e;color:#333333;',
            'pago'     => 'background-color:#27ae60;color:#ffffff;',
            'recusado' => 'background-color:#e74c3c;color:#ffffff;',
        ];

        return $map[$cls] ?? $map['gerado'];
    }
}
