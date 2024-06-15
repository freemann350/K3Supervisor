<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterConnectionException;
use App\Exceptions\ClusterException;
use Illuminate\Http\Request;
use App\Http\Requests\BackupRequest;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use App\Models\Cluster;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
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
        return view('backups.index');
    }

    public function store(BackupRequest $request)
    {
        $resources = $request->validated();
        
        $resourcesList = [];
        foreach ($resources as $key => $value) {
            if ($key != 'excludeDefaultResources' && $key != 'excludeDeploymentPods')
            array_push($resourcesList,$key);
        }

        if (count($resourcesList) == 0) {
            $errormsg['message'] = 'You need at least one resource to make a backup';
            $errormsg['status'] = 'Bad Request';
            $errormsg['code'] = '400';
            return redirect()->back()->with('error_msg', $errormsg);
        }


        $options['excludeDefaultResources'] = isset($resources['excludeDefaultResources']);
        $options['excludeDeploymentPods'] = isset($resources['excludeDeploymentPods']);

        $filePath = $this->createBackup($resourcesList, $options);
        
        if ($filePath == -1) {
            $errormsg['message'] = 'Could create the requested backup';
            $errormsg['status'] = 'Internal Server Error';
            $errormsg['code'] = '500';

            return redirect()->back()->with('error_msg', $errormsg);
        } else if ($filePath == -2) {
            $errormsg['message'] = 'The resources requested have no data to backup';
            $errormsg['status'] = 'Unprocessable Content';
            $errormsg['code'] = '422';
            
            return redirect()->back()->with('error_msg', $errormsg);
        }

    
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function createBackup($resources, $options)
    {

        $endpoints = [];
        foreach ($resources as $resource) {
            switch ($resource) {
                case 'namespaces':
                    $endpoints['namespaces'] = '/api/v1/namespaces';
                    break;
                case 'pods':
                    $endpoints['pods'] = '/api/v1/pods';
                    break;
                case 'deployments':
                    $endpoints['deployments'] = '/apis/apps/v1/deployments';
                    break;
                case 'services':
                    $endpoints['services'] = '/api/v1/services';
                    break;
                case 'ingresses':
                    $endpoints['ingresses'] = '/apis/networking.k8s.io/v1/ingresses';
                    break;
                
            }
        }

        $client = new Client([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Authorization' => $this->token,
                'Accept' => 'application/json',
            ],
            'verify' => false,
            'timeout' => $this->timeout
        ]);

        $curTime = date('d-m-Y_H-i-s');
        $tempDir = storage_path("app/k8s_backups/$curTime");
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $promises = [];
        foreach ($endpoints as $resource => $endpoint) {
            $promises[$resource] = $client->getAsync($endpoint);
        }

        $results = Promise\Utils::settle($promises)->wait();

        $itemCount = 0;
        foreach ($results as $resource => $result) {
            if ($result['state'] === 'fulfilled') {
                try {
                    $data = json_decode($result['value']->getBody(), true);
                    $itemCount += count($data['items']);

                    if (count($data['items']) == 0) {
                        continue;
                   }
                    mkdir("$tempDir/$resource", 0755, true);

                    $resourceList = $data['items'];
                    foreach ($resourceList as $resourceData) {
                        
                        if ($options['excludeDefaultResources']) {
                            if ((isset($resourceData['metadata']['name']) && preg_match('/^kube-/', $resourceData['metadata']['name'])) || (isset($resourceData['metadata']['namespace']) && preg_match('/^kube-/', $resourceData['metadata']['namespace']))) {
                                continue;
                            }
                        }

                        if ($options['excludeDeploymentPods'] && $resource == 'pods') {
                            $deploymentPod = false;
                            if (isset($resourceData['metadata']['ownerReferences'])) {
                                foreach ($resourceData['metadata']['ownerReferences'] as $ownerReferences) {
                                    if (isset($ownerReferences['kind']) && $ownerReferences['kind'] == 'ReplicaSet') {
                                        $deploymentPod = true;
                                    }
                                    break;
                                }
                            }
                            if ($deploymentPod)
                                continue;
                        }
                        unset($resourceData['metadata']['managedFields']);
                        unset($resourceData['metadata']['uid']);
                        unset($resourceData['metadata']['resourceVersion']);
                        unset($resourceData['metadata']['creationTimestamp']);
                        
                        $dataFile = json_encode($resourceData, JSON_PRETTY_PRINT);
                        $fileName = $resourceData['metadata']['name'].".json";
                        file_put_contents("$tempDir/$resource/$fileName", $dataFile);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => "Failed to process $resource: " . $e->getMessage()], 500);
                }
            } else {
                
                return response()->json(['error' => "Failed to fetch $resource"], 500);
            }
        }

        if ($itemCount == 0) {
            $this->deleteDirectory($tempDir);

            return -2;
        } 

        $zip = new ZipArchive;
        $zipFileName = session('clusterName') . '_backup_' . $curTime . '.zip';
        $zipFilePath = storage_path('app/') . $zipFileName;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create zip file'], 500);
        }


        $this->deleteDirectory($tempDir);
        $filePath = storage_path('app/') . $zipFileName;

        if (file_exists($filePath)) {
            return $filePath;
        } else {
            return -1;
        }
    }

    private function deleteDirectory($dir) 
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        
        return rmdir($dir);
    }

}
