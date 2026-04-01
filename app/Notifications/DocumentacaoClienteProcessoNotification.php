<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentacaoClienteProcessoNotification extends Notification
{
    use Queueable;

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
        return (new MailMessage)
            ->subject(Lang::getFromJson('Documentação enviada pelo cliente - Processo ' . $this->processo->nu_processo_pro))
            ->line(Lang::getFromJson('O cliente marcou a documentação como enviada para o processo ' . $this->processo->nu_processo_pro . '.'))
            ->action(Lang::getFromJson('Ver Processo'), url('processos/acompanhamento/' . safe_encrypt($this->processo->cd_processo_pro)))
            ->line(Lang::getFromJson('Acesse o sistema para verificar os arquivos anexados.'));
    }
}
