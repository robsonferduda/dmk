-- =============================================================================
-- [CHECK-IN / WHATSAPP] Configuração ChatPro por conta.
--
-- Cada conta (escritório) tem sua própria instância ChatPro e um número
-- de WhatsApp destino para receber as notificações automáticas
-- (check-in do correspondente, lembretes diários de audiência etc.).
-- =============================================================================

ALTER TABLE conta_con
    ADD COLUMN IF NOT EXISTS nu_telefone_whatsapp_con   VARCHAR(20)  NULL,
    ADD COLUMN IF NOT EXISTS ds_chatpro_instance_id_con VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS ds_chatpro_token_con       VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS fl_chatpro_ativo_con       CHAR(1)      NOT NULL DEFAULT 'N';

COMMENT ON COLUMN conta_con.nu_telefone_whatsapp_con   IS 'Telefone WhatsApp do escritório (destino) no formato E.164 ex.: 5548999999999.';
COMMENT ON COLUMN conta_con.ds_chatpro_instance_id_con IS 'ID da instância ChatPro da conta.';
COMMENT ON COLUMN conta_con.ds_chatpro_token_con       IS 'Token (Authorization) da instância ChatPro da conta.';
COMMENT ON COLUMN conta_con.fl_chatpro_ativo_con       IS 'Indica se a integração ChatPro está ativa para a conta (S/N).';
