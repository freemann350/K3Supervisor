@extends('template.layout')
@section('main-content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Deployments</h4>
            <p class="card-description">
                List of all <b>Deployments</b> on the cluster
            </p>
            @if (!isset($conn_error))
            <hr>
            <form method="GET">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Namespace</span>
                    </div>
                    <select class="form-select fix-height" name="showNamespaceData">
                        <option value="All" {{ (app('request')->input('showNamespaceData') == 'All' || app('request')->input('showNamespaceData') == null) ? "selected" : ""}}>All</option> 
                        @foreach ($namespaceList as $namespace)
                        <option value="{{ $namespace }}" {{app('request')->input('showNamespaceData') == $namespace ? "selected" : ""}}>{{ $namespace }}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="showDefault" value="true" {{app('request')->input('showDefault')!=null ? "checked" : ""}}>
                    Show Deployments from default Namespaces
                    <i class="input-helper"></i></label>
                </div>
                <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
            <hr>
            <div class="table-responsive">
            <table class="table table-hover table-striped" style="text-align:center" id="dt">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Namespace</th>
                    <th>Replicas</th>
                    <th>Total Containers</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @if (count($deployments) > 0)
                @foreach ($deployments as $deployment)                    
                <tr>
                    <td>{{$deployment['name']}}</td>
                    <td>{{$deployment['namespace']}}</td>
                    <td>{{$deployment['replicas']}}&nbsp;</td>
                    <td>{{$deployment['totalContainers']}}&nbsp;</td>
                    <td>
                        <a class="btn btn-outline-info btn-fw btn-rounded btn-sm"  href="{{route('Deployments.show',[$deployment['namespace'],$deployment['name']])}}"><i class="mdi mdi-information-outline"></i></a>
                        @if ((Auth::user()->resources == '[*]' || str_contains(Auth::user()->resources,'Deployments')) && (Auth::user()->verbs == '[*]' || str_contains(Auth::user()->verbs,'Delete')) )
                        @if (!preg_match('/^kube-/', $deployment['namespace']))
                        <a class="btn btn-outline-danger btn-fw btn-rounded btn-sm" href="#" onclick="_delete('Are you sure you want to delete the Deployment &quot;{{$deployment["name"]}}?','{{ route("Deployments.destroy", [$deployment['namespace'], $deployment['name']]) }}')"><i class="mdi mdi-trash-can-outline"></i></a>
                        @endif
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            </div>
            <br>
            <button onclick="location.reload();" type="button" class="btn btn-info btn-lg btn-block"><i class="mdi mdi-refresh"></i>Refresh info
            </button>
            @else
            <p>Could not load info.</p>
            <p>Error: <b>{{$conn_error}}</b></p>
            @endif
        </div>
    </div>
</div>
@if (isset($deployments))
@if ((Auth::user()->resources == '[*]' || str_contains(Auth::user()->resources,'Deployments')) && (Auth::user()->verbs == '[*]' || str_contains(Auth::user()->verbs,'Create')) )
<div class="d-grid gap-2">
  <a class="btn btn-success btn-lg btn-block" href="{{ route('Deployments.create') }}"><i class="mdi mdi-plus-circle"></i> Add new Deployment</a>
</div>
@endif
@endif
@endsection