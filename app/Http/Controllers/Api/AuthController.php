<?php

namespace App\Http\Controllers\Api;

use App\Brandshop\Auth\Proxy;
use App\Events\UserRegistered;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authProxy;

    public function __construct(Proxy $authProxy)
    {
        $this->authProxy = $authProxy;
    }

    public function login(Request $request)
    {
        if ($this->guard()->check()) {
            throw new \Exception('You have already login in.');
        }

        $credentials = $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        return $this->authProxy->attempt($credentials);
    }

    public function refreshToken(Request $request)
    {
        if (!$refreshToken = $request->get('refresh_token')) {
            throw new \InvalidArgumentException('Missing refresh_token.');
        }

        return $this->authProxy->refreshToken($refreshToken);
    }

    public function logout(Request $request)
    {
        $this->authProxy->logout($this->guard()->user()->token());
    }

    public function register(Request $request)
    {
        if ($this->guard()->check()) {
            throw new \Exception('You have already login in.');
        }

        $data = $this->validate($request, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $userVerification = UserVerification::create([
            'user_id' => $user->id,
            'token' => str_random(30),
        ]);

        // fire
        event(new UserRegistered($user, $userVerification));

        return $this->respondSuccess('You have successfully registered. An email is sent to you for verification');
    }

    public function verifyUser($token)
    {
        $userVerification = UserVerification::where('token', $token)
            ->firstOrFail();

        if ($userVerification->isExpired()) {
            throw new \Exception('Your verification request is expired');
        }

        $userVerification->user->verified();
        $userVerification->delete();

        return $this->respondSuccess('Congratulations. Verified');
    }

    // TODO reset password
    public function resetPassword()
    {

    }

    // TODO forgot password
    public function forgotPassword()
    {

    }

    protected function guard()
    {
        return Auth::guard('api');
    }
}
