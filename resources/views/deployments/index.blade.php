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
            <h5>Filters</h5>
            <form method="GET">
            <div class="form-group">
                <br>
                <div class="col-sm-3 form-check-inline">
                    <input class="form-check-input" type="checkbox" name="showDefault" value="true" {{app('request')->input('showDefault')!=null ? "checked" : ""}}>
                    <label class="form-check-label"> &nbsp;Show Deployments from default Namespaces</label>
                </div>
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
                        <a class="btn btn-outline-info btn-fw btn-rounded btn-sm"  href="{{route('Deployments.show',$deployment['name'])}}"><i class="mdi mdi-information-outline"></i></a>
                        @if (!preg_match('/^kube-/', $deployment['namespace']))
                        <a class="btn btn-outline-danger btn-fw btn-rounded btn-sm" href="#" onclick="_delete('Are you sure you want to delete the Deployment &quot;{{$deployment["name"]}}?','{{ route("Deployments.destroy", [$deployment['namespace'], $deployment['name']]) }}')"><i class="mdi mdi-trash-can-outline"></i></a>
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
@endsection