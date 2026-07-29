-- Lançamentos parciais de pagamento de correspondente (por processo/item)
-- Ambiente: PostgreSQL

CREATE TABLE IF NOT EXISTS pagamento_correspondente_baixa_pcb (
  cd_pagamento_correspondente_baixa_pcb BIGSERIAL PRIMARY KEY,
  cd_pagamento_correspondente_pag BIGINT NOT NULL,
  cd_pagamento_correspondente_item_pai BIGINT NULL,
  cd_tipo_baixa_pcb SMALLINT NOT NULL, -- 1=Honorário 2=Despesa
  vl_baixa_pcb NUMERIC(12,2) NOT NULL,
  dt_baixa_pcb DATE NOT NULL,
  ds_observacao_pcb TEXT NULL,
  dc_comprovante_pcb VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT pcb_pag_fk FOREIGN KEY (cd_pagamento_correspondente_pag)
    REFERENCES pagamento_correspondente_pag (cd_pagamento_correspondente_pag)
    ON DELETE CASCADE,
  CONSTRAINT pcb_item_fk FOREIGN KEY (cd_pagamento_correspondente_item_pai)
    REFERENCES pagamento_correspondente_item_pai (cd_pagamento_correspondente_item_pai)
    ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS pcb_pag_idx
  ON pagamento_correspondente_baixa_pcb (cd_pagamento_correspondente_pag);

CREATE INDEX IF NOT EXISTS pcb_item_idx
  ON pagamento_correspondente_baixa_pcb (cd_pagamento_correspondente_item_pai);

-- Ambiente já existente: adicionar vínculo ao item/processo
ALTER TABLE pagamento_correspondente_baixa_pcb
  ADD COLUMN IF NOT EXISTS cd_pagamento_correspondente_item_pai BIGINT NULL;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint WHERE conname = 'pcb_item_fk'
  ) THEN
    ALTER TABLE pagamento_correspondente_baixa_pcb
      ADD CONSTRAINT pcb_item_fk
      FOREIGN KEY (cd_pagamento_correspondente_item_pai)
      REFERENCES pagamento_correspondente_item_pai (cd_pagamento_correspondente_item_pai)
      ON DELETE CASCADE;
  END IF;
END $$;

CREATE INDEX IF NOT EXISTS pcb_item_idx
  ON pagamento_correspondente_baixa_pcb (cd_pagamento_correspondente_item_pai);
