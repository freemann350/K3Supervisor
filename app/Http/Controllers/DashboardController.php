<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterConnectionException;
use App\Exceptions\ClusterException;
use App\Models\Cluster;
use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class DashboardController extends Controller
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

    public function index(Request $request): View
    {
        $events = $this->getEvents();
        $nodes = $this->getNodeInfo();
        $resources = $this->getTotalResources();

        return view('dashboard.index',['events' => $events, 'nodes' => $nodes, 'resources' => $resources]);
    }

    private function getNodeInfo() {
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
                $data['name'] =  $jsonData['metadata']['labels']['kubernetes.io/hostname'];
                $data['master'] = isset($jsonData['metadata']['labels']['node-role.kubernetes.io/master']) ? true : false;
                $data['os'] =  $jsonData['status']['nodeInfo']['osImage'];
                foreach ($jsonData['status']['addresses'] as $address) {
                    if ($address['type'] == 'InternalIP') {
                        $data['ip'] = $address['address'];
                        break;
                    } else {
                        $data['ip'] = null;
                    }
                }
                $data['podCidr'] = $jsonData['spec']['podCIDR'];
                $data['cpus'] =  $jsonData['status']['capacity']['cpu'];
                $data['arch'] =  $jsonData['metadata']['labels']['beta.kubernetes.io/arch'];
                $data['memory'] =  $jsonData['status']['capacity']['memory'];
                $data['memory'] = intval(str_replace('Ki','',$data['memory']));
                $data['memory'] = round($data['memory']/1000000);
                $nodes[] = $data;
            }

            return array_reverse($nodes);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getTotalResources() {
        try {
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 1
            ]);
    
            $promises = [
                'namespaces' => $client->getAsync("/api/v1/namespaces"),
                'pods' => $client->getAsync("/api/v1/pods"),
                'deployments' => $client->getAsync("/apis/apps/v1/deployments"),
                'services' => $client->getAsync("/api/v1/services"),
                'ingresses' => $client->getAsync("/apis/networking.k8s.io/v1/ingresses"),
            ];
    
            $responses = Promise\Utils::settle($promises)->wait();
    
            $totalResources = [];
            foreach ($responses as $key => $result) {
                if ($result['state'] === 'fulfilled') {
                    $jsonData = json_decode($result['value']->getBody(), true);
                    $totalResources[$key] = count($jsonData['items']);
                } else {
                    $totalResources[$key] = 0;
                }
            }
    
            return $totalResources;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEvents() {
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

            $response = $client->get("/api/v1/events");
            $jsonData = json_decode($response->getBody(), true);

            $events = [];
            foreach ($jsonData['items'] as $jsonData) {
                try {
                    $data['kind'] =  $jsonData['involvedObject']['kind'];
                    $data['name'] =  $jsonData['involvedObject']['name'];
                    $data['namespace'] =  isset($jsonData['involvedObject']['namespace']) ? $jsonData['involvedObject']['namespace'] : '-';
                    $data['type'] =  $jsonData['type'];
                    $data['time'] =  $jsonData['eventTime'];
                    $data['startTime'] =  $jsonData['firstTimestamp'];
                    $data['endTime'] =  $jsonData['lastTimestamp'];
                    $data['message'] =  isset($jsonData['message']) ? $jsonData['message'] : '-';
                    $events[] = $data;
                } catch (\Throwable $th) {
                    $events[] = null;
                }
            }

            return $events;
        } catch (\Exception $e) {
            return null;
        }
    }
}
