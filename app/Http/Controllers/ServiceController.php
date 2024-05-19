<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ServiceController extends Controller
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

            $response = $client->get("/api/v1/services");

            $jsonData = json_decode($response->getBody(), true);
            
            $services = [];
            if ($request->query('showDefault') == "true") {
                foreach ($jsonData['items'] as $jsonData) {
                    $data['name'] =  $jsonData['metadata']['name'];
                    $data['namespace'] =  $jsonData['metadata']['namespace'];
                    $data['ports'] =  count($jsonData['spec']['ports']);
                    $data['selector'] =  isset($jsonData['spec']['selector']) ? $jsonData['spec']['selector'] : "-";
                    $data['type'] =  $jsonData['spec']['type'];

                    $services[] = $data;
                }
            } else {
                foreach ($jsonData['items'] as $jsonData) {
                    if (!preg_match('/^kube-/', $jsonData['metadata']['namespace'])) {
                        $data['name'] =  $jsonData['metadata']['name'];
                        $data['namespace'] =  $jsonData['metadata']['namespace'];
                        $data['ports'] =  count($jsonData['spec']['ports']);
                        $data['selector'] =  isset($jsonData['spec']['selector']) ? $jsonData['spec']['selector'] : "-";
                        $data['type'] =  $jsonData['spec']['type'];

                        $services[] = $data;
                    }
                }
            }

            return view('services.index', ['services' => $services]);
        } catch (\Exception $e) {
            return view('services.index', ['conn_error' => $e->getMessage()]);
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

            $response = $client->get("/api/v1/namespaces/$namespace/services/$id");

            $data = json_decode($response->getBody(), true);

            return view('services.show', ['service' => $data]);
        } catch (\Exception $e) {
            return view('services.show', ['conn_error' => $e->getMessage()]);
        }
    }
    
    public function create(): View 
    {
        return view("services.create");
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $formData = $request->validated();
        
        //dd($formData);
        // MAIN INFO
        $data['apiVersion'] = "v1";
        $data['kind'] = "Service";
        $data['metadata']['name'] = $formData['name'];
        $data['metadata']['namespace'] = $formData['namespace'];

        // LABELS & ANNOTATIONS
        if (isset($formData['key_labels']) && isset($formData['value_labels'])) {
            foreach ($formData['key_labels'] as $key => $label) {
                $data['metadata']['labels'][$formData['key_labels'][$key]] = $formData['value_labels'][$key];
            }
        }

        if (isset($formData['key_annotations']) && isset($formData['value_annotations'])) {
            foreach ($formData['key_annotations'] as $key => $annotation) {
                $data['metadata']['annotations'][$formData['key_annotations'][$key]] = $formData['value_annotations'][$key];
            }
        }

        //SELECTOR
        if (isset($formData['key_selectorLabels']) && isset($formData['value_selectorLabels'])) {
            foreach ($formData['key_selectorLabels'] as $key => $selector) {
                $data['spec']['selector'][$formData['key_selectorLabels'][$key]] = $formData['value_selectorLabels'][$key];
            }
        }

        // PORTS
        $data['spec']['ports'] = [];
        if (isset($formData['portName']) && isset($formData['protocol']) && isset($formData['port']) && isset($formData['target']) && isset($formData['nodePort'])) {
            $arr_port = [];
            foreach ($formData['portName'] as $key => $port) {
                $arr_port['name'] = $formData['portName'][$key];
                $arr_port['protocol'] = $formData['protocol'][$key];
                $arr_port['port'] = intval($formData['port'][$key]);
                $arr_port['targetPort'] = intval($formData['target'][$key]);
                if ($formData['protocol'] != 'ClusterIP')
                    $arr_port['nodePort'] = intval($formData['nodePort'][$key]);

                array_push($data['spec']['ports'],$arr_port);
            }
        }
        

        // EXTRA INFO
        if (isset($formData['type'])  && $formData['type'] != "Auto") {
            $data['spec']['type'] = $formData['type'];
        }

        if (isset($formData['type']) && isset($formData['externalName'])) {
            $data['spec']['externalName'] = $formData['externalName'];
        }

        if (isset($formData['externalTrafficPolicy'])  && $formData['externalTrafficPolicy'] != "Auto") {
            $data['spec']['externalTrafficPolicy'] = $formData['externalTrafficPolicy'];
        }

        if (isset($formData['sessionAffinity']) && $formData['sessionAffinity'] != "Auto") {
            $data['spec']['sessionAffinity'] = $formData['sessionAffinity'];
        }

        if (isset($formData['sessionAffinity']) && isset($formData['sessionAffinity'])  && $formData['sessionAffinity'] != "Auto") {
            $data['spec']['sessionAffinityConfig']['clientIP']['timeoutSeconds'] = intval($formData['sessionAffinityTimeoutSeconds']);
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
                'timeout' => $this->timeout
            ]);

            $response = $client->post("/api/v1/namespaces/".$formData['namespace']."/services");

            return redirect()->route('Services.index')->with('success-msg', "Service '". $formData['name'] ."' was added with success on Namespace '". $formData['namespace']."'");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            dd($e->getResponse()->getBody()->getContents());
        } catch (\Exception $e) {
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
                'timeout' => $this->timeout
            ]);
    
            $response = $client->delete("/api/v1/namespaces/$namespace/services/$id");

            return redirect()->route('Services.index')->with('success-msg', "Service '$id' was deleted with success");
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
