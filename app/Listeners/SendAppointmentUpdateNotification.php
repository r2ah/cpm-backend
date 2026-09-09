<?php

namespace App\Listeners;

use App\Events\AppoinmentUpdated;
use App\Notification\AppointmentUpdateNotification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentUpdateNotification
{
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
    public function handle(AppoinmentUpdated $event): void
    {
        //Notification::send($users, new AppointmentUpdatedNotification($event->opinion));
    }
}
