<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MensagemProcessoNotification extends Notification
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
                ->subject(Lang::getFromJson('Você possui uma nova mensagem no processo '.$this->processo->nu_processo_pro))
                ->markdown('email.convite')
                ->line(Lang::getFromJson('Você recebeu uma nova mensagem no processo '.$this->processo->nu_processo_pro.'. Clique no botão abaixo para acessar o processo e ler a mensagem:'))
                ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
                ->line(Lang::getFromJson('Aguardamos sua resposta'));


    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
