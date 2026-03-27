<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class HonorarioAlteracaoNotification extends Notification
{
    use Queueable;

    public $alteracao;

    public function __construct($alteracao)
    {
        $this->alteracao = $alteracao;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $processo = $this->alteracao->processo;

        return (new MailMessage)
            ->subject(Lang::getFromJson('Pedido de Alteração de Honorário - Processo ' . $processo->nu_processo_pro))
            ->markdown('email.honorario_alteracao')
            ->line(Lang::getFromJson('Um cliente solicitou a alteração do valor do serviço no processo ' . $processo->nu_processo_pro . '.'))
            ->line(Lang::getFromJson('Valor atual: R$ ' . number_format($this->alteracao->nu_valor_antigo_tha, 2, ',', '.')))
            ->line(Lang::getFromJson('Valor proposto: R$ ' . number_format($this->alteracao->nu_valor_novo_tha, 2, ',', '.')))
            ->action(Lang::getFromJson('Ver Pedido'), url('processos/honorario-alteracao/' . $this->alteracao->cd_taxa_honorario_alteracao_tha))
            ->line(Lang::getFromJson('Acesse o sistema para aprovar ou reprovar o pedido.'));
    }
}
