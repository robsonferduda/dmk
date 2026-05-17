-- [CHECK-IN PÚBLICO]
-- Permite check-in sem usuário logado (via link com token enviado por WhatsApp).
-- O correspondente é identificado pelo processo (cd_correspondente_cor),
-- não pelo usuário autenticado.
ALTER TABLE public.processo_checkin_pck
    ALTER COLUMN cd_user_checkin_pck DROP NOT NULL;
