<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FiliacaoNotification extends Notification
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
            ->subject(Lang::getFromJson('Convite Sistema'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Você recebeu um convite de '.$notifiable->nm_razao_social_con.' para fazer parte da sua rede de correspondentes. Clique no botão abaixo para aceitar o convite:'))
            ->action(Lang::getFromJson('Aceitar convite'), url(config('app.url').route('correspondente.filiacao', ['token' => $this->convite->token_coc], false)))
            ->line(Lang::getFromJson('Após aceitar o convite, você terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
            ->line(Lang::getFromJson('Aguardamos sua resposta para darmos início a parceria.'));
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
