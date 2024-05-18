<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeploymentRequest;
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
        $this->endpoint = env("K8S_API_ENDPOINT", "https://localhost:6443");
        $this->token = "Bearer " . env("K8S_BEARER_TOKEN");
        $this->timeout  = env("K8S_CONNECTION_TIMEOUT", 5);
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
                'timeout' => 5
            ]);

            $response = $client->get("/apis/apps/v1/deployments");

            $jsonData = json_decode($response->getBody(), true);
            
            $deployments = [];
            if ($request->query('showDefault') == "true") {
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
            } else {
                foreach ($jsonData['items'] as $jsonData) {
                    if (!preg_match('/^kube-/', $jsonData['metadata']['namespace'])) {
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
                }
            }
            
            return view('deployments.index', ['deployments' => $deployments]);
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
                'timeout' => 5
            ]);

            $response = $client->get("/apis/apps/v1/namespaces/$namespace/deployments/$id");

            $data = json_decode($response->getBody(), true);

            return view('deployments.show', ['deployment' => $data]);
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
        $data['spec']['replicas'] = 3;
        
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

        try {

            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'body' => $jsonData,
                'verify' => false,
                'timeout' => 5
            ]);

            $response = $client->post("/apis/apps/v1/namespaces/".$formData['namespace']."/deployments");

            return redirect()->route('Deployments.index')->with('success-msg', "Deployment '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            dd($e->getResponse()->getBody()->getContents());
        } catch (\Exception $e) {
            //TODO: ERROR PARSING
            $error = $this->treat_error($e->getMessage());
            if ($error == null)
                dd($e->getMessage());

            return redirect()->back()->withInput()->with('error-msg', $error);
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
                'timeout' => 5
            ]);
    
            $response = $client->delete("/apis/apps/v1/namespaces/$namespace/deployments/$id");

            return redirect()->route('Deployments.index')->with('success-msg', "Deployment '$id' was deleted with success");
        } catch (\Exception $e) {
            $error = $this->treat_error($e->getMessage());

            if ($error == null)
                dd($e->getMessage());

            return redirect()->back()->withInput()->with('error-msg', $error);
        }
    }

    private function treat_error($errorMessage) 
    {
        $error = null;

        // Search for the detail and error information within the error message
        if (preg_match('/"detail":\s*"([^"]+)"/', $errorMessage, $matches)) {
            $error['detail'] = $matches[1];
        } else {
            $error['detail'] = null;
        }
    
        if (preg_match('/"error":\s*(\d+)/', $errorMessage, $matches)) {
            $error['error'] = (int) $matches[1];
        } else {
            $error['error'] = null;
        }        

        if (preg_match('/"message":\s*"([^"]+)"/', $errorMessage, $matches)) {
            $error['message'] = $matches[1];
        } else {
            $error['message'] = null;
        }

        if ($error['detail'] == null && $error['error'] == null && $error['message'] == null)
            return null;

        return $error;
    }
}
