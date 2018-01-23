<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * @var UserVerification
     */
    protected $userVerification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, UserVerification $userVerification)
    {
        $this->user = $user;
        $this->userVerification = $userVerification;
        $this->onQueue('emails');
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return UserVerification
     */
    public function getUserVerification()
    {
        return $this->userVerification;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.users.verification')
            ->with([
                'user' => $this->user,
                'verifyUrl' => route('user.verify', [
                    'token' => $this->userVerification->token
                ]),
            ]);
    }
}
