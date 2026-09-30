<?php

namespace App\Services\Correspondente;

use DB;

class CorrespondenteBusca
{
    /**
     * Lista os correspondentes vinculados ao escritório, uma linha por vínculo.
     *
     * Filtros aceitos: nome, cd_categoria_correspondente_cac, identificacao, cd_estado_est, cd_cidade_cde.
     * A cidade só é considerada junto com o estado (comarcas de atuação).
     */
    public function buscar($conta, array $filtros = [])
    {
        $conta = (int) $conta;
        $params = [];

        $sql = "SELECT t1.cd_conta_correspondente_ccr,
                       t1.cd_conta_con,
                       t1.cd_correspondente_cor,
                       t1.cd_entidade_ete,
                       t3.nu_identificacao_ide,
                       t_oab.nu_identificacao_ide AS nu_oab_ide,
                       t1.nm_conta_correspondente_ccr,
                       t4.cd_categoria_correspondente_cac,
                       t4.dc_categoria_correspondente_cac,
                       t5.cd_cidade_cde,
                       t5.nm_cidade_cde,
                       t10.email,
                       t4.color_cac,
                       cor.fl_advogado_con
                FROM conta_correspondente_ccr t1
                LEFT JOIN conta_con cor
                    ON cor.cd_conta_con = t1.cd_correspondente_cor
                LEFT JOIN categoria_correspondente_cac t4
                    ON t1.cd_categoria_correspondente_cac = t4.cd_categoria_correspondente_cac
                LEFT JOIN (
                    SELECT DISTINCT ON (ide.cd_entidade_ete)
                           ide.cd_entidade_ete,
                           ide.nu_identificacao_ide
                    FROM identificacao_ide ide
                    INNER JOIN conta_correspondente_ccr ccr
                        ON ccr.cd_entidade_ete = ide.cd_entidade_ete
                       AND ccr.cd_conta_con = {$conta}
                       AND ccr.deleted_at IS NULL
                    WHERE ide.cd_tipo_identificacao_tpi IN (1, 7)
                      AND ide.deleted_at IS NULL
                    ORDER BY ide.cd_entidade_ete, ide.cd_identificacao_ide
                ) t3 ON t3.cd_entidade_ete = t1.cd_entidade_ete
                LEFT JOIN (
                    SELECT DISTINCT ON (ide.cd_entidade_ete)
                           ide.cd_entidade_ete,
                           ide.nu_identificacao_ide
                    FROM identificacao_ide ide
                    INNER JOIN conta_correspondente_ccr ccr
                        ON ccr.cd_entidade_ete = ide.cd_entidade_ete
                       AND ccr.cd_conta_con = {$conta}
                       AND ccr.deleted_at IS NULL
                    WHERE ide.cd_tipo_identificacao_tpi = 3
                      AND ide.deleted_at IS NULL
                    ORDER BY ide.cd_entidade_ete, ide.cd_identificacao_ide
                ) t_oab ON t_oab.cd_entidade_ete = t1.cd_entidade_ete
                LEFT JOIN (
                    SELECT DISTINCT ON (cat.cd_entidade_ete)
                           cat.cd_entidade_ete,
                           cat.cd_cidade_cde,
                           cde.nm_cidade_cde
                    FROM cidade_atuacao_cat cat
                    INNER JOIN conta_correspondente_ccr ccr
                        ON ccr.cd_entidade_ete = cat.cd_entidade_ete
                       AND ccr.cd_conta_con = {$conta}
                       AND ccr.deleted_at IS NULL
                    INNER JOIN cidade_cde cde ON cat.cd_cidade_cde = cde.cd_cidade_cde
                    WHERE cat.fl_origem_cat = 'S'
                      AND cat.deleted_at IS NULL
                    ORDER BY cat.cd_entidade_ete, cat.cd_cidade_atuacao_cat
                ) t5 ON t5.cd_entidade_ete = t1.cd_entidade_ete
                LEFT JOIN (
                    SELECT DISTINCT ON (u.cd_conta_con)
                           u.cd_conta_con,
                           u.email
                    FROM users u
                    INNER JOIN conta_correspondente_ccr ccr
                        ON ccr.cd_correspondente_cor = u.cd_conta_con
                       AND ccr.cd_conta_con = {$conta}
                       AND ccr.deleted_at IS NULL
                    WHERE u.cd_nivel_niv = 3
                    ORDER BY u.cd_conta_con, u.id
                ) t10 ON t10.cd_conta_con = t1.cd_correspondente_cor
                WHERE t1.deleted_at IS NULL
                  AND t1.cd_conta_con = {$conta} ";

        $nome = trim((string) ($filtros['nome'] ?? ''));
        if ($nome !== '') {
            $sql .= " AND t1.nm_conta_correspondente_ccr ILIKE ? ";
            $params[] = '%' . $nome . '%';
        }

        if (!empty($filtros['cd_categoria_correspondente_cac'])) {
            $sql .= " AND t4.cd_categoria_correspondente_cac = ? ";
            $params[] = (int) $filtros['cd_categoria_correspondente_cac'];
        }

        $identificacao = trim((string) ($filtros['identificacao'] ?? ''));
        if ($identificacao !== '') {
            $sql .= " AND t3.nu_identificacao_ide = ? ";
            $params[] = $identificacao;
        }

        if (!empty($filtros['cd_estado_est'])) {
            $condicaoCidade = '';
            $paramsCidade = [];

            if (!empty($filtros['cd_cidade_cde'])) {
                $condicaoCidade = ' AND t7.cd_cidade_cde = ? ';
                $paramsCidade[] = (int) $filtros['cd_cidade_cde'];
            }

            $sql .= " AND t1.cd_entidade_ete IN (
                        SELECT t8.cd_entidade_ete
                        FROM cidade_atuacao_cat t7, conta_correspondente_ccr t8, cidade_cde t9
                        WHERE t7.cd_entidade_ete = t8.cd_entidade_ete
                          AND t7.cd_cidade_cde = t9.cd_cidade_cde
                          {$condicaoCidade}
                          AND t9.cd_estado_est = ?
                          AND t8.cd_conta_con = {$conta}
                          AND t7.deleted_at IS NULL) ";

            $params = array_merge($params, $paramsCidade, [(int) $filtros['cd_estado_est']]);
        }

        $sql .= ' ORDER BY t1.nm_conta_correspondente_ccr';

        return DB::select($sql, $params);
    }

    /**
     * Remove da exportação os correspondentes da categoria INATIVO.
     */
    public function semInativos(array $correspondentes)
    {
        return array_values(array_filter($correspondentes, function ($correspondente) {
            $categoria = trim((string) ($correspondente->dc_categoria_correspondente_cac ?? ''));
            return strcasecmp($categoria, 'INATIVO') !== 0;
        }));
    }
}
