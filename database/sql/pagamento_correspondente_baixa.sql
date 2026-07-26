-- Lançamentos parciais de pagamento de correspondente
-- Ambiente: PostgreSQL

CREATE TABLE IF NOT EXISTS pagamento_correspondente_baixa_pcb (
  cd_pagamento_correspondente_baixa_pcb BIGSERIAL PRIMARY KEY,
  cd_pagamento_correspondente_pag BIGINT NOT NULL,
  cd_tipo_baixa_pcb SMALLINT NOT NULL, -- 1=Honorário 2=Despesa
  vl_baixa_pcb NUMERIC(12,2) NOT NULL,
  dt_baixa_pcb DATE NOT NULL,
  ds_observacao_pcb TEXT NULL,
  dc_comprovante_pcb VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT pcb_pag_fk FOREIGN KEY (cd_pagamento_correspondente_pag)
    REFERENCES pagamento_correspondente_pag (cd_pagamento_correspondente_pag)
    ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS pcb_pag_idx
  ON pagamento_correspondente_baixa_pcb (cd_pagamento_correspondente_pag);
