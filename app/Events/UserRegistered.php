<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UserVerification
     */
    protected $userVerification;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, UserVerification $userVerification)
    {
        $this->user = $user;
        $this->userVerification = $userVerification;
    }

    public function getUser()
    {
        return $this->user;
    }


    public function getUserVerification()
    {
        return $this->userVerification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}