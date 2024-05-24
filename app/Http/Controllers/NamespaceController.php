<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterException;
use App\Http\Requests\NamespaceRequest;
use App\Models\Cluster;
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
                'timeout' => $this->timeout
            ]);

            $response = $client->get("/api/v1/namespaces/" . $id);
            
            $jsonData = json_decode($response->getBody(), true);
            unset($jsonData['metadata']['managedFields']);
            $json = json_encode($jsonData, JSON_PRETTY_PRINT);
            
            $data = [];
            // METADATA
            $data['name'] = isset($jsonData['metadata']['name']) ? $jsonData['metadata']['name'] : '';
            $data['uid'] = isset($jsonData['metadata']['uid']) ? $jsonData['metadata']['uid'] : '';
            $data['creationTimestamp'] = isset($jsonData['metadata']['creationTimestamp']) ? $jsonData['metadata']['creationTimestamp'] : '';
            $data['labels'] = isset($jsonData['metadata']['labels']) ? $jsonData['metadata']['labels'] : '';
            $data['annotations'] = isset($jsonData['metadata']['annotations']) ? $jsonData['metadata']['annotations'] : null;

            // SPEC (FINALIZERS)
            $data['finalizers'] = isset($jsonData['spec']['finalizers']) ? $jsonData['spec']['finalizers'] : '';
            
            // STATUS
            $data['status'] = isset($jsonData['status']['phase']) ? $jsonData['status']['phase'] : 'Unkown';
            
            return view('namespaces.show', ['namespace' => $data, 'json' => $json]);
        } catch (\Exception $e) {
            dd($e->getMessage());
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
                'timeout' => $this->timeout
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

    public function destroy($id): RedirectResponse
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
