@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Create Resource using JSON (for advanced users only)</h4>
            <p class="card-description">
                Here you can make your own resource on the Cluster.
                <br>
                Check the <a href="https://kubernetes.io/docs/reference/generated/kubernetes-api/v1.26/#endpoints-v1-core">Kubernetes API Documentation</a> for the correct parameters
            </p>
            <form method="POST" action="{{route('CustomResources.store')}}">
            @csrf
            <div class="form-group">
                <textarea class="form-control" name="resource" id="resource">{{old('resource')}}</textarea>
                @error('resource')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <p class="btn btn-info btn-fw" onclick="prettyPrint()">Beautify JSON</p><br>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection