<?php

namespace App\Http\Controllers;

use App\Http\Requests\PodRequest;
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

            $response = $client->get("/api/v1/pods");

            $jsonData = json_decode($response->getBody(), true);
            
            $pods = [];
            if ($request->query('showDefault') == "true") {
                foreach ($jsonData['items'] as $jsonData) {
                    $data['name'] =  $jsonData['metadata']['name'];
                    $data['namespace'] =  $jsonData['metadata']['namespace'];
                    $data['podIP'] =  isset($jsonData['status']['podIP']) ? $jsonData['status']['podIP'] : "-";
                    $data['totalContainers'] = isset($jsonData['status']['containerStatuses']) ? count($jsonData['status']['containerStatuses']) : '';
                    $data['status'] =  $jsonData['status']['phase'];
    
                    $pods[] = $data;
                }
            } else {
                foreach ($jsonData['items'] as $jsonData) {
                    if (!preg_match('/^kube-/', $jsonData['metadata']['namespace'])) {
                        $data['name'] =  $jsonData['metadata']['name'];
                        $data['namespace'] =  $jsonData['metadata']['namespace'];
                        $data['podIP'] =  isset($jsonData['status']['podIP']) ? $jsonData['status']['podIP'] : "-";
                        $data['totalContainers'] = isset($jsonData['status']['containerStatuses']) ? count($jsonData['status']['containerStatuses']) : '';
                        $data['status'] =  $jsonData['status']['phase'];
        
                        $pods[] = $data;
                    }
                }
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

    public function store(PodRequest $request): RedirectResponse
    {
        $formData = $request->validated();
        dd($formData);
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
        
        $data['spec']['restartPolicy'] = $formData['restartpolicy'];
        if (isset($formData['graceperiod'])) {
            $data['spec']['terminationGracePeriodSeconds'] = $formData['graceperiod'];
        }
        
        $jsonData = json_encode($data);
        dd($jsonData);

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

            $response = $client->post("/api/v1//namespaces/".$formData['namespace']."/pods");

            return redirect()->route('Namespaces.index')->with('success-msg', "Pod '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
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
