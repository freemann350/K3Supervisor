@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Create new Backup</h4>
            <p class="card-description">
                Here you can create Backups of specific resources
            </p>
            <form method="POST" action="{{route('Backups.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Resources to save</label>
                <div class="col-sm-12">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="namespaces" value="true">
                        Namespaces
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="pods" value="true">
                        Pods
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="deployments" value="true">
                        Deployments
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="services" value="true">
                        Services
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="ingresses" value="true">
                        Ingresses
                        <i class="input-helper"></i></label>
                    </div>
                </div>
                <label class="col-sm-3 col-form-label">Options</label>
                <div class="col-sm-12">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="excludeDefaultResources" value="true">
                        Exclude default resources
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="excludeDeploymentPods" value="true">
                        Exclude deployment Pods (only works for Pod backups)
                        <i class="input-helper"></i></label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection