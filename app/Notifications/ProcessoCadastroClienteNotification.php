<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProcessoCadastroClienteNotification extends Notification
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
        $cliente = ($this->processo->cliente) ? $this->processo->cliente->nm_razao_social_cli : 'Cliente';

        return (new MailMessage)
            ->subject(Lang::getFromJson('Novo processo cadastrado pelo cliente - '.$this->processo->getAssuntoNotification()))
            ->markdown('email.email')
            ->line(Lang::getFromJson('O cliente '.$cliente.' cadastrou um novo processo no sistema.'))
            ->line(Lang::getFromJson('Número do processo: '.$this->processo->nu_processo_pro))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
            ->line(Lang::getFromJson('Acesse o sistema para analisar e dar continuidade ao atendimento.'));
    }
}
