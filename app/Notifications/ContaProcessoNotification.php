<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContaProcessoNotification extends Notification
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

        $decisao = ($notifiable->parecer == 'S') ? 'ACEITOU' : 'RECUSOU' ;
        $resposta = ($notifiable->parecer == 'S') ? 'Aceito' : 'Recusado' ;

        return (new MailMessage)
            ->subject(Lang::getFromJson('Processo '.$resposta.' pelo Correspondente - '.$this->processo->getAssuntoNotification()))
            ->markdown('email.aceite_processo')
            ->line(Lang::getFromJson('A sua solicitação referente ao processo '.$this->processo->nu_processo_pro.' foi respondida por '.$notifiable->correspondente.' e ele '.$decisao.' sua proposta. Clique no botão abaixo para mais informações:'))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processos.detalhes', ['token' => $notifiable->token], false)))
            ->line(Lang::getFromJson('Você já pode dar andamento ao processo. Bom trabalho!'));
    }

}