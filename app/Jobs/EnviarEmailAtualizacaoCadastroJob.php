<?php

namespace App\Jobs;

use App\User;
use App\ContaCorrespondente;
use App\Notifications\CorrespondenteAtualizacaoCadastroNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EnviarEmailAtualizacaoCadastroJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Número de tentativas em caso de falha (ex.: SMTP temporariamente indisponível)
    public $tries = 3;

    // Espera 60 s antes de retentar
    public $backoff = 60;

    protected int $cdContaCorrespondenteCcr;

    public function __construct(int $cdContaCorrespondenteCcr)
    {
        $this->cdContaCorrespondenteCcr = $cdContaCorrespondenteCcr;
    }

    public function handle(): void
    {
        $vinculo = ContaCorrespondente::find($this->cdContaCorrespondenteCcr);

        if (!$vinculo) {
            return;
        }

        $user = User::where('cd_conta_con', $vinculo->cd_correspondente_cor)->first();

        if (!$user || empty($user->email)) {
            Log::warning("EnviarEmailAtualizacaoCadastroJob: email não encontrado para cd_conta_correspondente_ccr={$this->cdContaCorrespondenteCcr}");
            return;
        }

        $token = \Crypt::encrypt($vinculo->cd_conta_correspondente_ccr);
        $link  = url('atualizar-cadastro/' . $token);

        Notification::route('mail', $user->email)
            ->notify(new CorrespondenteAtualizacaoCadastroNotification($link));
    }
}
