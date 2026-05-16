-- [CHECK-IN PÚBLICO]
-- Token usado para gerar link de check-in sem login (enviado por WhatsApp
-- ao correspondente). Gerado on-demand pelo backend; valor opaco
-- (random bytes em hex). Único quando preenchido.
ALTER TABLE public.processo_pro
    ADD COLUMN IF NOT EXISTS ds_checkin_token_pro VARCHAR(80);

CREATE UNIQUE INDEX IF NOT EXISTS uq_processo_pro_checkin_token
    ON public.processo_pro (ds_checkin_token_pro)
    WHERE ds_checkin_token_pro IS NOT NULL AND deleted_at IS NULL;
