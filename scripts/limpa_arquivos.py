#!/usr/bin/env python3
import os
import time
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
from dotenv import load_dotenv

# ================= CONFIGURAÇÕES =================

# Calcula o caminho relativo partindo da localização do script
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
PROJETO_DIR = os.path.dirname(SCRIPT_DIR)  # sobe para a raiz do projeto
BASE_DIR = os.path.join(PROJETO_DIR, "storage", "arquivos", "64", "processos")

# Carrega variáveis do .env
load_dotenv(os.path.join(PROJETO_DIR, '.env'))

DIAS = 90

# Configurações de email do .env
EMAIL_REMETENTE = os.getenv('MAIL_FROM_ADDRESS', 'suporte@lawyerexpress.com.br')
EMAIL_SENHA = os.getenv('MAIL_PASSWORD')
SMTP_SERVER = os.getenv('MAIL_HOST', 'mail.lawyerexpress.com.br')
SMTP_PORT = int(os.getenv('MAIL_PORT', 465))
MAIL_ENCRYPTION = os.getenv('MAIL_ENCRYPTION', 'ssl')

DESTINATARIOS = [
    "robsonferduda@gmail.com",
    "dmk@dmkadvogados.com.br"
]

# =================================================


def tamanho_diretorio(path):
    total = 0
    for root, _, files in os.walk(path):
        for f in files:
            fp = os.path.join(root, f)
            if os.path.exists(fp):
                total += os.path.getsize(fp)
    return total


def bytes_para_humano(bytes):
    for unidade in ['B', 'KB', 'MB', 'GB', 'TB']:
        if bytes < 1024:
            return f"{bytes:.2f} {unidade}"
        bytes /= 1024
    return f"{bytes:.2f} PB"


def limpar_arquivos_antigos():
    agora = time.time()
    limite = agora - (DIAS * 86400)

    arquivos_removidos = 0
    bytes_removidos = 0

    for pasta in os.listdir(BASE_DIR):
        caminho_pasta = os.path.join(BASE_DIR, pasta)

        # garante que só entra nas pastas numéricas
        if not pasta.isdigit():
            continue

        if not os.path.isdir(caminho_pasta):
            continue

        for root, _, files in os.walk(caminho_pasta):
            for arquivo in files:
                caminho_arquivo = os.path.join(root, arquivo)
                try:
                    if os.path.getmtime(caminho_arquivo) < limite:
                        tamanho = os.path.getsize(caminho_arquivo)
                        os.remove(caminho_arquivo)
                        arquivos_removidos += 1
                        bytes_removidos += tamanho
                except Exception as e:
                    print(f"Erro ao remover {caminho_arquivo}: {e}")

    return arquivos_removidos, bytes_removidos


def enviar_email(relatorio):
    msg = MIMEMultipart()
    msg["From"] = EMAIL_REMETENTE
    msg["To"] = ", ".join(DESTINATARIOS)
    msg["Subject"] = "Relatório diário de limpeza - Processos"

    msg.attach(MIMEText(relatorio, "plain"))

    # Usa SSL ou TLS baseado na configuração do .env
    if MAIL_ENCRYPTION == 'ssl':
        with smtplib.SMTP_SSL(SMTP_SERVER, SMTP_PORT) as server:
            server.login(EMAIL_REMETENTE, EMAIL_SENHA)
            server.send_message(msg)
    else:
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()
            server.login(EMAIL_REMETENTE, EMAIL_SENHA)
            server.send_message(msg)


def main():
    data_execucao = datetime.now().strftime("%d/%m/%Y %H:%M:%S")

    tamanho_antes = tamanho_diretorio(BASE_DIR)

    arquivos_removidos, bytes_removidos = limpar_arquivos_antigos()

    tamanho_depois = tamanho_diretorio(BASE_DIR)

    relatorio = f"""
Relatório de Limpeza Automática - Processos
Data/Hora: {data_execucao}

Diretório analisado: {BASE_DIR}
Critério: arquivos com mais de {DIAS} dias

Espaço antes da limpeza: {bytes_para_humano(tamanho_antes)}
Espaço depois da limpeza: {bytes_para_humano(tamanho_depois)}
Espaço liberado: {bytes_para_humano(bytes_removidos)}

Total de arquivos removidos: {arquivos_removidos}
"""

    enviar_email(relatorio)


if __name__ == "__main__":
    main()