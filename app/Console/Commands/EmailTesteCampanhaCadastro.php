<?php

namespace App\Console\Commands;

use App\ContaCorrespondente;
use App\Mail\AtualizacaoCadastroMail;
use App\Services\Cadastro\FormularioCadastro;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EmailTesteCampanhaCadastro extends Command
{
    protected $signature = 'cadastro:email-teste
        {email : destinatário do teste}
        {--nome=MARIA DA SILVA SOUZA : nome do correspondente fictício}
        {--vinculo= : cd_conta_correspondente_ccr cujo formulário real o botão abre (o nome vem do cadastro)}
        {--prazo=7 : dias até o prazo}
        {--html= : em vez de enviar, grava o HTML neste arquivo}';

    protected $description = 'Envia (ou grava em arquivo) um exemplo do e-mail de atualização cadastral';

    public function handle()
    {
        $email = $this->argument('email');
        $nome = $this->option('nome');
        $link = config('cadastro.url_publica') . '/atualizar-dados/exemplo';

        if ($this->option('vinculo')) {
            $vinculo = ContaCorrespondente::find((int) $this->option('vinculo'));
            if (! $vinculo) {
                $this->error('Vínculo não encontrado.');
                return 1;
            }
            $nome = $vinculo->nm_conta_correspondente_ccr;
            $link = FormularioCadastro::url($vinculo->cd_conta_correspondente_ccr);
        }

        $mail = new AtualizacaoCadastroMail(
            AtualizacaoCadastroMail::nomeFormatado($nome),
            Carbon::today()->addDays((int) $this->option('prazo')),
            $link
        );

        if ($this->option('html')) {
            file_put_contents($this->option('html'), $mail->render());
            $this->info('HTML gravado em ' . $this->option('html'));
            return 0;
        }

        $mail->subject('[TESTE] Atualização cadastral – DMK Advogados');
        Mail::to($email)->send($mail);
        $this->info("E-mail de teste enviado para {$email} (remetente " . config('mail.from.address') . ', resposta para ' . config('cadastro.responder_para.email') . ').');
        $this->line("Link do botão: {$link}");

        return 0;
    }
}
