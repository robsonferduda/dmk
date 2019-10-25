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
    public $options;

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
        $cod_cli = (!empty($this->processo->cliente->cod_externo_cli)) ? $this->processo->cliente->cod_externo_cli.' - '.$this->processo->cliente->nm_razao_social_cli : $this->processo->cliente->nm_razao_social_cli;

        $this->options = array('url_yes' => url(config('app.url').route('resposta', ['resposta' => 'S','token' => $token], false)), 
                               'url_not' => url(config('app.url').route('resposta', ['resposta' => 'N','token' => $token], false)),
                               'text_yes' => "Aceitar Diligência",
                               'text_not' => "Recusar Diligência"
                        );

        //Tratamento de atributos nulos
        $advogado = ($this->processo->advogadoSolicitante) ? $this->processo->advogadoSolicitante->nm_contato_cot : 'Não informado';
        $data = ($this->processo->dt_prazo_fatal_pro) ? date('d/m/Y', strtotime($this->processo->dt_prazo_fatal_pro)) : 'Não informado';
        $hora = ($this->processo->hr_audiencia_pro) ? date('H:i', strtotime($this->processo->hr_audiencia_pro)) : 'Não informado';
        $obs = ($this->processo->$dc_observacao_pro) ? $this->processo->dc_observacao_pro : 'Nanhuma infomração adicional';

        return (new MailMessage)
            ->subject(Lang::getFromJson('Solicitação de Diligência - Processo '.$this->processo->nu_processo_pro))
            ->markdown('email.resposta',$this->options)
            ->line(Lang::getFromJson('Olá '.$notifiable->correspondente.','))
           

            ->line(Lang::getFromJson('Para responder, selecione uma das opções abaixo:'))
            ->action(Lang::getFromJson('Aceitar Diligência'),null)
            ->action(Lang::getFromJson('Recusar Diligência'),null)
            ->line(Lang::getFromJson('Aguardamos a sua resposta.'));
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
