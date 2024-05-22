<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterException;
use App\Models\Cluster;
use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;

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
        $this->endpoint = $cluster['endpoint'];
        $this->token = "Bearer " . $cluster['token'];
        $this->timeout  = $cluster['timeout'];
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
                'timeout' => 5
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
            // NAMESPACES
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 0.5
            ]);
            
            $response = $client->get("/api/v1/namespaces");
            $jsonData = json_decode($response->getBody(), true);
            $totalResources['namespaces'] = count($jsonData['items']);

            // PODS
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 0.5
            ]);
            
            $response = $client->get("/api/v1/pods");
            $jsonData = json_decode($response->getBody(), true);
            $totalResources['pods'] = count($jsonData['items']);

            // DEPLOYMENTS
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 0.5
            ]);
            
            $response = $client->get("/apis/apps/v1/deployments");
            $jsonData = json_decode($response->getBody(), true);
            $totalResources['deployments'] = count($jsonData['items']);

            // SERVICES
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 0.5
            ]);
            
            $response = $client->get("/api/v1/services");
            $jsonData = json_decode($response->getBody(), true);
            $totalResources['services'] = count($jsonData['items']);
            
            // INGRESSES
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 0.5
            ]);
            
            $response = $client->get("/apis/networking.k8s.io/v1/ingresses");
            $jsonData = json_decode($response->getBody(), true);
            $totalResources['ingresses'] = count($jsonData['items']);

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
                'timeout' => 5
            ]);

            $response = $client->get("/api/v1/events");
            
            $jsonData = json_decode($response->getBody(), true);

            $events = [];
            foreach ($jsonData['items'] as $jsonData) {
                $data['kind'] =  $jsonData['involvedObject']['kind'];
                $data['name'] =  $jsonData['involvedObject']['name'];
                $data['namespace'] =  $jsonData['involvedObject']['namespace'];
                $data['type'] =  $jsonData['type'];
                $data['time'] =  $jsonData['eventTime'];
                $data['startTime'] =  $jsonData['firstTimestamp'];
                $data['endTime'] =  $jsonData['lastTimestamp'];
                $data['message'] =  $jsonData['message'];
                $events[] = $data;
            }


            return array_reverse($events);
        } catch (\Exception $e) {
            return null;
        }
    }
}
