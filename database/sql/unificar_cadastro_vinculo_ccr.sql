-- Unifica dados cadastrais do correspondente na entidade do vínculo (tipo 11 / ccr.cd_entidade_ete).
-- Copia da entidade da pessoa (tipo 6) apenas quando o vínculo estiver sem o dado.
-- Não apaga a entidade da pessoa (continua usada no login).

-- CPF / CNPJ / OAB / RG
INSERT INTO identificacao_ide (
    cd_entidade_ete,
    cd_conta_con,
    cd_tipo_identificacao_tpi,
    nu_identificacao_ide,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    COALESCE(ide_pessoa.cd_conta_con, ccr.cd_correspondente_cor),
    ide_pessoa.cd_tipo_identificacao_tpi,
    ide_pessoa.nu_identificacao_ide,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_vinculo
    ON ete_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
   AND ete_vinculo.deleted_at IS NULL
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN identificacao_ide ide_pessoa
    ON ide_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND ide_pessoa.cd_tipo_identificacao_tpi IN (1, 2, 3, 7)
   AND ide_pessoa.deleted_at IS NULL
   AND NULLIF(TRIM(ide_pessoa.nu_identificacao_ide), '') IS NOT NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM identificacao_ide ide_vinculo
        WHERE ide_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND ide_vinculo.cd_tipo_identificacao_tpi = ide_pessoa.cd_tipo_identificacao_tpi
          AND ide_vinculo.deleted_at IS NULL
          AND NULLIF(TRIM(ide_vinculo.nu_identificacao_ide), '') IS NOT NULL
  );

-- Endereço (somente se o vínculo não tiver endereço)
INSERT INTO endereco_ede (
    cd_entidade_ete,
    cd_conta_con,
    nu_cep_ede,
    dc_logradouro_ede,
    nu_numero_ede,
    nm_bairro_ede,
    dc_complemento_ede,
    cd_cidade_cde,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    COALESCE(ede_pessoa.cd_conta_con, ccr.cd_correspondente_cor),
    ede_pessoa.nu_cep_ede,
    ede_pessoa.dc_logradouro_ede,
    ede_pessoa.nu_numero_ede,
    ede_pessoa.nm_bairro_ede,
    ede_pessoa.dc_complemento_ede,
    ede_pessoa.cd_cidade_cde,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN endereco_ede ede_pessoa
    ON ede_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND ede_pessoa.deleted_at IS NULL
   AND NULLIF(TRIM(ede_pessoa.dc_logradouro_ede), '') IS NOT NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM endereco_ede ede_vinculo
        WHERE ede_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND ede_vinculo.deleted_at IS NULL
          AND NULLIF(TRIM(ede_vinculo.dc_logradouro_ede), '') IS NOT NULL
  );

-- Telefones (somente se o vínculo não tiver nenhum)
INSERT INTO fone_fon (
    cd_entidade_ete,
    cd_conta_con,
    cd_tipo_fone_tfo,
    nu_fone_fon,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    COALESCE(fon_pessoa.cd_conta_con, ccr.cd_correspondente_cor),
    fon_pessoa.cd_tipo_fone_tfo,
    fon_pessoa.nu_fone_fon,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN fone_fon fon_pessoa
    ON fon_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND fon_pessoa.deleted_at IS NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM fone_fon fon_vinculo
        WHERE fon_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND fon_vinculo.deleted_at IS NULL
  );

-- E-mails (somente se o vínculo não tiver nenhum)
INSERT INTO endereco_eletronico_ele (
    cd_entidade_ete,
    cd_conta_con,
    cd_tipo_endereco_eletronico_tee,
    dc_endereco_eletronico_ede,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    COALESCE(email_pessoa.cd_conta_con, ccr.cd_correspondente_cor),
    email_pessoa.cd_tipo_endereco_eletronico_tee,
    email_pessoa.dc_endereco_eletronico_ede,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN endereco_eletronico_ele email_pessoa
    ON email_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND email_pessoa.deleted_at IS NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM endereco_eletronico_ele email_vinculo
        WHERE email_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND email_vinculo.deleted_at IS NULL
  );

-- Dados bancários (somente se o vínculo não tiver nenhum)
INSERT INTO dados_bancarios_dba (
    cd_entidade_ete,
    cd_conta_con,
    nm_titular_dba,
    nu_cpf_cnpj_dba,
    nu_agencia_dba,
    nu_conta_dba,
    cd_banco_ban,
    dc_pix_dba,
    cd_tipo_conta_tcb,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    COALESCE(dba_pessoa.cd_conta_con, ccr.cd_correspondente_cor),
    dba_pessoa.nm_titular_dba,
    dba_pessoa.nu_cpf_cnpj_dba,
    dba_pessoa.nu_agencia_dba,
    dba_pessoa.nu_conta_dba,
    dba_pessoa.cd_banco_ban,
    dba_pessoa.dc_pix_dba,
    dba_pessoa.cd_tipo_conta_tcb,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN dados_bancarios_dba dba_pessoa
    ON dba_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND dba_pessoa.deleted_at IS NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM dados_bancarios_dba dba_vinculo
        WHERE dba_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND dba_vinculo.deleted_at IS NULL
  );

-- Comarcas (somente se o vínculo não tiver nenhuma)
INSERT INTO cidade_atuacao_cat (
    cd_entidade_ete,
    cd_cidade_cde,
    fl_origem_cat,
    created_at,
    updated_at
)
SELECT
    ccr.cd_entidade_ete,
    cat_pessoa.cd_cidade_cde,
    cat_pessoa.fl_origem_cat,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN cidade_atuacao_cat cat_pessoa
    ON cat_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND cat_pessoa.deleted_at IS NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM cidade_atuacao_cat cat_vinculo
        WHERE cat_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND cat_vinculo.deleted_at IS NULL
  );

-- Despesas reembolsáveis (somente se o vínculo não tiver nenhuma para o escritório)
INSERT INTO reembolso_tipo_despesa_rtd (
    cd_conta_con,
    cd_entidade_ete,
    cd_tipo_despesa_tds,
    created_at,
    updated_at
)
SELECT
    rtd_pessoa.cd_conta_con,
    ccr.cd_entidade_ete,
    rtd_pessoa.cd_tipo_despesa_tds,
    NOW(),
    NOW()
FROM conta_correspondente_ccr ccr
JOIN entidade_ete ete_pessoa
    ON ete_pessoa.cd_conta_con = ccr.cd_correspondente_cor
   AND ete_pessoa.cd_tipo_entidade_tpe = 6
   AND ete_pessoa.deleted_at IS NULL
JOIN reembolso_tipo_despesa_rtd rtd_pessoa
    ON rtd_pessoa.cd_entidade_ete = ete_pessoa.cd_entidade_ete
   AND rtd_pessoa.cd_conta_con = ccr.cd_conta_con
   AND rtd_pessoa.deleted_at IS NULL
WHERE ccr.deleted_at IS NULL
  AND ccr.cd_entidade_ete IS NOT NULL
  AND NOT EXISTS (
        SELECT 1
        FROM reembolso_tipo_despesa_rtd rtd_vinculo
        WHERE rtd_vinculo.cd_entidade_ete = ccr.cd_entidade_ete
          AND rtd_vinculo.cd_conta_con = ccr.cd_conta_con
          AND rtd_vinculo.deleted_at IS NULL
  );
