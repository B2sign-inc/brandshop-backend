<?php


namespace App\Brandshop\Auth;


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use Laravel\Passport\Token;

class Proxy
{
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var ClientResolver
     */
    protected $clientResolver;

    public function __construct(Application $app, ClientResolver $clientResolver)
    {
        $this->app = $app;
        $this->request = $app->make('request');
        $this->db = $app->make('db');
        
        $this->clientResolver = $clientResolver;
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->clientResolver->resolve();
    }

    /**
     * attempt login
     * @param $credentials
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attempt($credentials)
    {
        if (!isset($credentials['email'])) {
            throw new \InvalidArgumentException('Missing email');
        }

        if (!isset($credentials['password'])) {
            throw new \InvalidArgumentException('Missing email');
        }

        // passport use username as credential parameter
        return $this->proxy('password', [
            'username' => $credentials['email'],
            'password' => $credentials['password'],
        ]);
    }

    public function refreshToken($refreshToken)
    {
        return $this->proxy('refresh_token', ['refresh_token' => $refreshToken]);
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied
     * @param array $data the data to send to the server
     * @param string $scope the scope of access
     */
    protected function proxy($grantType, $data = [], $scope = '*')
    {
        $client = $this->getClient();

        return $this->app->handle(Request::create(
            'api/token',
            'POST',
            array_merge($data, [
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'grant_type' => $grantType,
                'scope' => $scope,
            ])
        ));
    }

    public function logout(Token $token)
    {
        $token->revoke();

        $refreshToken = $this->db
            ->table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
            ->update([
                'revoked' => true
            ]);
    }
}