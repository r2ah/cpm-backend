<?php

namespace App\Listeners;

use App\Events\OpinionStateChanged;
use App\Notifications\OpinionStateChangedNotification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOpinionStateChangeNotification implements ShouldQueue
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
    public function handle(OpinionStateChanged $event): void
    {
        //Notification::send($users, new OpinionStateChangedNotification($event->opinion));
    }
}
