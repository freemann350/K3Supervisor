<?php

namespace App\Http\Controllers;

use App\Exceptions\ClusterException;
use App\Models\Cluster;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
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
        return view('dashboard.index');
    }
}
