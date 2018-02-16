<?php

namespace App\Providers;

use App\Brandshop\FSM\Event\TransitionEvent;
use App\Events\UserRegistered;
use App\Listeners\SendUserVerificationNotification;
use App\Listeners\StateHistoryManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class => [
            SendUserVerificationNotification::class,
        ],
        TransitionEvent::POST_TRANSITION => [
            StateHistoryManager::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
