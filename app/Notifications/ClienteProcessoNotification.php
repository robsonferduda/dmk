<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\File;
use App\Processo;

class ClienteProcessoNotification extends Notification
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
        $conta = Processo::where('cd_processo_pro', $notifiable->cd_processo_pro)->select('cd_conta_con')->first()['cd_conta_con'];

        $fl_anexo = false;
        //Verifica se existem anexos para ser enviados
        if (count($notifiable->anexos) > 0) {
            $fl_anexo = true;
            $id_file = date("YmdHis");
            $destino = "arquivos/$conta/processos/$notifiable->cd_processo_pro/$id_file/";
            $destino_zip = "arquivos/$conta/processos/$notifiable->cd_processo_pro/anexos/";

            if (!is_dir($destino)) {
                @mkdir(storage_path($destino), 0775);
            }

            for ($i=0; $i < count($notifiable->anexos); $i++) {

                $file = explode("/", $notifiable->anexos[$i]);
                copy(storage_path(rtrim($notifiable->anexos[$i],"/")), storage_path($destino.$file[4]));
            }

            //Gerar zip
            $zips = glob(storage_path($destino));
            \Zipper::make(storage_path($destino_zip.$id_file.'_anexos.zip'))->add($zips)->close();


            //Excluir diretorio temp
            foreach (File::allFiles(storage_path($destino)) as $file) {
                unlink($file->getRealPath());
            }

            rmdir(storage_path($destino));
            //Fim da exclusão
        }

        if ($fl_anexo) {
            return (new MailMessage)
                ->subject(Lang::getFromJson('Processo '.$this->processo->nu_processo_pro.' finalizado'))
                ->markdown('email.finalizacao')
                ->attach(storage_path($destino_zip.$id_file.'_anexos.zip'))
                ->line(Lang::getFromJson('O processo identificado pelo número '.$this->processo->nu_processo_pro.' foi finalizado por '.$notifiable->conta.'.'))
                ->line(Lang::getFromJson('Este email possui anexos que integram a mensagem, favor verificar.'))
                ->line(Lang::getFromJson('Em caso de dúvidas entre em contato com o responsável pelo processo'));
        } else {
            return (new MailMessage)
                ->subject(Lang::getFromJson('Processo '.$this->processo->nu_processo_pro.' finalizado'))
                ->markdown('email.finalizacao')
                ->line(Lang::getFromJson('O processo identificado pelo número '.$this->processo->nu_processo_pro.' foi finalizado por '.$notifiable->conta.'.'))
                ->line(Lang::getFromJson('Em caso de dúvidas entre em contato com o responsável pelo processo'));
        }
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
