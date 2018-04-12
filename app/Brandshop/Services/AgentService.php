<?php


namespace App\Brandshop\Services;


use GuzzleHttp\Client;
use Illuminate\Foundation\Application;

class AgentService
{
    /**
     * @var Client
     */
    protected $client;

    protected $agentApi;

    public function __construct(Client $client)
    {
        $this->agentApi = config('brandshop.agent.api');
        $this->client = new Client([
            'base_uri' => $this->agentApi,
        ]);
    }

    public function getAllCategories()
    {
        $response = $this->client->get('categories');
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            throw new \Exception('Api fail: ' . $response->getBody()->getContents());
        }
    }
}