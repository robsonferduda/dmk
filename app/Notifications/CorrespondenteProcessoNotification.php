<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CorrespondenteProcessoNotification extends Notification
{
    use Queueable;

    public $processo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($processo)
    {
        $this->processo = $processo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $token = \Crypt::encrypt($this->processo->cd_processo_pro);  

        return (new MailMessage)
            ->subject(Lang::getFromJson('Solicitação de Diligência'))
            ->markdown('email.convite')
            ->line(Lang::getFromJson('Olá nome_do_correspondente. '.$notifiable->nm_razao_social_con.' acaba de receber uma nova solicitação.'))
            ->line(Lang::getFromJson('Após realizar o seu cadastro, terá ao seu alcance o acesso a uma plataforma completa para o gerenciamento de diligências e audiências solicitadas ao vosso escritório.'))
            ->line(Lang::getFromJson('Aguardamos seu cadastro para darmos início a parceria.'));
            ->action(Lang::getFromJson('Responder Solicitação'), url(config('app.url').route('correspondente.processo', ['token' => $token], false)))
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

/*
Solicitação: 0000 (numero gerado pelo sistema)

Data Prazo Fatal: 22/05/2019 16:15:00
Número Processo: 0300249-43.2018.8.24.0052

Parte Autora: SEGURADORA LIDER DOS CONSORCIOS DO SEGURO DPVAT S/A
Parte Ré: ELZA DA SILVA
Solicitante: DMK ADVOGADOS ASSOCIADOS 
Tipo: Audiência Advogado e Preposto
Vara: 2 VARA CIVEL

Cidade: Porto União

UF: Santa Catarina
Código Cliente: 0000000
Descrição da Solicitação:  Solicitamos o seu comparecimento etc....
*/
