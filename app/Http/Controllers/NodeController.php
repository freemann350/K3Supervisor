<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterConnectionException;
use App\Exceptions\ClusterException;
use App\Models\Cluster;
use Illuminate\View\View;
use GuzzleHttp\Client;

class NodeController extends Controller
{
    
    private $endpoint;
    private $token;
    private $timeout;

    public function __construct()
    {
        if (!session('clusterId')) 
            throw new ClusterException();

        $cluster = Cluster::findOrFail(session('clusterId'));

        if ($this->checkConnection($cluster) == -1)
            throw new ClusterConnectionException();

        $this->endpoint = $cluster['endpoint'];
        $this->token = "Bearer " . $cluster['token'];
        $this->timeout  = $cluster['timeout'];
    }

    private function checkConnection($cluster) {
        
        try {
            if ($cluster['auth_type'] == 'P') {
                $client = new Client([
                    'base_uri' => $cluster['endpoint'],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'verify' => false,
                    'timeout' => 0.5
                ]);     
            } else {
                $client = new Client([
                    'base_uri' => $cluster['endpoint'],
                    'headers' => [
                        'Authorization' => "Bearer ". $cluster['token'],
                        'Accept' => 'application/json',
                    ],
                    'verify' => false,
                    'timeout' => 0.5
                ]);
            }
            $response = $client->get("/api/v1");
            $online = $response->getStatusCode();
        } catch (\Exception $e) {
            $online = -1;
        }

        return $online;
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
                'timeout' => $this->timeout
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
