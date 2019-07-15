<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VinculoCorrespondenteNotification extends Notification
{
    use Queueable;

    public $conta;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($conta)
    {
        $this->conta = $conta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        
            return (new MailMessage)
                ->subject(Lang::getFromJson('Novo Correspondente'))
                ->markdown('email.convite')
                ->line(Lang::getFromJson('Você foi adicionado como corresponde por '.$this->conta->nm_razao_social_con.'. Clique no botão abaixo para acessar:'))
                ->action(Lang::getFromJson('Acesse Aqui'), url(route('autenticacao')))
                ->line(Lang::getFromJson('Após acessar, terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
                ->line(Lang::getFromJson('Aguardamos seu cadastro para darmos início a parceria.'));


    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
