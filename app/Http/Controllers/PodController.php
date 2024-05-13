<?php

namespace App\Http\Controllers;

use App\Http\Requests\PodRequest;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;

class PodController extends Controller
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
                'timeout' => 5
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

            return view('pods.index', ['pods' => $pods]);
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
                'timeout' => 5
            ]);

            $response = $client->get("/api/v1/namespaces/$namespace/pods/$id");

            $data = json_decode($response->getBody(), true);

            return view('pods.show', ['pod' => $data]);
        } catch (\Exception $e) {
            return view('pods.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("pods.create");
    }

    public function store($namespace, PodRequest $request): RedirectResponse
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
    
            $response = $client->delete("/api/v1/namespaces/$namespace/pods/$id");

            return redirect()->route('Pods.index')->with('success-msg', "Pod '$id' was deleted with success");
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
        dd($errorMessage);
        return $error;
    }
}
