<?php

namespace App\Listeners;

use App\Events\OpinionCreated;
use App\Notification\OpinionCreationNotification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOpinionCreationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OpinionCreated $event): void
    {
        //Notification::send($users, new OpinionCreationNotification($event->opinion));
    }
}
