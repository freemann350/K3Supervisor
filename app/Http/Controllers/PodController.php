<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterException;
use App\Http\Requests\PodRequest;
use App\Models\Cluster;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PodController extends Controller
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

            $response = $client->get("/api/v1/pods");

            $jsonData = json_decode($response->getBody(), true);
            
            $pods = [];
            foreach ($jsonData['items'] as $jsonData) {
                $data['name'] =  $jsonData['metadata']['name'];
                $data['namespace'] =  $jsonData['metadata']['namespace'];
                $data['podIP'] =  isset($jsonData['status']['podIP']) ? $jsonData['status']['podIP'] : "-";
                $data['totalContainers'] = isset($jsonData['status']['containerStatuses']) ? count($jsonData['status']['containerStatuses']) : '';
                $data['status'] =  $jsonData['status']['phase'];

                $pods[] = $data;
            }

            //FILTERS
            $namespaceList = [];
            foreach ($pods as $key => $pod) {
                if ($request->query('showDefault') != "true") {
                    if (!preg_match('/^kube-/', $pod['namespace']))
                    array_push($namespaceList,$pod['namespace']);
                } else {
                    array_push($namespaceList,$pod['namespace']);
                }
            }

            if ($request->query('showNamespaceData') && $request->query('showNamespaceData') != "All") {
                foreach ($pods as $key => $pod) {
                    if ($pod['namespace'] != $request->query('showNamespaceData')) 
                    {
                        unset($pods[$key]);
                    }
                }
            }
            $namespaceList = array_unique($namespaceList);
            //dd($namespaceList);

            if ($request->query('showDefault') != "true") {
                foreach ($pods as $key => $pod) {
                    if (preg_match('/^kube-/', $pod['namespace'])) 
                    {
                        unset($pods[$key]);
                    }
                }
            }

            return view('pods.index', ['pods' => $pods, 'namespaceList' => $namespaceList]);
        } catch (\Exception $e) {
            return view('pods.index', ['conn_error' => $e->getMessage()]);
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

            $response = $client->get("/api/v1/namespaces/$namespace/pods/$id");

            $jsonData = json_decode($response->getBody(), true);
            unset($jsonData['metadata']['managedFields']);
            $json = json_encode($jsonData, JSON_PRETTY_PRINT);
            
            $data = [];
            // METADATA
            $data['name'] = isset($jsonData['metadata']['name']) ? $jsonData['metadata']['name'] : '';
            $data['namespace'] = isset($jsonData['metadata']['namespace']) ? $jsonData['metadata']['namespace'] : '';
            $data['uid'] = isset($jsonData['metadata']['uid']) ? $jsonData['metadata']['uid'] : '';
            $data['creationTimestamp'] = isset($jsonData['metadata']['creationTimestamp']) ? $jsonData['metadata']['creationTimestamp'] : '';
            $data['labels'] = isset($jsonData['metadata']['labels']) ? $jsonData['metadata']['labels'] : '';
            $data['annotations'] = isset($jsonData['metadata']['annotations']) ? $jsonData['metadata']['annotations'] : null;
            
            // SPEC (CONTAINERS)
            $data['containers'] = isset($jsonData['spec']['containers']) ? $jsonData['spec']['containers'] : '';
            $data['restartPolicy'] = isset($jsonData['spec']['restartPolicy']) ? $jsonData['spec']['restartPolicy'] : '';
            $data['terminationGracePeriodSeconds'] = isset($jsonData['spec']['terminationGracePeriodSeconds']) ? $jsonData['spec']['terminationGracePeriodSeconds'] : '';
            $data['nodeName'] = isset($jsonData['spec']['nodeName']) ? $jsonData['spec']['nodeName'] : '';
            
            // STATUS
            $data['status'] = isset($jsonData['status']['phase']) ? $jsonData['status']['phase'] : 'Unkown';
            $data['hostIp'] = isset($jsonData['status']['hostIP']) ? $jsonData['status']['hostIP'] : 'Unkown';
            $data['podIp'] = isset($jsonData['status']['podIP']) ? $jsonData['status']['podIP'] : 'Unkown';
            
            return view('pods.show', ['pod' => $data, 'json' => $json]);
        } catch (\Exception $e) {
            return view('pods.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("pods.create");
    }

    public function store(PodRequest $request): RedirectResponse
    {
        try {
            $formData = $request->validated();

            $data['apiVersion'] = "v1";
            $data['kind'] = "Pod";
            $data['metadata']['name'] = $formData['name'];
            $data['metadata']['namespace'] = $formData['namespace'];

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
            
            $data['spec']['containers'] = [];
            foreach ($formData['containers'] as $container) {
                $arr_container = [];
                $arr_container['name'] = $container['name'];
                $arr_container['image'] = $container['image'];
                
                if (isset($container['ports'])) {
                    $arr_container['ports'] = [];
                    foreach ($container['ports'] as $port) {
                        array_push($arr_container['ports'],['containerPort' => intval($port)]);
                    }
                }

                if (isset($container['env'])) {
                    $arr_container['env'] = [];
                    foreach ($container['env']['key'] as $keyEnv => $env) {
                        $arr_env = [];
                        $arr_env['name'] = $container['env']['key'][$keyEnv];
                        $arr_env['value'] = $container['env']['value'][$keyEnv];

                        array_push($arr_container['env'],$arr_env);
                    }
                }
                
                array_push($data['spec']['containers'],$arr_container);
            };

            $data['spec']['restartPolicy'] = $formData['restartpolicy'];
            if (isset($formData['graceperiod'])) {
                $data['spec']['terminationGracePeriodSeconds'] = intval($formData['graceperiod']);
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

            $response = $client->post("/api/v1/namespaces/".$formData['namespace']."/pods");

            return redirect()->route('Pods.index')->with('success-msg', "Pod '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
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
    
            $response = $client->delete("/api/v1/namespaces/$namespace/pods/$id");

            return redirect()->route('Pods.index')->with('success-msg', "Pod '$id' was deleted with success");
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
