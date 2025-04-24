<?php

namespace App\Notifications;

use App\ContaCorrespondente;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProcessoAtualizacaoDadosNotification extends Notification
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
        $correspondente = ContaCorrespondente::where('cd_correspondente_cor', $notifiable->cd_correspondente_cor)->first()->nm_conta_correspondente_ccr;

        return (new MailMessage)
            ->subject(Lang::getFromJson('Processo '.$this->processo->nu_processo_pro.' - Atualização de Dados (Advogado e/ou Preposto)'))
            ->markdown('email.atualizar_dados')
            ->line(Lang::getFromJson($correspondente.' atualizou os dados dos responsáveis pela realização do ato.'))
            ->line(Lang::getFromJson('Clique no botão abaixo para visualizar os dados:'))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['processo' => \Crypt::encrypt($notifiable->cd_processo_pro)], false)))
            ->line(Lang::getFromJson('Você já pode dar andamento ao processo. Bom trabalho!'));
    }

}