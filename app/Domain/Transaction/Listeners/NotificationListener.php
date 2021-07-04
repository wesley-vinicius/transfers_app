<?php

namespace App\Domain\Transaction\Listeners;

use App\Domain\Transaction\Events\SendNotification;
use App\Domain\Transaction\Jobs\NotificationsTransactionJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Domain\Transaction\Events\SendNotification  $event
     *
     * @return void
     */
    public function handle(SendNotification $event)
    {
        dispatch(new NotificationsTransactionJobs($event->transaction));
    }
}
