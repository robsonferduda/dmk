<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
        // Executa limpeza de arquivos diariamente às 02:00
        $schedule->command('arquivos:limpar')
                 ->dailyAt('02:00')
                 ->appendOutputTo(storage_path('logs/limpeza-arquivos.log'));

        // [WHATSAPP-LEMBRETE] Lembrete aos correspondentes sobre
        // diligências do dia (prazo fatal = hoje), com link de check-in.
        // --conta=64: fase de testes, restrito ao escritório 64. Remover quando for para produção geral.
        $schedule->command('whatsapp:lembrete-diligencias')
                 ->dailyAt('08:00')
                 ->appendOutputTo(storage_path('logs/whatsapp-lembrete.log'));

        // [WHATSAPP-LEMBRETE PRÉ] Lembrete pré-diligência (prazo fatal = amanhã)
        // Executado às 13h do dia anterior à audiência.
        // --conta=64: fase de testes, restrito ao escritório 64. Remover quando for para produção geral.
        $schedule->command('whatsapp:lembrete-prediligencias')
             ->weekdays()
             ->dailyAt('13:00')
             ->appendOutputTo(storage_path('logs/whatsapp-lembrete-pre.log'));

        // [PAGAMENTOS] Consolida diariamente os pagamentos devidos aos correspondentes
        // do mês corrente. Executa às 00:30 para refletir os processos do dia anterior.
        $schedule->command('pagamentos:consolidar')
                 ->dailyAt('00:30')
                 ->appendOutputTo(storage_path('logs/pagamentos-consolidar.log'));
                 */

        // [CADASTRO] Atualização cadastral em lotes pequenos ao longo do dia útil; o teto diário fica em config/cadastro.php.
        $schedule->command('cadastro:enviar-lote')
                 ->everyTenMinutes()
                 ->weekdays()
                 ->between('8:00', '18:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/cadastro-envio.log'));

        // [AUTENTIQUE] Rede de segurança dos webhooks: atualiza os contratos aguardando assinatura.
        $schedule->command('autentique:sincronizar')
                 ->hourlyAt(15)
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/autentique-sincronizar.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
