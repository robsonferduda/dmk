"""
Vídeo de demonstração da tela v2/correspondentes.

Credenciais em ~/.dmk-demo (fora do projeto), uma por linha:
    DEMO_EMAIL=...
    DEMO_SENHA=...
    DEMO_NIVEL=1            # 1 Escritório, 2 Colaborador, 3 Correspondente, 4 Cliente
    DEMO_URL=https://...    # opcional, padrão: produção

Uso:
    PLAYWRIGHT_BROWSERS_PATH=$HOME/.cache/ms-playwright python3 tools/demo-video/correspondentes.py

Nomes, e-mails, documentos e fotos são trocados por dados fictícios antes de a página aparecer no vídeo.
"""
import os
import shutil
import subprocess
import sys
import time
from datetime import datetime
from pathlib import Path

from playwright.sync_api import sync_playwright

LARGURA, ALTURA = 1440, 900
SAIDA = Path.home() / 'dmk-videos'
AQUI = Path(__file__).resolve().parent


def ler_credenciais():
    arquivo = Path.home() / '.dmk-demo'
    if not arquivo.exists():
        sys.exit(f'Crie {arquivo} com DEMO_EMAIL e DEMO_SENHA.')
    valores = {}
    for linha in arquivo.read_text().splitlines():
        if '=' in linha and not linha.strip().startswith('#'):
            chave, valor = linha.split('=', 1)
            valores[chave.strip()] = valor.strip().strip('"\'')
    for obrigatoria in ('DEMO_EMAIL', 'DEMO_SENHA'):
        if not valores.get(obrigatoria):
            sys.exit(f'{obrigatoria} ausente em {arquivo}.')
    valores.setdefault('DEMO_NIVEL', '1')
    valores['DEMO_URL'] = valores.get('DEMO_URL', 'https://sistema.lawyerexpress.com.br').rstrip('/')
    return valores


def entrar(browser, cred):
    contexto = browser.new_context(viewport={'width': LARGURA, 'height': ALTURA})
    pagina = contexto.new_page()
    pagina.goto(cred['DEMO_URL'] + '/login', wait_until='networkidle')
    pagina.select_option('select[name=nivel]', cred['DEMO_NIVEL'])
    pagina.fill('input[name=email]', cred['DEMO_EMAIL'])
    pagina.fill('input[name=password]', cred['DEMO_SENHA'])
    pagina.click('form button[type=submit]')
    pagina.wait_for_load_state('networkidle')
    if '/login' in pagina.url:
        sys.exit('Login recusado: confira as credenciais em ~/.dmk-demo.')
    estado = contexto.storage_state()
    contexto.close()
    return estado


class Cena:
    def __init__(self, pagina):
        self.p = pagina
        self.inicio = time.monotonic()
        self.corte = 0.0

    def pausa(self, segundos):
        self.p.wait_for_timeout(int(segundos * 1000))

    def legenda(self, texto, segundos=0):
        self.p.evaluate('t => window.demoLegenda(t)', texto)
        self.pausa(segundos)

    def cartao(self, titulo, subtitulo, segundos=3, abertura=False):
        """Com abertura=True, o vídeo final começa com o cartão já na tela (corta o carregamento inicial)."""
        self.p.evaluate('([t, s]) => window.demoCartao(t, s)', [titulo, subtitulo])
        self.pausa(0.6)
        if abertura:
            self.corte = time.monotonic() - self.inicio
        self.pausa(segundos)
        self.p.evaluate('() => window.demoCartao(null)')
        self.pausa(0.6)

    def mover(self, alvo, passos=25):
        caixa = alvo.bounding_box()
        self.p.mouse.move(caixa['x'] + caixa['width'] / 2, caixa['y'] + caixa['height'] / 2, steps=passos)
        self.pausa(0.3)

    def clicar(self, alvo, navega=False):
        self.mover(alvo)
        if navega:
            with self.p.expect_navigation(wait_until='networkidle'):
                alvo.click()
        else:
            alvo.click()

    def escolher(self, select_id, texto):
        """Choices.js: abre a lista, digita e confirma a primeira opção."""
        caixa = self.p.locator(f'#{select_id}').locator('xpath=ancestor::div[contains(@class,"choices")][1]')
        self.clicar(caixa)
        self.p.wait_for_selector(f'div.choices.is-open #{select_id}', state='attached')
        self.pausa(0.4)
        self.p.keyboard.type(texto, delay=110)
        self.pausa(0.8)
        self.p.keyboard.press('Enter')
        self.pausa(0.6)

    def rolar(self, pixels, passos=12):
        for _ in range(passos):
            self.p.mouse.wheel(0, pixels / passos)
            self.pausa(0.05)


