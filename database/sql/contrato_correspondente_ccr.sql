-- Campos de contrato do correspondente (vínculo conta x correspondente)
-- Execute se a migration não puder rodar via artisan.

ALTER TABLE conta_correspondente_ccr
  ADD COLUMN IF NOT EXISTS fl_contrato_gerado_ccr BOOLEAN NOT NULL DEFAULT FALSE,
  ADD COLUMN IF NOT EXISTS fl_contrato_assinado_ccr BOOLEAN NOT NULL DEFAULT FALSE,
  ADD COLUMN IF NOT EXISTS dt_contrato_gerado_ccr TIMESTAMP NULL,
  ADD COLUMN IF NOT EXISTS dc_caminho_contrato_ccr VARCHAR(255) NULL;
