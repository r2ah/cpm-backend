<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Notification\AppointmentCreationNotification;

use App\Events\AppointmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentNotification implements ShouldQueue
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
    public function handle(AppointmentCreated $event): void
    {
        //Notification::send($users, new AppointmentCreationNotification($event->opinion));
    }
}
