<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Traits\VerifyNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CorrespondenteCadastroContaNotification extends Notification
{
    use Queueable, VerifyNotification;

    public $conta;

    public function __construct($conta)
    {
        $this->conta = $conta;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $correspondente = \Crypt::encrypt($notifiable->cd_conta_con);

        return (new MailMessage)
            ->subject(Lang::getFromJson('Cadastro de Correspondente - '.$this->conta->nm_razao_social_con))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('O escritório '.$this->conta->nm_razao_social_con.' adicionou você como correspondente no Sistema Easyjuris. Utilize o endereço abaixo para acessar o sistema, confirmar seu cadastro e criar a senha de acesso:'))
            ->action(Lang::getFromJson('Acesse Aqui'), url(route('cadastrar.senha', ['nivel_url' => $correspondente], false)))
            ->line(Lang::getFromJson('Após confirmar seu cadastro, você terá ao seu alcance uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
            ->line(Lang::getFromJson('Aguardamos você para darmos início a parceria.'));
    }

    public function toArray($notifiable)
    {
        return [
            
        ];
    }
}