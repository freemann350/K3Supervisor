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
        try {
            $formData = $request->validated();
            
            $data['apiVersion'] = "v1";
            $data['kind'] = "Namespace";
            $data['metadata']['name'] = $formData['name'];

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

            $finalizers = ['kubernetes'];
            if (isset($formData['finalizers'])) {
                foreach ($formData['finalizers'] as $key => $finalizer) {
                array_push($finalizers,$formData['finalizers'][$key]);
                }
            }   

            $data['spec']['finalizers'] = $finalizers;
            
            $jsonData = json_encode($data);

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

            $response = $client->post("/api/v1/namespaces");

            return redirect()->route('Namespaces.index')->with('success-msg', "Namespace '". $formData['name'] ."' was added with success");
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
