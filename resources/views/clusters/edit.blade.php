@extends('template.layout')
@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Update Cluster</h4>
            <p class="card-description">
                Here you can update a Kubernetes Cluster
                <br>
                If you want to use a Token, check the documentation guide to <a href="https://kubernetes.io/docs/tasks/administer-cluster/access-cluster-api/?amp;amp#without-kubectl-proxy">Access Clusters Using the Kubernetes API</a>.
            </p>
            <form method="POST" action="{{route('Clusters.update',$cluster['id'])}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$cluster['name']}}" placeholder="My Cluster">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Endpoint *</label>
                <div class="col-sm-12">
                    <input type="text" name="endpoint" class="form-control @error('endpoint') is-invalid @enderror" value="{{$cluster['endpoint']}}" placeholder="https://127.0.0.1:6443">
                    @error('endpoint')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Auth Type *</label>
                <div class="col-sm-12">
                    <select class="form-select" name="auth_type">
                        <option value="P" {{$cluster['auth_type'] == 'P' ? 'selected' : ''}}>Proxy (No auth)</option>
                        <option value="T" {{$cluster['auth_type'] == 'T' ? 'selected' : ''}}>Token (Auth)</option>
                    </select>
                    @error('auth_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Token</label>
                <div class="col-sm-12">
                    <input type="text" name="token" class="form-control @error('token') is-invalid @enderror" value="{{$cluster['token']}}">
                    @error('token')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Timeout</label>
                <div class="col-sm-12">
                    <input type="text" name="timeout" class="form-control @error('timeout') is-invalid @enderror" value="{{$cluster['timeout']}}" placeholder="5">
                    @error('timeout')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
<form method="POST" action="{{route ('Clusters.destroy',$cluster['id'])}}" enctype="multipart/form-data">
@csrf
@method('DELETE')
    <div class="d-grid gap-2">
        <a href="#deletion" id="deletion" class="btn btn-danger btn-lg btn-block" onclick="_delete('Are you sure you want to delete this Cluster?','{{ route("Clusters.destroy", $cluster["id"]) }}')"><i class="mdi mdi-trash-can-outline"></i> Delete this Cluster</a>
    </div>
</form>
@endsection