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
    
            $jsonData = json_decode($response->getBody(), true);
            
            $nodes = [];
            foreach ($jsonData['items'] as $jsonData) {
                $data['hostname'] =  $jsonData['metadata']['labels']['kubernetes.io/hostname'];
                $data['arch'] =  $jsonData['metadata']['labels']['kubernetes.io/os'] . " (" .  $jsonData['metadata']['labels']['kubernetes.io/arch'] .")";
    
                if (isset($jsonData['metadata']['labels']['node-role.kubernetes.io/master'])) {
                    $data['master'] =  "true";
                } else {
                    $data['master'] = "false";
                }
    
                $data['instance'] =  $jsonData['metadata']['labels']['node.kubernetes.io/instance-type'];
                $data['podCIDRs'] = $jsonData['spec']['podCIDRs'];
                
                foreach ($jsonData['status']['conditions'] as $condition) {
                    if ($condition['type'] === 'Ready' && $condition['status'] == "True") {
                        $data['status'] = $condition['status'];
                        break;
                    }
                }
                
                $data['os'] =  $jsonData['status']['nodeInfo']['osImage'];

                $nodes[] = $data;
            }
            
            return view('nodes.index', ['nodes' => $nodes]);
        } catch (\Exception $e) {
            return view('nodes.index', ['conn_error' => $e->getMessage()]);
        }
    }
}
