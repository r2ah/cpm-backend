<?php

namespace App\Listeners;

use App\Events\OpinionStateChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogOpinionStateChange
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
    public function handle(OpinionStateChanged $event): void
    {
        //
    }
}
