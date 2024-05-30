<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterConnectionException;
use App\Exceptions\ClusterException;
use App\Http\Requests\DeploymentRequest;
use App\Models\Cluster;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DeploymentController extends Controller
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

            $response = $client->get("/apis/apps/v1/deployments");

            $jsonData = json_decode($response->getBody(), true);
            
            // DATA PARSING
            $deployments = [];
            foreach ($jsonData['items'] as $jsonData) {
                $data['name'] =  $jsonData['metadata']['name'];
                $data['namespace'] =  $jsonData['metadata']['namespace'];
                if (isset($jsonData['status']['replicas']) && isset($jsonData['status']['unavailableReplicas'])) {
                    $data['replicas'] =  $jsonData['status']['replicas']-$jsonData['status']['unavailableReplicas'] . "/" . $jsonData['status']['replicas'];
                } else if (isset($jsonData['status']['replicas']) && !isset($jsonData['status']['unavailableReplicas'])) {
                    $data['replicas'] =  $jsonData['status']['replicas']."/".$jsonData['status']['replicas'];
                } else {
                    $data['replicas'] = '-';
                }
                $data['totalContainers'] = isset($jsonData['spec']['template']['spec']['containers']) ? count($jsonData['spec']['template']['spec']['containers']) : '-';
                $deployments[] = $data;
            }

            //FILTERS
            $namespaceList = [];
            foreach ($deployments as $key => $deployment) {
                if ($request->query('showDefault') != "true") {
                    if (!preg_match('/^kube-/', $deployment['namespace']))
                    array_push($namespaceList,$deployment['namespace']);
                } else {
                    array_push($namespaceList,$deployment['namespace']);
                }
            }

            if ($request->query('showNamespaceData') && $request->query('showNamespaceData') != "All") {
                foreach ($deployments as $key => $deployment) {
                    if ($deployment['namespace'] != $request->query('showNamespaceData')) 
                    {
                        unset($deployments[$key]);
                    }
                }
            }
            $namespaceList = array_unique($namespaceList);

            if ($request->query('showDefault') != "true") {
                foreach ($deployments as $key => $deployment) {
                    if (preg_match('/^kube-/', $deployment['namespace'])) 
                    {
                        unset($deployments[$key]);
                    }
                }
            }
            
            return view('deployments.index', ['deployments' => $deployments, 'namespaceList' => $namespaceList]);
        } catch (\Exception $e) {
            return view('deployments.index', ['conn_error' => $e->getMessage()]);
        }
    }

    public function show($namespace, $id): View
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

            $response = $client->get("/apis/apps/v1/namespaces/$namespace/deployments/$id");

            $jsonData = json_decode($response->getBody(), true);
            unset($jsonData['metadata']['managedFields']);
            $json = json_encode($jsonData, JSON_PRETTY_PRINT);
            
            $data = [];
            // METADATA
            $data['name'] = isset($jsonData['metadata']['name']) ? $jsonData['metadata']['name'] : '';
            $data['namespace'] = isset($jsonData['metadata']['namespace']) ? $jsonData['metadata']['namespace'] : '';
            $data['uid'] = isset($jsonData['metadata']['uid']) ? $jsonData['metadata']['uid'] : '';
            $data['creationTimestamp'] = isset($jsonData['metadata']['creationTimestamp']) ? $jsonData['metadata']['creationTimestamp'] : '';
            $data['labels'] = isset($jsonData['metadata']['labels']) ? $jsonData['metadata']['labels'] : null;
            $data['annotations'] = isset($jsonData['metadata']['annotations']) ? $jsonData['metadata']['annotations'] : null;
            
            // SPECS
            $data['strategy'] = isset($jsonData['spec']['strategy']['type']) ? $jsonData['spec']['strategy']['type'] : '';
            $data['rollingUpdate'] = isset($jsonData['spec']['strategy']['rollingUpdate']) ? $jsonData['spec']['strategy']['rollingUpdate'] : null;
            $data['revisionHistoryLimit'] = isset($jsonData['spec']['revisionHistoryLimit']) ? $jsonData['spec']['revisionHistoryLimit'] : '';
            $data['progressDeadlineSeconds'] = isset($jsonData['spec']['progressDeadlineSeconds']) ? $jsonData['spec']['progressDeadlineSeconds'] : '';
            $data['selectorMatchLabels'] = isset($jsonData['spec']['selector']['matchLabels']) ? $jsonData['spec']['selector']['matchLabels'] : null;
                        
            // TEMPLATE SPEC (CONTAINERS)
            $data['containers'] = isset($jsonData['spec']['template']['spec']['containers']) ? $jsonData['spec']['template']['spec']['containers'] : '';
            $data['restartPolicy'] = isset($jsonData['spec']['template']['spec']['restartPolicy']) ? $jsonData['spec']['template']['spec']['restartPolicy'] : '';
            $data['terminationGracePeriodSeconds'] = isset($jsonData['spec']['template']['spec']['terminationGracePeriodSeconds']) ? $jsonData['spec']['template']['spec']['terminationGracePeriodSeconds'] : '';

            // STATUS
            $data['replicas'] = isset($jsonData['status']['replicas']) ? $jsonData['status']['updatedReplicas'] : 'Unkown';
            $data['readyReplicas'] = isset($jsonData['status']['readyReplicas']) ? $jsonData['status']['updatedReplicas'] : 'Unkown';
            $data['availableReplicas'] = isset($jsonData['status']['availableReplicas']) ? $jsonData['status']['updatedReplicas'] : 'Unkown';
            $data['updatedReplicas'] = isset($jsonData['status']['updatedReplicas']) ? $jsonData['status']['updatedReplicas'] : 'Unkown';
            
            return view('deployments.show', ['deployment' => $data, 'json' => $json]);
        } catch (\Exception $e) {
            return view('deployments.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("deployments.create");
    }

    public function store(DeploymentRequest $request): RedirectResponse
    {
        try {
            $formData = $request->validated();
            
            // MAIN INFO
            $data['apiVersion'] = "apps/v1";
            $data['kind'] = "Deployment";
            $data['metadata']['name'] = $formData['name'];
            $data['metadata']['namespace'] = $formData['namespace'];

            // LABELS & ANNOTATIONS
            if (isset($formData['key_labels']) && isset($formData['value_labels'])) {
                foreach ($formData['key_labels'] as $key => $labels) {
                    $data['metadata']['labels'][$formData['key_labels'][$key]] = $formData['value_labels'][$key];
                }
            }

            if (isset($formData['key_annotations']) && isset($formData['value_annotations'])) {
                foreach ($formData['key_annotations'] as $key => $annotations) {
                    $data['metadata']['annotations'][$formData['key_annotations'][$key]] = $formData['value_annotations'][$key];
                }
            }
            
            // REPLICAS & SELECTORS
            $data['spec']['replicas'] = intval($formData['replicas']);
            
            if (isset($formData['key_matchLabels']) && isset($formData['value_matchLabels'])) {
                foreach ($formData['key_matchLabels'] as $key => $labels) {
                    $data['spec']['selector']['matchLabels'][$formData['key_matchLabels'][$key]] = $formData['value_matchLabels'][$key];
                }
            }

            $data['spec']['template']['metadata']['labels']= [];
            if (isset($formData['key_templateLabels']) && isset($formData['value_templateLabels'])) {
                foreach ($formData['key_templateLabels'] as $key => $labels) {
                    $data['spec']['template']['metadata']['labels'][$formData['key_templateLabels'][$key]] = $formData['value_templateLabels'][$key];
                }
            }

            // CONTAINERS
            $data['spec']['template']['spec']['containers'] = [];
            foreach ($formData['containers'] as $container) {
                $arr_container = [];
                
                // MAIN INFO
                $arr_container['name'] = $container['name'];
                $arr_container['image'] = $container['image'];
                $arr_container['imagePullPolicy'] = $container['imagePullPolicy'];
                
                // PORTS
                if (isset($container['ports'])) {
                    $arr_container['ports'] = [];
                    foreach ($container['ports'] as $port) {
                        array_push($arr_container['ports'],['containerPort' => intval($port)]);
                    }
                }

                // ENVIRONMENT VARIABLES
                if (isset($container['env'])) {
                    $arr_container['env'] = [];
                    foreach ($container['env']['key'] as $keyEnv => $env) {
                        $arr_env = [];
                        $arr_env['name'] = $container['env']['key'][$keyEnv];
                        $arr_env['value'] = $container['env']['value'][$keyEnv];

                        array_push($arr_container['env'],$arr_env);
                    }
                }
                
                // PUSH CONTAINER INFO TO CONTAINER LIST
                array_push($data['spec']['template']['spec']['containers'],$arr_container);
            };
            
            // EXTRA INFO
            if (isset($formData['strategy'])) {
                switch ($formData['strategy']) {
                    case 'RollingUpdate':
                        $data['spec']['strategy']['type'] = 'RollingUpdate';
                        $data['spec']['strategy']['rollingUpdate']['maxUnavailable'] = $formData['maxUnavailable'];
                        $data['spec']['strategy']['rollingUpdate']['maxSurge'] = $formData['maxSurge'];
                        break;
                    case 'Recreate':
                        break;
                    default:
                        break;
                }
            }

            if (isset($formData['minReadySeconds'])) {
                $data['spec']['minReadySeconds'] = intval($formData['minReadySeconds']);
            }

            if (isset($formData['revisionHistoryLimit'])) {
                $data['spec']['revisionHistoryLimit'] = intval($formData['revisionHistoryLimit']);
            }

            if (isset($formData['progressDeadlineSeconds'])) {
                $data['spec']['progressDeadlineSeconds'] = intval($formData['progressDeadlineSeconds']);
            }
            
            $jsonData = json_encode($data);



            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'body' => $jsonData,
                'verify' => false,
                'timeout' => $this->timeout
            ]);

            $response = $client->post("/apis/apps/v1/namespaces/".$formData['namespace']."/deployments");

            return redirect()->route('Deployments.index')->with('success-msg', "Deployment '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            
            $errormsg = $this->treat_error($e->getResponse()->getBody()->getContents());
            
            if ($errormsg == null) {
                return redirect()->back()->withInput()->with('error_msg', $errormsg);
            }

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        } catch (\Exception $e) {
            $errormsg = $this->treat_error($e->getMessage());

            if ($errormsg == null) {
                $errormsg['message'] = $e->getMessage();
                $errormsg['status'] = "Internal Server Error";
                $errormsg['code'] = "500";
            }

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        }
    }

    public function destroy($namespace, $id) 
    {
        try {
            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'verify' => false,
                'timeout' => $this->timeout
            ]);
    
            $response = $client->delete("/apis/apps/v1/namespaces/$namespace/deployments/$id");

            return redirect()->route('Deployments.index')->with('success-msg', "Deployment '$id' was deleted with success");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            
            $errormsg = $this->treat_error($e->getResponse()->getBody()->getContents());
            
            if ($errormsg == null) {
                return redirect()->back()->withInput()->with('error_msg', $errormsg);
            }

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        } catch (\Exception $e) {
            $errormsg = $this->treat_error($e->getMessage());

            if ($errormsg == null) {
                $errormsg['message'] = $e->getMessage();
                $errormsg['status'] = "Internal Server Error";
                $errormsg['code'] = "500";
            }

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        }
    }

    private function treat_error($errorMessage) 
    {
        $error = null;

        $jsonData = json_decode($errorMessage, true);

        if (isset($jsonData['message']))
            $error['message'] = $jsonData['message'];
        if (isset($jsonData['status']))
            $error['status'] = $jsonData['status'];
        if (isset($jsonData['code']))
            $error['code'] = $jsonData['code'];

        return $error;
    }
}
