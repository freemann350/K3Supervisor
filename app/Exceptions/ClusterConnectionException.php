<?php

namespace App\Exceptions;

use Exception;

class ClusterConnectionException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $errormsg['message'] = "There was an error connecting to the cluster '". session('clusterName')."'. Please try again later.";
        $errormsg['status'] = "Service Unavailable";
        $errormsg['code'] = "503";

        session()->forget('clusterId');
        session()->forget('clusterName');
        $redirect = redirect()->route('Clusters.index')->with('error_msg', $errormsg);

        return response($redirect, 302);
    }
}
