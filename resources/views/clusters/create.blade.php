@extends('template.layout')
@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Cluster</h4>
            <p class="card-description">
                Here you can add a new Kubernetes Cluster
            </p>
            <form method="POST" action="{{route('Clusters.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="My Cluster">
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
                    <input type="text" name="endpoint" class="form-control @error('endpoint') is-invalid @enderror" value="{{old('endpoint') ?? 'https://my-cluster:6443'}}" placeholder="https://127.0.0.1:6443">
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
                        <option value="P" {{old('token') == 'P' ? 'selected' : ''}}>Proxy (No auth)</option>
                        <option value="T" {{old('token') == 'T' ? 'selected' : ''}}>Token (Auth)</option>
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
                    <input type="text" name="token" class="form-control @error('token') is-invalid @enderror" value="{{old('token')}}">
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
                    <input type="text" name="timeout" class="form-control @error('timeout') is-invalid @enderror" value="{{old('timeout')}}" placeholder="5">
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
@endsection