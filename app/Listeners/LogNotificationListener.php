<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Log\Events\MessageLogged;

class LogNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessageLogged $event)
    {
        // Verifica se o nível do log é crítico (error, critical, etc.)
        if (in_array($event->level, ['error', 'critical'])) {
            $this->sendErrorEmail($event);
        }
    }

    protected function sendErrorEmail(MessageLogged $event)
    {
        $to = 'robsonferduda@gmail.com';

        $errorDetails = [
            'Message' => $event->message,
            'Context' => $event->context,
            'Level' => $event->level,
        ];

        Mail::raw(print_r($errorDetails, true), function ($message) use ($to) {
            $message->to($to)
                    ->subject('Erro Capturado na Aplicação');
        });
    }
}
