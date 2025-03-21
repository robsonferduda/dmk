<?php

namespace App\Notifications;

use App\Conta;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProcessoRequisitarDadosNotification extends Notification
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
        $escritorio = Conta::where('cd_conta_con', $notifiable->cd_conta_con)->first()->nm_razao_social_con;

        return (new MailMessage)
            ->subject(Lang::getFromJson('Requisição de Dados - Processo '.$this->processo->nu_processo_pro))
            ->markdown('email.requisitar_dados')
            ->line(Lang::getFromJson($escritorio.' encaminhou um pedido de requisição de dados. Entre no processo e preencha os dados completos dos responsáveis pela realização do ato de acordo com o padrão do sistema.'))
            ->line(Lang::getFromJson('Clique no botão abaixo para realizar a operação:'))
            ->action(Lang::getFromJson('Ver Processo'), url(config('app.url').route('processo.acompanhar', ['processo' => \Crypt::encrypt($notifiable->cd_processo_pro)], false)))
            ->line(Lang::getFromJson('Você já pode dar andamento ao processo. Bom trabalho!'));
    }

}