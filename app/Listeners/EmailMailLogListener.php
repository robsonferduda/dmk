<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationFailed;

class EmailMailLogListener
{
    public function onSending(MessageSending $event): void
    {
        $this->gravar('sending', $this->dadosMensagem($event->message));
    }

    public function onSent(MessageSent $event): void
    {
        $this->gravar('sent', $this->dadosMensagem($event->message));
    }

    public function onNotificationFailed(NotificationFailed $event): void
    {
        $this->gravar('notification_failed', [
            'channel'      => $event->channel,
            'notification' => get_class($event->notification),
            'notifiable'   => is_object($event->notifiable) ? get_class($event->notifiable) : gettype($event->notifiable),
            'data'         => $event->data,
        ]);
    }

    private function dadosMensagem($message): array
    {
        if (is_object($message) && method_exists($message, 'getSwiftMessage')) {
            $message = $message->getSwiftMessage();
        }

        $to = method_exists($message, 'getTo') ? array_keys($message->getTo() ?: []) : [];
        $from = method_exists($message, 'getFrom') ? array_keys($message->getFrom() ?: []) : [];

        return [
            'assunto' => method_exists($message, 'getSubject') ? $message->getSubject() : null,
            'para'    => $to,
            'de'      => $from,
        ];
    }

    private function gravar(string $evento, array $dados): void
    {
        $linha = '[' . now()->format('Y-m-d H:i:s') . "] {$evento} " . json_encode($dados, JSON_UNESCAPED_UNICODE);

        Log::info('[MAIL] ' . $evento, $dados);

        @file_put_contents(
            storage_path('logs/mail.log'),
            $linha . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
