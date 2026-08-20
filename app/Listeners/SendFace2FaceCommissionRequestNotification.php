<?php

namespace App\Listeners;

use App\Events\Face2FaceCommissionRequestCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFace2FaceCommissionRequestNotification
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
    public function handle(Face2FaceCommissionRequestCreated $event): void
    {
        //
    }
}
