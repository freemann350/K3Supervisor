@extends('template.layout')
@section('main-content')
@if ($clusters == null)
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-header">
            <h5 class="display-5">List of all your Clusters</h5>
        </div>
        <div class="card-body">
            <h4 class="card-title"><strong>You don't have any devices yet</h4>
            <p class="card-description">
                Try adding one using the button below
            </p>
        </div>
    </div>
</div>
@else
<br>
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="display-5">List of all your Clusters</h5>
        </div>
        <div class="card-body">
            <div class="row">
            @foreach ($clusters as $cluster)
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><strong>{{$cluster['name']}}</strong>'s Info </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                    <p class="fw-bold">
                                        Auth Type
                                    </p>
                                    <p>
                                        {{$cluster['auth_type'] == 'T' ? 'Token' : 'Proxy' }}
                                    </p>
                                    <p class="fw-bold">
                                        Communication timeout:
                                    </p>
                                    <p>
                                        {{$cluster['timeout']}} seconds
                                    </p>
                                    </address>
                                </div>
                                <div class="col-md-6">
                                @if ($cluster['online'])
                                    <address>
                                        <p class="fw-bold">
                                            Status
                                        </p>
                                        <p class="mb-2 text-success">
                                            Online
                                        </p>
                                    </address>
                                    <a class="btn {{ session('clusterId') == $cluster['id'] ? 'btn-dark' : 'btn-outline-dark'}} btn-lg btn-block" href="{{ route ('Clusters.selectCluster', $cluster['id']) }}">{{ session('clusterId') == $cluster['id'] ? 'Current Cluster' : 'Use this device'}}</a><br>
                                    <a class="btn btn-outline-dark btn-lg btn-block" href="{{ route ('Clusters.edit', $cluster['id']) }}">Edit this device</a>
                                @else
                                    <address>
                                        <p class="fw-bold">
                                            Status
                                        </p>
                                        <p class="mb-2 text-danger">
                                            Unreachable
                                        </p>
                                    </address>
                                    <a class="btn btn-outline-dark btn-lg btn-block" href="{{ route ('Clusters.edit', $cluster['id']) }}">Edit this device</a>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endif
<div class="d-grid gap-2">
  <a class="btn btn-success btn-lg btn-block" href="{{ route ('Clusters.create') }}"><i class="mdi mdi-plus-circle"></i> Add new Cluster</a>
</div>
@endsection