<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProcessoCorrespondenteFinalizarNotification extends Notification
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
            ->subject(Lang::getFromJson('Processo Finalizado - Número '.$this->processo->nu_processo_pro))
            ->markdown('email.finalizar_correspondente')
            ->line(Lang::getFromJson('O correspondente finalizou o processo '.$this->processo->nu_processo_pro.' conforme solicitado.'))
            ->line(Lang::getFromJson('Clique no botão abaixo para visualizar o processo:'))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
            ->line(Lang::getFromJson('Acesse o sistema para verificar os processos.'));
    }

}