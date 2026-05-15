-- =====================================================================
-- [CHATPRO / WHATSAPP] Tabela única para histórico de mensagens
-- (entradas via webhook + saídas via sendText). Uma linha = 1 mensagem.
--
-- Convenção do projeto: sufixo _wmm para colunas desta tabela.
-- Reutiliza nu_telefone_whatsapp_con da conta_con para o telefone
-- do correspondente (cada correspondente JÁ é uma linha de conta_con).
-- =====================================================================

CREATE TABLE IF NOT EXISTS whatsapp_mensagem_wmm (
    cd_whatsapp_mensagem_wmm BIGSERIAL    PRIMARY KEY,

    -- Conta dona da instância ChatPro (escritório). Em instalação
    -- single-tenant é sempre a mesma; deixamos o vínculo pra ficar
    -- consistente com o padrão multi-conta do resto do schema.
    cd_conta_con             INTEGER      NOT NULL
        REFERENCES conta_con(cd_conta_con),

    -- 'I' = inbound  (recebida via webhook)
    -- 'O' = outbound (enviada via sendText)
    -- 'A' = ack/status update (delivered, read, failed)
    tp_direcao_wmm           CHAR(1)      NOT NULL,

    -- Telefones (E.164 sem '+', ex.: 5548999999999)
    nu_telefone_origem_wmm   VARCHAR(20)  NULL,
    nu_telefone_destino_wmm  VARCHAR(20)  NULL,

    -- Conteúdo da mensagem (texto puro). Para mídias guardamos só o
    -- body/caption; o binário fica no payload bruto.
    ds_mensagem_wmm          TEXT         NULL,

    -- Tipo da mensagem ChatPro: text, image, audio, video, document,
    -- location, contact, ack, status, etc.
    ds_tipo_wmm              VARCHAR(30)  NULL,

    -- Identificador único da mensagem na origem (ChatPro/WhatsApp).
    -- Usado para idempotência: se o mesmo message_id chegar duas vezes
    -- (re-entrega do webhook), ignoramos a segunda.
    ds_message_id_wmm        VARCHAR(120) NULL,

    -- Status atual: sent, delivered, read, failed, received
    ds_status_wmm            VARCHAR(30)  NULL,

    -- Payload bruto recebido/enviado, para auditoria e debug.
    ds_payload_raw_wmm       JSONB        NULL,

    -- Vínculos opcionais (preenchidos quando dá pra resolver o contexto).
    cd_processo_pro          INTEGER      NULL
        REFERENCES processo_pro(cd_processo_pro),
    cd_correspondente_cor    INTEGER      NULL
        REFERENCES conta_con(cd_conta_con),
    cd_processo_checkin_pck  BIGINT       NULL
        REFERENCES processo_checkin_pck(cd_processo_checkin_pck),

    -- Quando o evento ocorreu na origem (timestamp do ChatPro/WhatsApp).
    dt_evento_wmm            TIMESTAMP    NULL,

    created_at               TIMESTAMP    NULL,
    updated_at               TIMESTAMP    NULL,
    deleted_at               TIMESTAMP    NULL
);

-- Idempotência: nunca grava o mesmo message_id duas vezes (apenas
-- quando preenchido — webhooks de status nem sempre trazem).
CREATE UNIQUE INDEX IF NOT EXISTS uq_wmm_message_id
    ON whatsapp_mensagem_wmm (ds_message_id_wmm)
 WHERE ds_message_id_wmm IS NOT NULL
   AND deleted_at IS NULL;

-- Buscas por conversa (telefone) e janela temporal.
CREATE INDEX IF NOT EXISTS ix_wmm_conta_origem
    ON whatsapp_mensagem_wmm (cd_conta_con, nu_telefone_origem_wmm, created_at DESC);

CREATE INDEX IF NOT EXISTS ix_wmm_conta_destino
    ON whatsapp_mensagem_wmm (cd_conta_con, nu_telefone_destino_wmm, created_at DESC);

CREATE INDEX IF NOT EXISTS ix_wmm_processo
    ON whatsapp_mensagem_wmm (cd_processo_pro)
 WHERE cd_processo_pro IS NOT NULL;

CREATE INDEX IF NOT EXISTS ix_wmm_correspondente
    ON whatsapp_mensagem_wmm (cd_correspondente_cor)
 WHERE cd_correspondente_cor IS NOT NULL;

COMMENT ON TABLE  whatsapp_mensagem_wmm IS 'Histórico unificado de mensagens WhatsApp (ChatPro): inbound, outbound e acks.';
COMMENT ON COLUMN whatsapp_mensagem_wmm.tp_direcao_wmm IS 'I=inbound, O=outbound, A=ack/status';
COMMENT ON COLUMN whatsapp_mensagem_wmm.ds_message_id_wmm IS 'ID único da mensagem na origem (idempotência do webhook).';
