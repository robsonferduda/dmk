<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ConviteNotification extends Notification
{
    use Queueable;

    public $convite;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($convite)
    {
        $this->convite = $convite;
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
            ->subject(Lang::getFromJson('Cadastro Sistema'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Você recebeu um convite de '.$notifiable->nm_razao_social_con.' para realizar o cadastro como correspondente em nosso sistema. Clique no botão abaixo para se cadastrar:'))
            ->action(Lang::getFromJson('Cadastre-se Aqui'), url(config('app.url').route('correspondente.convite', ['token' => $this->convite->token_coc], false)))
            ->line(Lang::getFromJson('Após realizar o seu cadastro, terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
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
