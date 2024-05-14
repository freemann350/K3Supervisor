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

    public function store($namespace, DeploymentRequest $request): RedirectResponse
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
