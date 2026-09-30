<?php

namespace App\Services\Correspondente;

use Carbon\Carbon;
use DB;

/**
 * Indicadores dos processos de um correspondente dentro do escritório.
 * A data de referência é a do ato (dt_prazo_fatal_pro), com fallback para a data de cadastro.
 */
class AnaliseProcessos
{
    const FINALIZADOS = [6, 8];
    const CANCELADOS = [4, 7, 17, 19];

    const LIMIAR_TENDENCIA = 2.0;
    const LIMIAR_CHECKIN_PP = 10;
    const LIMIAR_VARIACAO = 5;

    private $conta;
    private $correspondente;

    public function __construct($conta, $correspondente)
    {
        $this->conta = (int) $conta;
        $this->correspondente = (int) $correspondente;
    }

    public static function grupoStatus($status)
    {
        if (in_array((int) $status, self::FINALIZADOS, true)) {
            return 'finalizado';
        }

        return in_array((int) $status, self::CANCELADOS, true) ? 'cancelado' : 'andamento';
    }

    /**
     * Série mensal dos últimos $meses meses (o mês corrente entra como parcial).
     */
    public function serieMensal($meses)
    {
        $fim = Carbon::now()->endOfMonth();
        $inicio = Carbon::now()->startOfMonth()->subMonths($meses - 1);
        $finalizados = implode(',', self::FINALIZADOS);
        $cancelados = implode(',', self::CANCELADOS);

        $linhas = DB::select("
            SELECT to_char(date_trunc('month', p.dt_ref), 'YYYY-MM') AS mes,
                   count(*) AS total,
                   count(*) FILTER (WHERE p.cd_status_processo_stp IN ({$finalizados})) AS finalizados,
                   count(*) FILTER (WHERE p.cd_status_processo_stp IN ({$cancelados})) AS cancelados,
                   COALESCE(sum(pth.vl_taxa_honorario_correspondente_pth) FILTER (WHERE p.cd_status_processo_stp NOT IN ({$cancelados})), 0) AS honorarios,
                   count(pth.vl_taxa_honorario_correspondente_pth) FILTER (WHERE p.cd_status_processo_stp NOT IN ({$cancelados}) AND pth.vl_taxa_honorario_correspondente_pth > 0) AS com_honorario,
                   count(*) FILTER (WHERE p.fl_checkin_pro AND p.cd_status_processo_stp IN ({$finalizados})) AS checkins,
                   percentile_cont(0.5) WITHIN GROUP (
                       ORDER BY GREATEST(0, EXTRACT(EPOCH FROM p.dt_finalizacao_pro - p.dt_prazo_fatal_pro) / 86400)
                   ) FILTER (WHERE p.dt_finalizacao_pro IS NOT NULL AND p.cd_status_processo_stp IN ({$finalizados})) AS dias_finalizacao
            FROM (
                SELECT pro.*, COALESCE(pro.dt_prazo_fatal_pro, pro.created_at::date) AS dt_ref
                FROM processo_pro pro
                WHERE pro.cd_conta_con = ?
                  AND pro.cd_correspondente_cor = ?
                  AND pro.deleted_at IS NULL
            ) p
            LEFT JOIN processo_taxa_honorario_pth pth
                ON pth.cd_processo_pro = p.cd_processo_pro
               AND pth.deleted_at IS NULL
            WHERE p.dt_ref BETWEEN ? AND ?
            GROUP BY 1
        ", [$this->conta, $this->correspondente, $inicio->toDateString(), $fim->toDateString()]);

        $escritorio = DB::select("
            SELECT to_char(date_trunc('month', COALESCE(dt_prazo_fatal_pro, created_at::date)), 'YYYY-MM') AS mes,
                   count(*) AS total
            FROM processo_pro
            WHERE cd_conta_con = ?
              AND cd_correspondente_cor IS NOT NULL
              AND deleted_at IS NULL
              AND COALESCE(dt_prazo_fatal_pro, created_at::date) BETWEEN ? AND ?
            GROUP BY 1
        ", [$this->conta, $inicio->toDateString(), $fim->toDateString()]);

        $inicioCheckin = DB::table('processo_pro')
            ->where('cd_conta_con', $this->conta)
            ->where('fl_checkin_pro', true)
            ->whereNull('deleted_at')
            ->min('dt_prazo_fatal_pro');
        $mesInicioCheckin = $inicioCheckin ? substr($inicioCheckin, 0, 7) : null;

        $porMes = [];
        foreach ($linhas as $linha) {
            $porMes[$linha->mes] = $linha;
        }

        $totalEscritorio = [];
        foreach ($escritorio as $linha) {
            $totalEscritorio[$linha->mes] = (int) $linha->total;
        }

        $serie = [];
        $mesAtual = Carbon::now()->format('Y-m');
        for ($data = $inicio->copy(); $data->lte($fim); $data->addMonth()) {
            $mes = $data->format('Y-m');
            $linha = $porMes[$mes] ?? null;

            $total = $linha ? (int) $linha->total : 0;
            $finalizados = $linha ? (int) $linha->finalizados : 0;
            $cancelados = $linha ? (int) $linha->cancelados : 0;
            $honorarios = $linha ? (float) $linha->honorarios : 0.0;
            $comHonorario = $linha ? (int) $linha->com_honorario : 0;
            $escritorioMes = $totalEscritorio[$mes] ?? 0;
            $checkinDisponivel = $mesInicioCheckin !== null && $mes >= $mesInicioCheckin;

            $serie[] = [
                'mes'              => $mes,
                'rotulo'           => $this->rotuloMes($data),
                'parcial'          => $mes === $mesAtual,
                'total'            => $total,
                'finalizados'      => $finalizados,
                'cancelados'       => $cancelados,
                'andamento'        => $total - $finalizados - $cancelados,
                'honorarios'       => round($honorarios, 2),
                'ticket'           => $comHonorario ? round($honorarios / $comHonorario, 2) : null,
                'participacao'     => $escritorioMes ? round($total * 100 / $escritorioMes, 2) : null,
                'taxa_checkin'     => ($checkinDisponivel && $finalizados) ? round($linha->checkins * 100 / $finalizados, 1) : null,
                'dias_finalizacao' => ($linha && $linha->dias_finalizacao !== null) ? round((float) $linha->dias_finalizacao, 1) : null,
            ];
        }

        return $serie;
    }

    public function resumo()
    {
        $finalizados = implode(',', self::FINALIZADOS);
        $cancelados = implode(',', self::CANCELADOS);

        return DB::selectOne("
            SELECT count(*) AS total,
                   count(*) FILTER (WHERE p.cd_status_processo_stp IN ({$finalizados})) AS finalizados,
                   count(*) FILTER (WHERE p.cd_status_processo_stp IN ({$cancelados})) AS cancelados,
                   min(COALESCE(p.dt_prazo_fatal_pro, p.created_at::date)) AS primeiro,
                   max(COALESCE(p.dt_prazo_fatal_pro, p.created_at::date)) FILTER (WHERE COALESCE(p.dt_prazo_fatal_pro, p.created_at::date) <= CURRENT_DATE) AS ultimo,
                   count(*) FILTER (WHERE p.dt_prazo_fatal_pro > CURRENT_DATE AND p.cd_status_processo_stp NOT IN ({$cancelados})) AS agendados,
                   COALESCE(sum(pth.vl_taxa_honorario_correspondente_pth) FILTER (WHERE p.cd_status_processo_stp NOT IN ({$cancelados})), 0) AS honorarios,
                   count(DISTINCT p.cd_cliente_cli) AS clientes,
                   count(DISTINCT p.cd_cidade_cde) AS comarcas
            FROM processo_pro p
            LEFT JOIN processo_taxa_honorario_pth pth
                ON pth.cd_processo_pro = p.cd_processo_pro
               AND pth.deleted_at IS NULL
            WHERE p.cd_conta_con = ?
              AND p.cd_correspondente_cor = ?
              AND p.deleted_at IS NULL
        ", [$this->conta, $this->correspondente]);
    }

    /**
     * Maiores concentrações no período: tipo de serviço, comarca ou cliente.
     */
    public function ranking($dimensao, $meses, $limite = 8)
    {
        $colunas = [
            'servico' => ["COALESCE(tse.nm_tipo_servico_tse, 'Não informado')",
                          'LEFT JOIN tipo_servico_tse tse ON tse.cd_tipo_servico_tse = pth.cd_tipo_servico_correspondente_tse'],
            'comarca' => ["COALESCE(cde.nm_cidade_cde || ' / ' || est.sg_estado_est, 'Não informada')",
                          'LEFT JOIN cidade_cde cde ON cde.cd_cidade_cde = p.cd_cidade_cde LEFT JOIN estado_est est ON est.cd_estado_est = cde.cd_estado_est'],
            'cliente' => ["COALESCE(NULLIF(TRIM(cli.nm_fantasia_cli), ''), cli.nm_razao_social_cli, 'Não informado')",
                          'LEFT JOIN cliente_cli cli ON cli.cd_cliente_cli = p.cd_cliente_cli'],
        ];

        list($rotulo, $join) = $colunas[$dimensao];
        $inicio = Carbon::now()->startOfMonth()->subMonths($meses - 1)->toDateString();
        $cancelados = implode(',', self::CANCELADOS);

        return DB::select("
            SELECT {$rotulo} AS rotulo,
                   count(*) AS total,
                   COALESCE(sum(pth.vl_taxa_honorario_correspondente_pth), 0) AS honorarios
            FROM processo_pro p
            LEFT JOIN processo_taxa_honorario_pth pth
                ON pth.cd_processo_pro = p.cd_processo_pro
               AND pth.deleted_at IS NULL
            {$join}
            WHERE p.cd_conta_con = ?
              AND p.cd_correspondente_cor = ?
              AND p.deleted_at IS NULL
              AND p.cd_status_processo_stp NOT IN ({$cancelados})
              AND COALESCE(p.dt_prazo_fatal_pro, p.created_at::date) BETWEEN ? AND CURRENT_DATE
            GROUP BY 1
            ORDER BY 2 DESC
            LIMIT " . (int) $limite, [$this->conta, $this->correspondente, $inicio]);
    }

    /**
     * Tendência calculada só com meses completos (o mês corrente é descartado).
     */
    public function tendencia(array $serie)
    {
        $completos = array_values(array_filter($serie, function ($mes) {
            return !$mes['parcial'];
        }));
        $ultimos12 = array_slice($completos, -12);
        $volumes = array_column($ultimos12, 'total');
        $media = count($volumes) ? array_sum($volumes) / count($volumes) : 0;

        $resultado = [
            'classificacao' => 'sem_dados',
            'variacao_mensal' => null,
            'media_mensal' => round($media, 1),
            'volume' => $this->comparaTrimestres($completos, 'total', 'soma'),
            'honorarios' => $this->comparaTrimestres($completos, 'honorarios', 'soma'),
            'ticket' => $this->comparaTrimestres($completos, 'ticket', 'media'),
            'participacao' => $this->comparaTrimestres($completos, 'participacao', 'media'),
            'meses_sem_processo' => count(array_filter($ultimos12, function ($mes) {
                return $mes['total'] === 0;
            })),
            'linha_tendencia' => [],
        ];

        if (count($volumes) < 6 || $media < 1) {
            return $resultado;
        }

        list($inclinacao, $intercepto) = $this->regressaoLinear($volumes);
        $variacao = $inclinacao * 100 / $media;

        $resultado['variacao_mensal'] = round($variacao, 1);
        $resultado['classificacao'] = $variacao > self::LIMIAR_TENDENCIA
            ? 'crescimento'
            : ($variacao < -self::LIMIAR_TENDENCIA ? 'queda' : 'estavel');

        $inicioLinha = count($serie) - count($ultimos12) - (end($serie)['parcial'] ? 1 : 0);
        foreach ($serie as $indice => $mes) {
            $posicao = $indice - $inicioLinha;
            $resultado['linha_tendencia'][] = ($posicao >= 0 && !$mes['parcial'])
                ? round(max(0, $intercepto + $inclinacao * $posicao), 1)
                : null;
        }

        return $resultado;
    }

    /**
     * Frases curtas que resumem a leitura dos indicadores.
     */
    public function leitura(array $tendencia, array $serie)
    {
        if (!array_sum(array_column($serie, 'total'))) {
            return [['flat', 'Nenhum processo com este correspondente no período analisado.']];
        }

        $frases = [];

        switch ($tendencia['classificacao']) {
            case 'crescimento':
                $frases[] = ['up', "Volume em crescimento: cerca de +{$this->numero($tendencia['variacao_mensal'])}% ao mês nos últimos 12 meses completos."];
                break;
            case 'queda':
                $frases[] = ['down', "Volume em queda: cerca de {$this->numero($tendencia['variacao_mensal'])}% ao mês nos últimos 12 meses completos."];
                break;
            case 'estavel':
                $frases[] = ['flat', "Volume estável, com média de {$this->numero($tendencia['media_mensal'])} processos por mês nos últimos 12 meses completos."];
                break;
            default:
                $frases[] = ['flat', 'Ainda não há volume suficiente para calcular tendência (mínimo de 6 meses com processos).'];
        }

        if ($tendencia['volume']['variacao'] !== null) {
            $frases[] = [$this->direcao($tendencia['volume']['variacao'], self::LIMIAR_VARIACAO),
                "Último trimestre: {$tendencia['volume']['atual']} processos, {$this->sinal($tendencia['volume']['variacao'])}% em relação ao trimestre anterior ({$tendencia['volume']['anterior']})."];
        }

        if ($tendencia['participacao']['variacao'] !== null && $tendencia['participacao']['atual'] !== null) {
            $frases[] = [$this->direcao($tendencia['participacao']['variacao'], self::LIMIAR_VARIACAO),
                "Responde por {$this->numero($tendencia['participacao']['atual'])}% dos processos do escritório com correspondente (antes: {$this->numero($tendencia['participacao']['anterior'])}%)."];
        }

        if ($tendencia['ticket']['variacao'] !== null && abs($tendencia['ticket']['variacao']) >= 5) {
            $frases[] = [$this->direcao($tendencia['ticket']['variacao']),
                "Honorário médio por processo " . ($tendencia['ticket']['variacao'] > 0 ? 'subiu' : 'caiu') . " {$this->numero(abs($tendencia['ticket']['variacao']))}% no último trimestre."];
        }

        if ($tendencia['meses_sem_processo'] > 0) {
            $frases[] = ['flat', "{$tendencia['meses_sem_processo']} " . ($tendencia['meses_sem_processo'] === 1 ? 'mês' : 'meses') . ' sem processos nos últimos 12 meses completos.'];
        }

        $checkins = array_values(array_filter(array_column($serie, 'taxa_checkin'), function ($valor) {
            return $valor !== null;
        }));
        if (count($checkins)) {
            $recentes = array_slice($checkins, -3);
            $mediaRecente = array_sum($recentes) / count($recentes);
            $frase = 'Check-in em ' . $this->numero($mediaRecente) . '% dos processos finalizados nos últimos 3 meses';
            $anteriores = array_slice($checkins, -6, max(count($checkins) - 3, 0) >= 3 ? 3 : max(count($checkins) - 3, 0));
            if (count($anteriores)) {
                $mediaAnterior = array_sum($anteriores) / count($anteriores);
                $diferenca = $mediaRecente - $mediaAnterior;
                $frase .= ' (antes: ' . $this->numero($mediaAnterior) . '%)';
                $frases[] = [$this->direcao($diferenca, self::LIMIAR_CHECKIN_PP), $frase . '.'];
            } else {
                $frases[] = ['flat', $frase . '.'];
            }
        }

        return $frases;
    }

    private function comparaTrimestres(array $completos, $campo, $modo)
    {
        $atual = array_slice($completos, -3);
        $anterior = array_slice($completos, -6, 3);

        $agrega = function (array $meses) use ($campo, $modo) {
            $valores = array_values(array_filter(array_column($meses, $campo), function ($valor) {
                return $valor !== null;
            }));
            if (!count($valores)) {
                return null;
            }
            return $modo === 'soma' ? array_sum($valores) : array_sum($valores) / count($valores);
        };

        $valorAtual = count($atual) === 3 ? $agrega($atual) : null;
        $valorAnterior = count($anterior) === 3 ? $agrega($anterior) : null;

        return [
            'atual'    => $valorAtual === null ? null : round($valorAtual, 2),
            'anterior' => $valorAnterior === null ? null : round($valorAnterior, 2),
            'variacao' => ($valorAtual !== null && $valorAnterior) ? round(($valorAtual - $valorAnterior) * 100 / $valorAnterior, 1) : null,
        ];
    }

    private function regressaoLinear(array $valores)
    {
        $n = count($valores);
        $mediaX = ($n - 1) / 2;
        $mediaY = array_sum($valores) / $n;
        $numerador = 0;
        $denominador = 0;

        foreach ($valores as $x => $y) {
            $numerador += ($x - $mediaX) * ($y - $mediaY);
            $denominador += ($x - $mediaX) ** 2;
        }

        $inclinacao = $denominador ? $numerador / $denominador : 0;

        return [$inclinacao, $mediaY - $inclinacao * $mediaX];
    }

    private function rotuloMes(Carbon $data)
    {
        $meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return $meses[$data->month - 1] . '/' . $data->format('y');
    }

    private function numero($valor)
    {
        return number_format((float) $valor, 1, ',', '.');
    }

    private function sinal($valor)
    {
        return ($valor > 0 ? '+' : '') . $this->numero($valor);
    }

    private function direcao($valor, $limiar = 0)
    {
        if (abs($valor) <= $limiar) {
            return 'flat';
        }
        return $valor > 0 ? 'up' : 'down';
    }
}
