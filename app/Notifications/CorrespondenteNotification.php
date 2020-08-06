<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CorrespondenteNotification extends Notification
{
    use Queueable;

    public $correspondente;

    public function __construct($correspondente)
    {
        $this->correspondente = $correspondente;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
       
       return (new MailMessage)
            ->subject(Lang::getFromJson('Confirmação de Cadastro'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Agradecemos seu cadastro em nosso sistema. Utilize o endereço abaixo para acessar:'))
            ->action(Lang::getFromJson('Acesse Aqui'), url(route('autenticacao')))
            ->line(Lang::getFromJson('Após acessar o seu cadastro, terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
            ->line(Lang::getFromJson('Aguardamos você para darmos início a parceria.'));
    }

}