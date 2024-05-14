<?php

namespace App\Http\Controllers;

use App\Http\Requests\NamespaceRequest;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;

class NamespaceController extends Controller
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

            $response = $client->get("/api/v1/namespaces");
            
            $jsonData = json_decode($response->getBody(), true);

            $namespaces = [];
            if ($request->query('showDefault') == "true") {
                foreach ($jsonData['items'] as $jsonData) {
                    $data['name'] =  $jsonData['metadata']['name'];
                    $data['creation'] =  $jsonData['metadata']['creationTimestamp'];
                    $data['status'] =  $jsonData['status']['phase'];
                    $namespaces[] = $data;
                }
            } else {
                foreach ($jsonData['items'] as $jsonData) {
                    if (!preg_match('/^kube-/', $jsonData['metadata']['name'])) {
                        $data['name'] =  $jsonData['metadata']['name'];
                        $data['creation'] =  $jsonData['metadata']['creationTimestamp'];
                        $data['status'] =  $jsonData['status']['phase'];
                        $namespaces[] = $data;
                    }
                }
            }
            

            return view('namespaces.index', ['namespaces' => $namespaces]);
        } catch (\Exception $e) {
            return view('namespaces.index', ['conn_error' => $e->getMessage()]);
        }
    }

    public function show($id): View
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

            $response = $client->get("/api/v1/namespaces/" . $id);

            $data = json_decode($response->getBody(), true);

            return view('namespaces.show', ['namespace' => $data]);
        } catch (\Exception $e) {
            return view('namespaces.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("namespaces.create");
    }

    public function store(NamespaceRequest $request): RedirectResponse
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

    public function destroy($id) 
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
    
            $response = $client->delete("/api/v1/namespaces/" . $id);

            return redirect()->route('Namespaces.index')->with('success-msg', "Namespace '$id' was deleted with success");
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
