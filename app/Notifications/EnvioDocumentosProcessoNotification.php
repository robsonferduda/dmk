<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EnvioDocumentosProcessoNotification extends Notification
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

        // Adiciona o botão "Ver Documentos" somente se o link estiver preenchido
        if (!empty($this->processo->ds_link_dados_pro)) {

            return (new MailMessage)
                ->subject(Lang::getFromJson('Orientações e documentos disponíveis - '.$this->processo->getAssuntoNotification()))
                ->markdown('email.documentos')
                ->line(Lang::getFromJson('As orientações e documentos do processo foram disponibilizados no sistema para seu aceite.'))
                ->action(Lang::getFromJson('Ver Documentos'), url($this->processo->ds_link_dados_pro))
                ->line(Lang::getFromJson('Utilize o botão abaixo para confirmar o recebimento dos documentos e a realização do ato contratado.'))
                ->action(Lang::getFromJson('Ver Documentos'), url($this->processo->ds_link_dados_pro))
                ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.correspondente', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
                ->line(Lang::getFromJson('Acesse o sistema para verificar os processos.'));

        }else{

            return (new MailMessage)
                ->subject(Lang::getFromJson('Orientações e documentos disponíveis - '.$this->processo->getAssuntoNotification()))
                ->markdown('email.documentos')
                ->line(Lang::getFromJson('As orientações e documentos do processo foram disponibilizados no sistema para seu aceite.'))
                ->line(Lang::getFromJson('Utilize o botão abaixo para confirmar o recebimento dos documentos e a realização do ato contratado.'))
                ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.correspondente', ['token' => \Crypt::encrypt($this->processo->cd_processo_pro)], false)))
                ->line(Lang::getFromJson('Acesse o sistema para verificar os processos.'));
        }
    }
}