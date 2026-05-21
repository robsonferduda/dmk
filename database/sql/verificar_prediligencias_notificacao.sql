-- [VERIFICAÇÃO] Processos que serão notificados pelo lembrete pré-diligência
-- Filtrando apenas o escritório cd_conta_con = 64 (fase de testes).
-- Para produção: remova o filtro "AND p.cd_conta_con = 64".
--
-- Situação possíveis:
--   ENVIARÁ          → tudo ok, mensagem será disparada
--   SEM CORRESPONDENTE → processo sem cd_correspondente_cor
--   SEM WHATSAPP     → correspondente não tem nu_telefone_whatsapp_con
--   SEM CHATPRO      → escritório sem instance_id ou token configurado
--   CHATPRO INATIVO  → escritório com fl_chatpro_ativo_con <> true

SELECT
    p.cd_processo_pro,
    p.nu_processo_pro,
    p.dt_prazo_fatal_pro,
    p.hr_audiencia_pro,

    -- Escritório
    esc.cd_conta_con                                    AS cd_escritorio,
    esc.nm_razao_social_con                             AS nm_escritorio,
    CASE
        WHEN esc.fl_chatpro_ativo_con IS NOT TRUE           THEN 'CHATPRO INATIVO'
        WHEN esc.ds_chatpro_instance_id_con IS NULL         THEN 'SEM CHATPRO'
        WHEN esc.ds_chatpro_token_con       IS NULL         THEN 'SEM CHATPRO'
        ELSE '✔'
    END                                                 AS chatpro_escritorio,

    -- Correspondente
    cor.cd_conta_con                                    AS cd_correspondente,
    cor.nm_razao_social_con                             AS nm_correspondente,
    cor.nu_telefone_whatsapp_con                        AS whatsapp,

    -- Situação final
    CASE
        WHEN p.cd_correspondente_cor IS NULL                              THEN 'SEM CORRESPONDENTE'
        WHEN cor.cd_conta_con IS NULL                                     THEN 'SEM CORRESPONDENTE'
        WHEN esc.fl_chatpro_ativo_con IS NOT TRUE                         THEN 'CHATPRO INATIVO'
        WHEN esc.ds_chatpro_instance_id_con IS NULL
          OR esc.ds_chatpro_token_con       IS NULL                       THEN 'SEM CHATPRO'
        WHEN cor.nu_telefone_whatsapp_con IS NULL
          OR cor.nu_telefone_whatsapp_con = ''                            THEN 'SEM WHATSAPP'
        ELSE 'ENVIARÁ'
    END                                                 AS situacao

FROM processo_pro p

-- Escritório da diligência
JOIN conta_con esc ON esc.cd_conta_con = p.cd_conta_con

-- Correspondente (LEFT: mostra mesmo sem correspondente)
LEFT JOIN conta_con cor ON cor.cd_conta_con = p.cd_correspondente_cor

WHERE p.cd_conta_con = 64                          -- fase de testes: fixar escritório
  AND p.dt_prazo_fatal_pro = CURRENT_DATE + 1      -- processos de amanhã
  AND p.deleted_at IS NULL

ORDER BY situacao, p.dt_prazo_fatal_pro, p.hr_audiencia_pro;
