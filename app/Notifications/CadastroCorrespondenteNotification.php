<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Traits\VerifyNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CadastroCorrespondenteNotification extends Notification
{
    use Queueable, VerifyNotification;

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
            ->subject(Lang::getFromJson('Cadastro Correspondente'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson($this->conta->nm_razao_social_con.' adicionou você como correspondente no Sistema DMK. Utilize o endereço abaixo para acessar o sistema:'))
            ->action(Lang::getFromJson('Acesse Aqui'), url(route('/')))
            ->line(Lang::getFromJson('Após acessar o seu cadastro, terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
            ->line(Lang::getFromJson('Aguardamos você para darmos início a parceria.'));
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
