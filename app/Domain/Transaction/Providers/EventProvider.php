<?php

namespace App\Domain\Transaction\Providers;

use App\Domain\Transaction\Events\SendNotification;
use App\Domain\Transaction\Listeners\NotificationListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class EventProvider extends EventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SendNotification::class => [
            NotificationListener::class,
        ],
    ];
}
