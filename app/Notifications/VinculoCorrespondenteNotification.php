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
        
        return (new MailMessage)
            ->subject(Lang::getFromJson('Bem-vindo Correspondente'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Você foi adicionado como corresponde pelo escritório '.$this->conta->nm_razao_social_con.'. Seus dados de acesso são:'))
            ->line(Lang::getFromJson('Usuário: '.$notifiable->email))
            ->line(Lang::getFromJson('Senha: '.$notifiable->senha))
            ->line(Lang::getFromJson('Clique no botão abaixo para acessar:'))
            ->action(Lang::getFromJson('Acesse Aqui'), url(route('autenticacao.correspondente')))
            ->line(Lang::getFromJson('Após seu primeiro acesso recomendamos que altere a senha que foi gerada automaticamente pelo sistema, para sua segurança. Também confira seus dados e altere as informações necessárias para manter seus dados atualizados.'));

    }

    public function toArray($notifiable)
    {
        return [
            
        ];
    }
}
