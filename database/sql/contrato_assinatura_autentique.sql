-- Assinatura eletrônica de contratos de correspondente (Autentique)
-- Equivalente à migration 2026_10_01_000001_create_contrato_assinatura_tables. Execute se a migration não puder rodar via artisan.

CREATE TABLE IF NOT EXISTS contrato_assinatura_cas (
  cd_contrato_assinatura_cas BIGSERIAL PRIMARY KEY,
  cd_conta_correspondente_ccr INTEGER NOT NULL,
  cd_conta_con INTEGER NOT NULL,
  cd_documento_autentique_cas VARCHAR(100) NULL UNIQUE,
  dc_status_cas VARCHAR(20) NOT NULL,
  dc_canal_cas VARCHAR(10) NOT NULL,
  dc_caminho_original_cas VARCHAR(255) NOT NULL,
  dc_caminho_assinado_cas VARCHAR(255) NULL,
  fl_sandbox_cas BOOLEAN NOT NULL DEFAULT FALSE,
  dc_erro_cas TEXT NULL,
  cd_usuario_envio_cas INTEGER NULL,
  dt_envio_cas TIMESTAMP NULL,
  dt_concluido_cas TIMESTAMP NULL,
  dt_recusado_cas TIMESTAMP NULL,
  dt_cancelado_cas TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
CREATE INDEX IF NOT EXISTS contrato_assinatura_cas_ccr_index ON contrato_assinatura_cas (cd_conta_correspondente_ccr);
CREATE INDEX IF NOT EXISTS contrato_assinatura_cas_conta_index ON contrato_assinatura_cas (cd_conta_con);
CREATE INDEX IF NOT EXISTS contrato_assinatura_cas_status_index ON contrato_assinatura_cas (dc_status_cas);

CREATE TABLE IF NOT EXISTS contrato_signatario_csi (
  cd_contrato_signatario_csi BIGSERIAL PRIMARY KEY,
  cd_contrato_assinatura_cas BIGINT NOT NULL,
  dc_papel_csi VARCHAR(20) NOT NULL,
  nu_ordem_csi SMALLINT NOT NULL,
  nm_signatario_csi VARCHAR(255) NULL,
  dc_email_csi VARCHAR(255) NULL,
  dc_telefone_csi VARCHAR(30) NULL,
  dc_public_id_csi VARCHAR(100) NULL,
  dc_link_csi VARCHAR(255) NULL,
  dt_visualizado_csi TIMESTAMP NULL,
  dt_assinado_csi TIMESTAMP NULL,
  dt_recusado_csi TIMESTAMP NULL,
  dt_falha_entrega_csi TIMESTAMP NULL,
  dc_motivo_csi TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
CREATE INDEX IF NOT EXISTS contrato_signatario_csi_cas_index ON contrato_signatario_csi (cd_contrato_assinatura_cas);
CREATE INDEX IF NOT EXISTS contrato_signatario_csi_public_id_index ON contrato_signatario_csi (dc_public_id_csi);

CREATE TABLE IF NOT EXISTS autentique_evento_aev (
  cd_autentique_evento_aev BIGSERIAL PRIMARY KEY,
  dc_evento_id_aev VARCHAR(100) NOT NULL UNIQUE,
  dc_tipo_aev VARCHAR(60) NOT NULL,
  cd_documento_autentique_aev VARCHAR(100) NULL,
  js_payload_aev TEXT NOT NULL,
  dt_processado_aev TIMESTAMP NULL,
  dc_erro_aev TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
CREATE INDEX IF NOT EXISTS autentique_evento_aev_tipo_index ON autentique_evento_aev (dc_tipo_aev);
CREATE INDEX IF NOT EXISTS autentique_evento_aev_documento_index ON autentique_evento_aev (cd_documento_autentique_aev);
