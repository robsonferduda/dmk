-- [MIGRAÇÃO] Copiar telefone celular dos correspondentes para nu_telefone_whatsapp_con
--
-- Contexto: O campo nu_telefone_whatsapp_con foi criado para registrar o WhatsApp
-- dos correspondentes. Muitos deles já possuem um celular cadastrado em fone_fon
-- mas não preencheram o campo WhatsApp. Este script copia o primeiro celular
-- encontrado (cd_tipo_fone_tfo = 1) para nu_telefone_whatsapp_con, apenas para
-- correspondentes (cd_nivel_niv = 3) que ainda não possuem WhatsApp cadastrado.
--
-- O número é armazenado somente com dígitos (regexp_replace remove formatação).
-- Rodar em ambiente de homologação antes de produção.

-- ============================================================
-- 1. PREVIEW — quantos serão afetados e quais números serão copiados
-- ============================================================
-- Regra de formatação (E.164 sem '+'):
--   10 dígitos → número antigo sem o 9: DDD(2) + 8 dígitos → adiciona 9 após DDD + prefixo 55
--   11 dígitos → número moderno: DDD(2) + 9 + 8 dígitos     → apenas adiciona prefixo 55
--   outros     → mantém apenas os dígitos (caso inesperado)
SELECT
    c.cd_conta_con,
    c.nm_razao_social_con,
    f.nu_fone_fon                                                           AS fone_original,
    regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')                       AS fone_digits,
    CASE
        WHEN length(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')) = 10
            THEN '55'
                 || substring(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g'), 1, 2)
                 || '9'
                 || substring(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g'), 3)
        WHEN length(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')) = 11
            THEN '55' || regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')
        ELSE regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')
    END                                                                     AS whatsapp_formatado
FROM conta_con c
JOIN (
    SELECT DISTINCT ON (cd_conta_con)
        cd_conta_con,
        nu_fone_fon
    FROM fone_fon
    WHERE cd_tipo_fone_tfo = 1   -- CELULAR
      AND deleted_at IS NULL
    ORDER BY cd_conta_con, cd_fone_fon ASC
) f ON f.cd_conta_con = c.cd_conta_con
WHERE c.fl_correspondente_con = 'S'
  AND (c.nu_telefone_whatsapp_con IS NULL OR c.nu_telefone_whatsapp_con = '')
ORDER BY c.nm_razao_social_con;

-- ============================================================
-- 2. UPDATE — executa a migração (mesma regra de formatação do SELECT)
-- ============================================================
UPDATE conta_con c
SET    nu_telefone_whatsapp_con = CASE
           WHEN length(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')) = 10
               THEN '55'
                    || substring(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g'), 1, 2)
                    || '9'
                    || substring(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g'), 3)
           WHEN length(regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')) = 11
               THEN '55' || regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')
           ELSE regexp_replace(f.nu_fone_fon, '[^0-9]', '', 'g')
       END
FROM (
    SELECT DISTINCT ON (cd_conta_con)
        cd_conta_con,
        nu_fone_fon
    FROM fone_fon
    WHERE cd_tipo_fone_tfo = 1   -- CELULAR
      AND deleted_at IS NULL
    ORDER BY cd_conta_con, cd_fone_fon ASC
) f
WHERE c.cd_conta_con = f.cd_conta_con
  AND c.fl_correspondente_con = 'S'
  AND (c.nu_telefone_whatsapp_con IS NULL OR c.nu_telefone_whatsapp_con = '');

-- ============================================================
-- 3. Ativar fl_chatpro_ativo_con para todos os correspondentes
--    que possuem nu_telefone_whatsapp_con preenchido
-- ============================================================
UPDATE conta_con
SET    fl_chatpro_ativo_con = true
WHERE  fl_correspondente_con = 'S'
  AND  nu_telefone_whatsapp_con IS NOT NULL
  AND  nu_telefone_whatsapp_con <> ''
  AND  (fl_chatpro_ativo_con IS NULL OR fl_chatpro_ativo_con <> true);
