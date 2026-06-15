<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\LogNotificationListener;
use App\Listeners\EmailMailLogListener;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationFailed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'Illuminate\Log\Events\MessageLogged' => [
            'App\Listeners\LogNotificationListener',
        ],
        MessageSending::class => [
            [EmailMailLogListener::class, 'onSending'],
        ],
        MessageSent::class => [
            [EmailMailLogListener::class, 'onSent'],
        ],
        NotificationFailed::class => [
            [EmailMailLogListener::class, 'onNotificationFailed'],
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}
