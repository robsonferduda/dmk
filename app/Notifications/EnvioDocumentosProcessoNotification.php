<?php

namespace App\Notifications;

use App\AnexoProcesso;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EnvioDocumentosProcessoNotification extends Notification
{
    use Queueable;

    /**
     * Tamanho máximo (em bytes) do zip de anexos para envio por e-mail.
     * Acima disso o anexo NÃO é incluído (a maioria dos servidores SMTP
     * limita a mensagem em ~10-25 MB). Mantemos uma margem de segurança.
     */
    const MAX_ATTACHMENT_BYTES = 8 * 1024 * 1024; // 8 MB

    public $processo;

    public function __construct($processo)
    {
        $this->processo = $processo;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $zipPath          = $this->gerarZipAnexos();
        $zipExiste        = !is_null($zipPath) && file_exists($zipPath);
        $tamanhoZip       = $zipExiste ? filesize($zipPath) : 0;
        $anexoMuitoGrande = $zipExiste && $tamanhoZip > self::MAX_ATTACHMENT_BYTES;
        $temAnexos        = $zipExiste && !$anexoMuitoGrande;

        if ($anexoMuitoGrande) {
            Log::warning('Zip de anexos excede limite para envio por e-mail; anexo será omitido.', [
                'processo'   => $this->processo->cd_processo_pro,
                'tamanho'    => $tamanhoZip,
                'limite'     => self::MAX_ATTACHMENT_BYTES,
            ]);
        }

        $urlProcesso = url(config('app.url').route(
            'processo.correspondente',
            ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)],
            false
        ));

        $mail = (new MailMessage)
            ->subject(Lang::getFromJson('Orientações e documentos disponíveis - '.$this->processo->getAssuntoNotification()))
            ->markdown('email.envio-documentos', [
                'processo'         => $this->processo,
                'temAnexos'        => $temAnexos,
                'anexoMuitoGrande' => $anexoMuitoGrande,
                'urlProcesso'      => $urlProcesso,
            ]);

        if ($temAnexos) {
            $mail->attach($zipPath, [
                'as'   => 'documentos_processo_'.$this->processo->nu_processo_pro.'.zip',
                'mime' => 'application/zip',
            ]);
        }

        return $mail;
    }

    /**
     * Gera um arquivo ZIP contendo todos os anexos do processo.
     * Retorna o caminho absoluto do zip ou null caso não existam anexos válidos.
     */
    protected function gerarZipAnexos()
    {
        $anexos = AnexoProcesso::where('cd_processo_pro', $this->processo->cd_processo_pro)->get();

        if ($anexos->isEmpty()) {
            return null;
        }

        $conta       = $this->processo->cd_conta_con;
        $idProcesso  = $this->processo->cd_processo_pro;
        $idFile      = date('YmdHis');
        $destinoTemp = "arquivos/$conta/processos/$idProcesso/$idFile/";
        $destinoZip  = "arquivos/$conta/processos/$idProcesso/anexos/";

        if (!is_dir(storage_path($destinoTemp))) {
            @mkdir(storage_path($destinoTemp), 0775, true);
        }

        if (!is_dir(storage_path($destinoZip))) {
            @mkdir(storage_path($destinoZip), 0775, true);
        }

        $copiados = 0;

        foreach ($anexos as $anexo) {
            $origem = storage_path($anexo->nm_local_anexo_processo_apr.$anexo->nm_anexo_processo_apr);

            if (file_exists($origem)) {
                @copy($origem, storage_path($destinoTemp.$anexo->nm_anexo_processo_apr));
                $copiados++;
            }
        }

        if ($copiados === 0) {
            @rmdir(storage_path($destinoTemp));
            return null;
        }

        $zipPath = storage_path($destinoZip.$idFile.'_anexos.zip');

        try {
            \Zipper::make($zipPath)->add(glob(storage_path($destinoTemp).'*'))->close();
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar zip de anexos para envio ao correspondente', [
                'processo' => $this->processo->cd_processo_pro,
                'erro'     => $e->getMessage(),
            ]);
            $this->limparPasta(storage_path($destinoTemp));
            return null;
        }

        $this->limparPasta(storage_path($destinoTemp));

        return $zipPath;
    }

    protected function limparPasta($pasta)
    {
        if (!is_dir($pasta)) {
            return;
        }

        foreach (File::allFiles($pasta) as $file) {
            @unlink($file->getRealPath());
        }

        @rmdir($pasta);
    }
}