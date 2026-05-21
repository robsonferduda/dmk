-- [VERIFICAÇÃO] Dados necessários para o comando whatsapp:lembrete-prediligencias
-- Substitua 97561 pelo cd_processo_pro desejado.

-- ============================================================
-- 1. PROCESSO — campos básicos usados na mensagem
-- ============================================================
SELECT
    p.cd_processo_pro,
    p.nu_processo_pro,
    p.nm_reu_pro,
    p.dt_prazo_fatal_pro,
    p.hr_audiencia_pro,
    p.cd_conta_con           AS cd_escritorio,
    p.cd_correspondente_cor,
    v.nm_vara_var,
    c.nm_cidade_cde,
    e.sg_estado_est
FROM processo_pro p
LEFT JOIN vara_var      v ON v.cd_vara_var   = p.cd_vara_var
LEFT JOIN cidade_cde    c ON c.cd_cidade_cde = p.cd_cidade_cde
LEFT JOIN estado_est    e ON e.cd_estado_est = c.cd_estado_est
WHERE p.cd_processo_pro = 97561;

-- ============================================================
-- 2. ESCRITÓRIO — precisa ter ChatPro configurado e ativo
-- ============================================================
SELECT
    cc.cd_conta_con,
    cc.nm_razao_social_con,
    cc.fl_chatpro_ativo_con,
    CASE WHEN cc.ds_chatpro_instance_id_con IS NOT NULL THEN '✔ preenchido' ELSE '✗ NULL' END AS instance_id,
    CASE WHEN cc.ds_chatpro_token_con       IS NOT NULL THEN '✔ preenchido' ELSE '✗ NULL' END AS token
FROM conta_con cc
WHERE cc.cd_conta_con = (
    SELECT cd_conta_con FROM processo_pro WHERE cd_processo_pro = 97561
);

-- ============================================================
-- 3. CORRESPONDENTE — precisa ter nu_telefone_whatsapp_con
-- ============================================================
SELECT
    cc.cd_conta_con,
    cc.nm_razao_social_con,
    cc.nu_telefone_whatsapp_con,
    cc.fl_chatpro_ativo_con,
    cc.fl_correspondente_con
FROM conta_con cc
WHERE cc.cd_conta_con = (
    SELECT cd_correspondente_cor FROM processo_pro WHERE cd_processo_pro = 97561
);

-- ============================================================
-- 4. HISTÓRICO — já foi enviado lembrete de pré-diligência para este processo?
-- ============================================================
SELECT
    wmm.cd_whatsapp_mensagem_wmm,
    wmm.ds_tipo_wmm,
    wmm.ds_status_wmm,
    wmm.nu_telefone_destino_wmm,
    wmm.created_at
FROM whatsapp_mensagem_wmm wmm
WHERE wmm.cd_processo_pro = 97561
  AND wmm.ds_tipo_wmm     = 'lembrete_prediligencia'
ORDER BY wmm.created_at DESC;
