<?php

namespace Tests\Feature\Controllers\Api;

use App\Events\UserRegistered;
use App\Mail\UserVerificationMail;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        Event::fake();

        $firstname = 'foo';
        $lastname = 'bar';
        $email = 'foo@bar.com';
        $password = '123456';
        $password_confirmation = '123456';

        $response = $this->json('post', '/api/register', compact('firstname', 'lastname', 'email', 'password', 'password_confirmation'));
        $response->assertSuccessful();
        $user = User::where('email', $email)->first();
        $this->assertNotEmpty($user);
        $this->assertEquals(false, $user->isVerified());


        Event::assertDispatched(UserRegistered::class, function ($event) use ($user) {
            /** @var UserRegistered $event */
            return $event->getUser()->id = $user->id && $event->getUserVerification()->user->id === $user->id;
        });
    }

    public function testVerifyUser()
    {
        Mail::fake();

        $firstname = 'foo';
        $lastname = 'bar';
        $email = 'foo@foo.com';
        $password = '123456';
        $password_confirmation = '123456';

        $response = $this->json('post', route('api.register'), compact('email', 'firstname', 'lastname', 'password', 'password_confirmation'));
        $response->assertSuccessful();
        $user = User::where('email', $email)->first();

        $token = '';

        Mail::assertQueued(UserVerificationMail::class, function ($mail) use ($user, &$token) {
            $token = $mail->getUserVerification()->token;
            return $mail->hasTo($user->email)
                && $mail->getUserVerification()->user_id === $user->id;
        });

        $verifiedResponse = $this->get(route('user.verify', compact('token')));
        $verifiedResponse->assertSuccessful();
        $this->assertEmpty(UserVerification::where('token', $token)->first());
    }


    public function testLoginAndLogout()
    {
        $password = '123456';

        $user = factory(User::class)->create(['password' => bcrypt($password)]);
        $email = $user->email;

        $this->assertGuest();
        $response = $this->json('post', route('api.login'), compact('email', 'password'));
        $response->assertSuccessful();
        $result = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $result);


        Passport::actingAs($user);
        $logoutResponse = $this->json('get', route('api.logout'), []);
        $logoutResponse->assertSuccessful();
    }
}
