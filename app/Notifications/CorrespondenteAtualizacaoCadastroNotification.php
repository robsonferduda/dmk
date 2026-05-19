<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CorrespondenteAtualizacaoCadastroNotification extends Notification
{
    use Queueable;

    public $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Atualização Cadastral Obrigatória – Sistema DMK')
            ->greeting('Prezados(as), boa tarde!')
            ->line('Para continuidade da utilização do Sistema DMK, formalização dos contratos de parceria e manutenção do cadastro ativo para distribuição de audiências e diligências, solicitamos a **atualização obrigatória** dos seus dados cadastrais diretamente no Sistema DMK.')
            ->line('Pedimos especial atenção ao preenchimento correto e completo das seguintes informações:')
            ->line('• Nome completo')
            ->line('• RG')
            ->line('• CPF')
            ->line('• Número da OAB')
            ->line('• Endereço profissional completo (CEP incluído)')
            ->line('• E-mail atualizado')
            ->line('• Contato de WhatsApp atualizado')
            ->action('Clique aqui para atualizar seu cadastro', $this->link)
            ->line('⚠️ **IMPORTANTE:** O número de WhatsApp informado será utilizado pelo Sistema DMK para envio automático de confirmações de audiência, avisos operacionais, lembretes obrigatórios, solicitações de confirmação, realização de check-in no fórum/audiência e comunicações urgentes relacionadas aos atos contratados.')
            ->line('A atualização cadastral é **obrigatória** para: assinatura do contrato de correspondente; manutenção do cadastro ativo no sistema; recebimento de novas demandas; utilização das funcionalidades automáticas do Sistema DMK.')
            ->line('Solicitamos que a atualização seja realizada com máxima urgência, garantindo o correto funcionamento operacional e a regularização contratual da parceria.')
            ->line('Em caso de dúvidas, permanecemos à disposição.')
            ->salutation('Atenciosamente, DMK Advogados');
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
