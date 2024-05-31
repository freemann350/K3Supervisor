<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterConnectionException;
use App\Exceptions\ClusterException;
use App\Http\Requests\CustomResourceRequest;
use Illuminate\Http\Request;
use App\Models\Cluster;
use GuzzleHttp\Client;

class CustomResourceController extends Controller
{
    private $endpoint;
    private $token;
    private $timeout;

    public function __construct()
    {
        if (!session('clusterId')) 
            throw new ClusterException();

        $cluster = Cluster::findOrFail(session('clusterId'));

        if ($this->checkConnection($cluster) == -1)
            throw new ClusterConnectionException();

        $this->endpoint = $cluster['endpoint'];
        $this->token = "Bearer " . $cluster['token'];
        $this->timeout  = $cluster['timeout'];
    }
    
    private function checkConnection($cluster) 
    {
        
        try {
            if ($cluster['auth_type'] == 'P') {
                $client = new Client([
                    'base_uri' => $cluster['endpoint'],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'verify' => false,
                    'timeout' => 0.5
                ]);     
            } else {
                $client = new Client([
                    'base_uri' => $cluster['endpoint'],
                    'headers' => [
                        'Authorization' => "Bearer ". $cluster['token'],
                        'Accept' => 'application/json',
                    ],
                    'verify' => false,
                    'timeout' => 0.5
                ]);
            }
            $response = $client->get("/api/v1");
            $online = $response->getStatusCode();
        } catch (\Exception $e) {
            $online = -1;
        }

        return $online;
    }

    public function index()
    {
        return view('customresource.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomResourceRequest $request)
    {
        $formData = $request->validated();
        $data = json_decode($formData['resource'], true);

        if ($data == null) {
            $errormsg['message'] = "Could not parse JSON, syntax error";
            $errormsg['status'] = "Bad Request";
            $errormsg['code'] = "400";

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        }

        if (!isset($data['kind'])) {
            $errormsg['message'] = "Resource kind not found";
            $errormsg['status'] = "Bad Request";
            $errormsg['code'] = "400";

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        }

        if (!isset($data['metadata']['namespace'])) {
            $endpoint = $this->getKubernetesEndpoint($data['kind']);
        } else {
            $endpoint = $this->getKubernetesEndpoint($data['kind'],$data['metadata']['namespace']);
        }

        if ($endpoint == -1) {
            $errormsg['message'] = "Could not find the correct endpoint for the specified resource";
            $errormsg['status'] = "Unprocessable Content";
            $errormsg['code'] = "422";

            return redirect()->back()->withInput()->with('error_msg', $errormsg);
        }

        try {
    
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

            $response = $client->post($endpoint);

            if (isset($data['metadata']['namespace']) && isset($data['metadata']['name'])) {
                $successMessage = $data['kind'] ." '".$data['metadata']['name']."' was added with success on Namespace '". $data['metadata']['namespace']."'";
            } else if (!isset($data['metadata']['namespace'])){
                $successMessage = $data['kind'] ." '".$data['metadata']['name']."' was added with success";
            } else {
                $successMessage = $data['kind'] ." was added with success";
            }

            return redirect()->route('CustomResource.index')->withInput()->with('success-msg', $successMessage);
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

    function getKubernetesEndpoint($resourceKind, $namespace = null) {
        switch ($resourceKind) {
            // Core API (v1)
            case 'Pod':
                return "/api/v1/namespaces/{$namespace}/pods";
            case 'Service':
                return "/api/v1/namespaces/{$namespace}/services";
            case 'ConfigMap':
                return "/api/v1/namespaces/{$namespace}/configmaps";
            case 'Secret':
                return "/api/v1/namespaces/{$namespace}/secrets";
            case 'PersistentVolume':
                return "/api/v1/persistentvolumes";
            case 'PersistentVolumeClaim':
                return "/api/v1/namespaces/{$namespace}/persistentvolumeclaims";
            case 'Namespace':
                return "/api/v1/namespaces";
            case 'Event':
                return "/api/v1/namespaces/{$namespace}/events";
            case 'Endpoint':
                return "/api/v1/namespaces/{$namespace}/endpoints";
            case 'ReplicationController':
                return "/api/v1/namespaces/{$namespace}/replicationcontrollers";
            case 'ServiceAccount':
                return "/api/v1/namespaces/{$namespace}/serviceaccounts";
            case 'ResourceQuota':
                return "/api/v1/namespaces/{$namespace}/resourcequotas";
            case 'LimitRange':
                return "/api/v1/namespaces/{$namespace}/limitranges";
            case 'PodTemplate':
                return "/api/v1/namespaces/{$namespace}/podtemplates";
            
            // Apps API (apps/v1)
            case 'Deployment':
                return "/apis/apps/v1/namespaces/{$namespace}/deployments";
            case 'StatefulSet':
                return "/apis/apps/v1/namespaces/{$namespace}/statefulsets";
            case 'DaemonSet':
                return "/apis/apps/v1/namespaces/{$namespace}/daemonsets";
            case 'ReplicaSet':
                return "/apis/apps/v1/namespaces/{$namespace}/replicasets";
    
            // Batch API (batch/v1)
            case 'Job':
                return "/apis/batch/v1/namespaces/{$namespace}/jobs";
            case 'CronJob':
                return "/apis/batch/v1/namespaces/{$namespace}/cronjobs";
    
            // Autoscaling API (autoscaling/v1, autoscaling/v2)
            case 'HorizontalPodAutoscaler':
                return "/apis/autoscaling/v1/namespaces/{$namespace}/horizontalpodautoscalers";
    
            // Networking API (networking.k8s.io/v1)
            case 'Ingress':
                return "/apis/networking.k8s.io/v1/namespaces/{$namespace}/ingresses";
            case 'NetworkPolicy':
                return "/apis/networking.k8s.io/v1/namespaces/{$namespace}/networkpolicies";
    
            // Policy API (policy/v1)
            case 'PodDisruptionBudget':
                return "/apis/policy/v1/namespaces/{$namespace}/poddisruptionbudgets";
    
            // Custom Resource Definitions (CRDs)
            case 'CustomResourceDefinition':
                return "/apis/apiextensions.k8s.io/v1/customresourcedefinitions";
    
            // Storage API (storage.k8s.io/v1)
            case 'StorageClass':
                return "/apis/storage.k8s.io/v1/storageclasses";
            case 'VolumeAttachment':
                return "/apis/storage.k8s.io/v1/volumeattachments";
    
            // Other APIs
            case 'APIService':
                return "/apis/apiregistration.k8s.io/v1/apiservices";
            case 'CertificateSigningRequest':
                return "/apis/certificates.k8s.io/v1/certificatesigningrequests";
            case 'PodSecurityPolicy':
                return "/apis/policy/v1beta1/podsecuritypolicies";
            
            default:
                return -1;
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
