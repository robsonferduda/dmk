<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Dias úteis forenses: fins de semana e feriados nacionais (inclui Carnaval e Corpus Christi,
 * em que o expediente forense normalmente não ocorre). Feriados estaduais/municipais não entram.
 */
class DiasUteis
{
    private static $cache = [];

    /**
     * @return string[] datas Y-m-d
     */
    public static function feriadosNacionais(int $ano): array
    {
        if (isset(self::$cache[$ano])) {
            return self::$cache[$ano];
        }

        $fixos = ['01-01', '04-21', '05-01', '09-07', '10-12', '11-02', '11-15', '11-20', '12-25'];
        $feriados = array_map(function ($mmdd) use ($ano) { return "{$ano}-{$mmdd}"; }, $fixos);

        $pascoa = self::pascoa($ano);
        foreach ([-48, -47, -2, 60] as $deslocamento) { // Carnaval (seg e ter), Sexta-feira Santa, Corpus Christi
            $feriados[] = $pascoa->copy()->addDays($deslocamento)->toDateString();
        }

        sort($feriados);
        return self::$cache[$ano] = $feriados;
    }

    public static function ehDiaUtil(Carbon $dia): bool
    {
        return ! $dia->isWeekend() && ! in_array($dia->toDateString(), self::feriadosNacionais($dia->year), true);
    }

    /** Primeiro dia útil depois de $dia. */
    public static function proximo(Carbon $dia): Carbon
    {
        $proximo = $dia->copy()->startOfDay()->addDay();
        while (! self::ehDiaUtil($proximo)) {
            $proximo->addDay();
        }
        return $proximo;
    }

    /** Último dia útil antes de $dia. */
    public static function anterior(Carbon $dia): Carbon
    {
        $anterior = $dia->copy()->startOfDay()->subDay();
        while (! self::ehDiaUtil($anterior)) {
            $anterior->subDay();
        }
        return $anterior;
    }

    /** Domingo de Páscoa (algoritmo de Meeus/Jones/Butcher). */
    private static function pascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($ano, $mes, $dia)->startOfDay();
    }
}
