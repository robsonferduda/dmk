-- Campanha de atualização cadastral dos correspondentes (equivalente à migration 2026_10_07_000001).

CREATE TABLE IF NOT EXISTS campanha_cadastro_cmc (
    cd_campanha_cadastro_cmc BIGSERIAL PRIMARY KEY,
    cd_conta_con             INTEGER      NOT NULL,
    nm_campanha_cmc          VARCHAR(150) NOT NULL,
    dc_criterio_cmc          VARCHAR(255),
    dc_status_cmc            VARCHAR(20)  NOT NULL DEFAULT 'pausada',
    nu_prazo_dias_cmc        INTEGER      NOT NULL DEFAULT 7,
    cd_usuario_cmc           INTEGER,
    dt_inicio_cmc            TIMESTAMP(0),
    dt_conclusao_cmc         TIMESTAMP(0),
    created_at               TIMESTAMP(0),
    updated_at               TIMESTAMP(0)
);
CREATE INDEX IF NOT EXISTS campanha_cadastro_cmc_cd_conta_con_index ON campanha_cadastro_cmc (cd_conta_con);

CREATE TABLE IF NOT EXISTS campanha_cadastro_envio_cme (
    cd_campanha_cadastro_envio_cme BIGSERIAL PRIMARY KEY,
    cd_campanha_cadastro_cmc       BIGINT       NOT NULL,
    cd_conta_correspondente_ccr    INTEGER      NOT NULL,
    nm_destinatario_cme            VARCHAR(255),
    dc_email_cme                   VARCHAR(255) NOT NULL,
    dt_ultimo_processo_cme         DATE,
    nu_ordem_cme                   INTEGER      NOT NULL DEFAULT 0,
    dc_token_cme                   VARCHAR(64)  NOT NULL UNIQUE,
    dc_status_cme                  VARCHAR(20)  NOT NULL DEFAULT 'pendente',
    nu_tentativas_cme              INTEGER      NOT NULL DEFAULT 0,
    nu_envios_cme                  INTEGER      NOT NULL DEFAULT 0,
    dc_erro_cme                    TEXT,
    dt_envio_cme                   TIMESTAMP(0),
    dt_prazo_cme                   DATE,
    dt_abertura_cme                TIMESTAMP(0),
    dt_clique_cme                  TIMESTAMP(0),
    dt_confirmacao_cme             TIMESTAMP(0),
    dc_pendencias_cme              TEXT,
    dt_pendencias_cme              TIMESTAMP(0),
    created_at                     TIMESTAMP(0),
    updated_at                     TIMESTAMP(0),
    CONSTRAINT campanha_cadastro_envio_cme_unico UNIQUE (cd_campanha_cadastro_cmc, cd_conta_correspondente_ccr)
);
CREATE INDEX IF NOT EXISTS campanha_cadastro_envio_cme_cd_campanha_cadastro_cmc_index ON campanha_cadastro_envio_cme (cd_campanha_cadastro_cmc);
CREATE INDEX IF NOT EXISTS campanha_cadastro_envio_cme_cd_conta_correspondente_ccr_index ON campanha_cadastro_envio_cme (cd_conta_correspondente_ccr);
CREATE INDEX IF NOT EXISTS campanha_cadastro_envio_cme_dc_status_cme_index ON campanha_cadastro_envio_cme (dc_status_cme);
CREATE INDEX IF NOT EXISTS campanha_cadastro_envio_cme_dt_envio_cme_index ON campanha_cadastro_envio_cme (dt_envio_cme);
