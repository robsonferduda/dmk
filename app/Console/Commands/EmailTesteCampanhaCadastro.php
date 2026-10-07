<?php

namespace App\Console\Commands;

use App\Mail\AtualizacaoCadastroMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EmailTesteCampanhaCadastro extends Command
{
    protected $signature = 'cadastro:email-teste
        {email : destinatário do teste}
        {--nome=MARIA DA SILVA SOUZA : nome do correspondente fictício}
        {--prazo=7 : dias até o prazo}
        {--html= : em vez de enviar, grava o HTML neste arquivo}';

    protected $description = 'Envia (ou grava em arquivo) um exemplo do e-mail de atualização cadastral';

    public function handle()
    {
        $email = $this->argument('email');
        $mail = new AtualizacaoCadastroMail(
            AtualizacaoCadastroMail::nomeFormatado($this->option('nome')),
            $email,
            Carbon::today()->addDays((int) $this->option('prazo')),
            config('cadastro.url_publica') . '/correspondente/login'
        );

        if ($this->option('html')) {
            file_put_contents($this->option('html'), $mail->render());
            $this->info('HTML gravado em ' . $this->option('html'));
            return 0;
        }

        $mail->subject('[TESTE] Atualização cadastral – DMK Advogados');
        Mail::to($email)->send($mail);
        $this->info("E-mail de teste enviado para {$email} (remetente " . config('mail.from.address') . ', resposta para ' . config('cadastro.responder_para.email') . ').');

        return 0;
    }
}
