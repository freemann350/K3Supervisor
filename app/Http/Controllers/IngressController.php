<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterException;
use App\Http\Requests\IngressRequest;
use App\Models\Cluster;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;

class IngressController extends Controller
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

            $response = $client->get("/apis/networking.k8s.io/v1/ingresses");


            $jsonData = json_decode($response->getBody(), true);
            
            $ingresses = [];
            foreach ($jsonData['items'] as $jsonData) {
                $data['name'] =  $jsonData['metadata']['name'];
                $data['namespace'] =  $jsonData['metadata']['namespace'];
                
                $i=0;
                $data['services'][$i] = [];
                foreach ($jsonData['spec']['rules'] as $rules) {
                    foreach ($rules['http']['paths'] as $path) {
                        $data['services'][$i]['path'] = $path['path'];
                        $data['services'][$i]['type'] = $path['pathType'];
                        
                        foreach ($path['backend'] as $backend) {
                            $data['services'][$i]['serviceName'] = $backend['name'];
                            $data['services'][$i]['port'] = $backend['port']['number'];
                        }
                        $i++;
                    }
                }

                $data['ingressIP'] = isset($jsonData['status']['loadBalancer']['ingress']) ? $jsonData['status']['loadBalancer']['ingress'] : '-';                
                $ingresses[] = $data;
            }

            //FILTERS
            $namespaceList = [];
            foreach ($ingresses as $key => $ingress) {
                if ($request->query('showDefault') != "true") {
                    if (!preg_match('/^kube-/', $ingress['namespace']))
                    array_push($namespaceList,$ingress['namespace']);
                } else {
                    array_push($namespaceList,$ingress['namespace']);
                }
            }

            if ($request->query('showNamespaceData') && $request->query('showNamespaceData') != "All") {
                foreach ($ingresses as $key => $ingresse) {
                    if ($ingresse['namespace'] != $request->query('showNamespaceData')) 
                    {
                        unset($ingresses[$key]);
                    }
                }
            }
            $namespaceList = array_unique($namespaceList);

            if ($request->query('showDefault') != "true") {
                foreach ($ingresses as $key => $ingresse) {
                    if (preg_match('/^kube-/', $ingresse['namespace'])) 
                    {
                        unset($ingresses[$key]);
                    }
                }
            }

            return view('ingresses.index', ['ingresses' => $ingresses, 'namespaceList' => $namespaceList]);
        } catch (\Exception $e) {
            return view('ingresses.index', ['conn_error' => $e->getMessage()]);
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

            $response = $client->get("/apis/networking.k8s.io/v1/namespaces/$namespace/ingresses/$id");

            $data = json_decode($response->getBody(), true);

            return view('ingresses.show', ['ingresses' => $data]);
        } catch (\Exception $e) {
            return view('ingresses.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("ingresses.create");
    }

    public function store(IngressRequest $request): RedirectResponse
    {
        try {
            $formData = $request->validated();
            
            // MAIN INFO
            $data['apiVersion'] = "networking.k8s.io/v1";
            $data['kind'] = "Ingress";
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

            
            // RULES
            $rules = [];
            if (isset($formData['rules'])) {
                foreach ($formData['rules'] as $key => $rule) {
                    $arrRule = [];
                    if (isset($rules['host']))
                        $rule['host'] = $rules['host'];
                    
                    $paths = [];
                    foreach ($rule['path']['pathName'] as $keyPathName => $pathName) {
                        $path = [];

                        $path['path'] = $pathName;
                        $path['pathType'] = $rule['path']['pathType'][$keyPathName];
                        $path['backend']['service']['name'] = $rule['path']['serviceName'][$keyPathName];
                        $path['backend']['service']['port']['number'] = intval($rule['path']['portNumber'][$keyPathName]);
                        $paths[] = $path;
                    }
                    
                    $arrRule['http']['paths'] = $paths;
                    $rules[] = $arrRule;
                }
            }
            $data['spec']['rules'] = $rules;
        
            // EXTRA INFO
            if (isset($formData['defaultBackendName']) && isset($formData['defaultBackendPort'])) {
                $data['spec']['defaultBackend']['service']['name'] = $formData['defaultBackendName'];
                $data['spec']['defaultBackend']['service']['port']['number'] = intval($formData['defaultBackendPort']);
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

            $response = $client->post("/apis/networking.k8s.io/v1/namespaces/".$formData['namespace']."/ingresses");

            return redirect()->route('Ingresses.index')->with('success-msg', "Ingress '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
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
    
            $response = $client->delete("/apis/networking.k8s.io/v1/namespaces/$namespace/ingresses/$id");

            return redirect()->route('Ingresses.index')->with('success-msg', "Ingress '$id' was deleted with success");
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
            $error['status'] = $jsonData['status'] ."(".$jsonData['reason'].")";
        if (isset($jsonData['code']))
            $error['code'] = $jsonData['code'];

        return $error;
    }
}
