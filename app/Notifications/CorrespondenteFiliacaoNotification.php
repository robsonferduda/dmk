<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CorrespondenteFiliacaoNotification extends Notification
{
    use Queueable;

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
        $nivel_url = \Crypt::encrypt(3);

        return (new MailMessage)
            ->subject(Lang::getFromJson('Cadastro de Correspondente - '.$this->conta->nm_razao_social_con))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Você foi cadastrado como correspondente do escritório '.$this->conta->nm_razao_social_con))
            ->line('Utilize seu usuario e senha para acessar o sistema e verificar seu cadastro.')
            ->line(Lang::getFromJson('Clique no botão abaixo para acessar:'))
            ->action(Lang::getFromJson('Acesse Aqui'), url(route('seleciona.perfil', ['nivel_url' => $nivel_url], false)))
            ->line(Lang::getFromJson('Se tiver dificuldade para lembrar seus dados de acesso, utilize a função "Esqueci minha senha" para cadastrar uma nova senha.'));

    }

    public function toArray($notifiable)
    {
        return [
            
        ];
    }
}