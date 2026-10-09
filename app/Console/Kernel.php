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
        // Lembretes de WhatsApp, limpeza de arquivos e consolidação de pagamentos rodam pelo crontab do servidor, não por aqui.

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
