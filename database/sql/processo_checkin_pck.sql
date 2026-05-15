-- =============================================================================
-- [CHECK-IN] processo_checkin_pck
-- Registro de check-in do correspondente ao chegar no fórum para resolver um
-- processo. Diferente do Timemark (processo_comprovacao_pcm), NÃO armazena
-- foto: somente data/hora + GPS (lat/lng/precisão) + endereço opcional.
--
-- Apenas UM check-in por processo (constraint UNIQUE em cd_processo_pro
-- considerando apenas linhas não deletadas via soft-delete).
-- =============================================================================

CREATE TABLE IF NOT EXISTS processo_checkin_pck (
    cd_processo_checkin_pck   BIGSERIAL       NOT NULL PRIMARY KEY,

    cd_processo_pro           BIGINT          NOT NULL,
    cd_conta_con              BIGINT          NOT NULL,
    cd_correspondente_cor     BIGINT          NULL,
    cd_entidade_ete           BIGINT          NULL,
    cd_user_checkin_pck       BIGINT          NOT NULL,

    dt_checkin_pck            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    nu_latitude_pck           NUMERIC(10, 7)  NULL,
    nu_longitude_pck          NUMERIC(10, 7)  NULL,
    nu_precisao_metros_pck    NUMERIC(10, 2)  NULL,

    ds_endereco_pck           VARCHAR(500)    NULL,
    nu_distancia_metros_pck   NUMERIC(10, 2)  NULL,
    ds_observacao_pck         TEXT            NULL,

    created_at                TIMESTAMP       NULL,
    updated_at                TIMESTAMP       NULL,
    deleted_at                TIMESTAMP       NULL,

    CONSTRAINT fk_pck_processo
        FOREIGN KEY (cd_processo_pro)
        REFERENCES processo_pro (cd_processo_pro),

    CONSTRAINT fk_pck_conta
        FOREIGN KEY (cd_conta_con)
        REFERENCES conta_con (cd_conta_con),

    CONSTRAINT fk_pck_user
        FOREIGN KEY (cd_user_checkin_pck)
        REFERENCES users (id)
);

-- Índices auxiliares
CREATE INDEX IF NOT EXISTS ix_pck_processo
    ON processo_checkin_pck (cd_processo_pro);

CREATE INDEX IF NOT EXISTS ix_pck_conta
    ON processo_checkin_pck (cd_conta_con);

CREATE INDEX IF NOT EXISTS ix_pck_correspondente
    ON processo_checkin_pck (cd_correspondente_cor);

CREATE INDEX IF NOT EXISTS ix_pck_dt_checkin
    ON processo_checkin_pck (dt_checkin_pck);

-- Garante apenas UM check-in ativo (não soft-deletado) por processo.
CREATE UNIQUE INDEX IF NOT EXISTS uq_pck_processo_ativo
    ON processo_checkin_pck (cd_processo_pro)
    WHERE deleted_at IS NULL;

COMMENT ON TABLE  processo_checkin_pck                         IS 'Check-in do correspondente ao chegar no fórum para resolver um processo (sem foto).';
COMMENT ON COLUMN processo_checkin_pck.dt_checkin_pck          IS 'Data/hora do check-in (capturado no cliente, validado no servidor).';
COMMENT ON COLUMN processo_checkin_pck.nu_latitude_pck         IS 'Latitude reportada pelo navegador (geolocation).';
COMMENT ON COLUMN processo_checkin_pck.nu_longitude_pck        IS 'Longitude reportada pelo navegador (geolocation).';
COMMENT ON COLUMN processo_checkin_pck.nu_precisao_metros_pck  IS 'Precisão (accuracy) em metros reportada pelo navegador.';
COMMENT ON COLUMN processo_checkin_pck.nu_distancia_metros_pck IS 'Distância (m) entre o ponto do check-in e o local cadastrado do fórum (calculada server-side, opcional).';
