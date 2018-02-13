<?php

namespace App\Listeners;

use App\Brandshop\FSM\Event\TransitionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StateHistroyManager
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TransitionEvent $event)
    {
        // TODO 1. state change history; 2. test
    }
}
