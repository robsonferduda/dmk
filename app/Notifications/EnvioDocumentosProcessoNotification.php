<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EnvioDocumentosProcessoNotification extends Notification
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
            ->subject(Lang::getFromJson('Envio de Documentos no Processo '.$this->processo->nu_processo_pro))
            ->markdown('email.documentos')
            ->line(Lang::getFromJson('Envio de documentos do processo '.$this->processo->nu_processo_pro.' finalizado.'))
            ->line(Lang::getFromJson('Confirma o recebimento dos documentos e a realização do ato contratado?'))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
            ->line(Lang::getFromJson('Acesse o sistema para verificar os processos.'));
    }

}