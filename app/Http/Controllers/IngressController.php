<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngressRequest;
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

            $response = $client->get("/apis/networking.k8s.io/v1/ingresses");


            $jsonData = json_decode($response->getBody(), true);
            
            $ingresses = [];
            if ($request->query('showDefault') == "true") {
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
            } else {
                foreach ($jsonData['items'] as $jsonData) {
                    if (!preg_match('/^kube-/', $jsonData['metadata']['namespace'])) {
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
                }
            }

            return view('ingresses.index', ['ingresses' => $ingresses]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            
            $errormsg = $this->treat_error($e->getResponse()->getBody()->getContents());
            
            if ($errormsg == null) {
                return view('ingresses.index', ['conn_error' => $e->getMessage()]);
            }

            return view('ingresses.index', ['conn_error' => $e->getMessage(), 'error_msg' => $errormsg]);
            
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
                'timeout' => 5
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

    public function store($namespace, IngressRequest $request): RedirectResponse
    {
        /*$formData = $request->validated();
        if ($formData["admin-mac"] != null )
            $formData["auto-mac"] = "false";

        if (is_null($formData["ageing-time"]))
            unset($formData["ageing-time"]);

        if (is_null($formData["mtu"]))
            unset($formData["mtu"]);

        if (is_null($formData["admin-mac"]))
            unset($formData["admin-mac"]);

        if (isset($formData["dhcp-snooping"]))
            $formData["dhcp-snooping"] = "true";

        $jsonData = json_encode($formData);

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

            $response = $client->post("/api/v1/namespaces", [
                'timeout' => 5
            ]);

            $response = $client->request('PUT', $device['method'] . "://" . $device['endpoint'] . "/rest/interface/bridge", [
                'auth' => [$device['username'], $device['password']],
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $jsonData,
            ]);

            return redirect()->route('Bridges.index', $device['id'])->with('success-msg', "A Bridge interface was added with success");
        } catch (\Exception $e) {
            $error = $this->treat_error($e->getMessage());

            if ($error == null)
                dd($e->getMessage());

            return redirect()->back()->withInput()->with('error-msg', $error);
        }*/
        return redirect()->back();
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
    
            $response = $client->delete("/apis/networking.k8s.io/v1/namespaces/$namespace/ingresses/$id");

            return redirect()->route('Ingresses.index')->with('success-msg', "Ingress '$id' was deleted with success");
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
