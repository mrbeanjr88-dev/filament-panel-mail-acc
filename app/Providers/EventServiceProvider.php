<?php

namespace App\Providers;

use App\Events\EmailProcessedEvent;
use App\Listeners\SendSlackNotificationListener;
use App\Listeners\SendWebhookNotificationListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmailProcessedEvent::class => [
            SendWebhookNotificationListener::class,
            SendSlackNotificationListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
