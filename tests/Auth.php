<?php


namespace Tests;


use App\Models\User;

trait Auth
{
    protected $hashedPassword;

    protected $password = '123456';

    /**
     * @return string access token
     */
    protected function login($credentials = null)
    {
        if ($credentials) {
            $email = $credentials['email'];
            $password = $credentials['password'];
        } else {
            $password = $this->password;
            $user = factory(User::class)->create(['password' => $this->getHashedPassword()]);
            $email = $user->email;
        }

        $response = $this->json('post', route('api.login'), compact('email', 'password'));
        $result = json_decode($response->getContent(), true);
        return $result['access_token'];
    }

    /**
     * @param $token
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function requestAsToken($token, $method, $uri, array $data = [], array $headers = [])
    {
        $headers = array_merge([
            'Authorization' => 'Bearer ' . $token,
        ], $headers);

        return $this->json($method, $uri, $data, $headers);
    }


    /**
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function requestAsLogined($method, $uri, array $data = [], array $headers = [])
    {
        return $this->requestAsToken($this->login(), $method, $uri, $data, $headers);
    }

    protected function getHashedPassword()
    {
        if (!$this->hashedPassword) {
            $this->hashedPassword = bcrypt($this->password);
        }
        return $this->hashedPassword;
    }
}