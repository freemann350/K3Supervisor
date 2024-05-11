<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use GuzzleHttp\Client;

class NodeController extends Controller
{
    
    private $endpoint;
    private $token;
    private $timeout;

    public function __construct()
    {
        $this->endpoint = env("K8S_API_ENDPOINT", "https://localhost:6443");
        $this->token = "Bearer " . env("K8S_BEARER_TOKEN");
        $this->timeout  = env("K8S_CONNECTION_TIMEOUT", 5);
    }

    public function index(): View
    {
        try {
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 5
            ]);

            $response = $client->get("/api/v1/nodes");

            $data = json_decode($response->getBody(), true);

            return view('nodes.index', ['nodes' => $data]);
        } catch (\Exception $e) {
            return view('nodes.index', ['conn_error' => $e->getMessage()]);
        }
    }
}