def roteiro(c, url):
    p = c.p
    p.goto(url + '/v2/correspondentes', wait_until='networkidle')
    p.mouse.move(LARGURA / 2, ALTURA / 2)
    c.cartao('Correspondentes', 'Visão geral da tela · DMK v2', 3, abertura=True)

    c.legenda('Lista dos correspondentes vinculados ao escritório, com atuação, categoria, comarca de origem e documentos.', 3.5)
    c.rolar(420)
    c.pausa(1)
    c.rolar(-420)
    c.pausa(0.5)

    c.legenda('As abas separam advogados e prepostos, com o total de cada grupo.', 1.5)
    c.clicar(p.locator('.users-filter-tab', has_text='Advogados'), navega=True)
    c.legenda('Somente advogados.', 2)
    c.clicar(p.locator('.users-filter-tab', has_text='Prepostos'), navega=True)
    c.legenda('Somente prepostos — para eles a OAB é opcional.', 2.5)
    c.clicar(p.locator('.users-filter-tab', has_text='Todos'), navega=True)

    c.legenda('Filtre por estado e cidade de atuação: a busca considera todas as comarcas atendidas, não só a de origem.', 1.5)
    c.escolher('filtro-estado', 'São Paulo')
    p.wait_for_function('document.querySelectorAll("#filtro-cidade option").length > 1')
    c.pausa(0.8)
    c.escolher('filtro-cidade', 'Campinas')
    c.clicar(p.locator('.dmk-filters button[type=submit]'), navega=True)
    c.legenda('Correspondentes que atendem Campinas/SP.', 3)

    c.legenda('Para voltar à lista completa, use "Limpar filtros".', 1.2)
    c.clicar(p.locator('.dmk-filters a', has_text='Limpar filtros'), navega=True)

    c.legenda('A busca rápida encontra pelo nome: digite e tecle Enter.', 0.8)
    busca = p.locator('.users-search input[name=nome]')
    c.clicar(busca)
    busca.type('Silva', delay=140)
    c.pausa(0.5)
    with p.expect_navigation(wait_until='networkidle'):
        p.keyboard.press('Enter')
    c.legenda('Resultado da busca por "Silva".', 2.5)

    c.legenda('Em cada linha: detalhes, edição e atalhos para processos, comarcas, despesas e honorários.', 1)
    c.mover(p.locator('.users-action-btn[title=Detalhes]').first)
    c.pausa(0.6)
    c.mover(p.locator('.users-action-btn[title=Editar]').first)
    c.pausa(0.6)
    c.clicar(p.locator('.users-action-btn.dropdown-toggle').first)
    c.pausa(3)
    p.keyboard.press('Escape')
    c.pausa(0.5)

    c.legenda(None)
    c.cartao('DMK · Correspondentes', 'Simulação de uso com dados fictícios', 3)


def main():
    cred = ler_credenciais()
    SAIDA.mkdir(exist_ok=True)
    temporario = SAIDA / 'tmp'
    shutil.rmtree(temporario, ignore_errors=True)

    with sync_playwright() as pw:
        browser = pw.chromium.launch()
        estado = entrar(browser, cred)

        contexto = browser.new_context(
            viewport={'width': LARGURA, 'height': ALTURA},
            storage_state=estado,
            record_video_dir=str(temporario),
            record_video_size={'width': LARGURA, 'height': ALTURA},
            locale='pt-BR',
        )
        contexto.add_init_script(path=str(AQUI / 'demo.js'))
        pagina = contexto.new_page()
        cena = Cena(pagina)
        try:
            roteiro(cena, cred['DEMO_URL'])
        except Exception:
            pagina.screenshot(path=str(SAIDA / 'erro.png'))
            raise
        finally:
            video = pagina.video.path()
            contexto.close()
            browser.close()

    destino = SAIDA / f'correspondentes-{datetime.now():%Y%m%d-%H%M}.mp4'
    subprocess.run(['ffmpeg', '-y', '-loglevel', 'error', '-ss', f'{cena.corte:.2f}', '-i', str(video),
                    '-c:v', 'libx264', '-pix_fmt', 'yuv420p', '-crf', '22', '-movflags', '+faststart',
                    str(destino)], check=True)
    shutil.rmtree(temporario, ignore_errors=True)
    print(destino)


if __name__ == '__main__':
    main()
