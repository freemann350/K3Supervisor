@extends('template.layout')
@section('main-content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Namespaces</h4>
            <p class="card-description">
                List of all <b>Namespaces</b> on the cluster
            </p>
            @if (!isset($conn_error))
            <div class="table-responsive">
            <table class="table table-hover table-striped" style="text-align:center" id="dt">
                <thead>
                <tr>
                    <th>Hostname</th>
                    <th>Creation Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($namespaces as $namespace)                    
                <tr>
                    <td>{{$namespace['name']}}</td>
                    <td>{{$namespace['creation']}}</td>
                    @if (isset($namespace['status']) && $namespace['status'] == "Active")
                    <td><label class="badge badge-success"> {{$namespace['status']}} </label></td>
                    @elseif (isset($namespace['status']) && $namespace['status'] == "Terminating")
                    <td><label class="badge badge-warning"> {{$namespace['status']}} </label></td>
                    @else
                    <td><label class="badge badge-danger"> {{$namespace['status']}} </label></td>
                    @endif
                    <td>
                        <a class="btn btn-outline-info btn-fw btn-rounded btn-sm"  href="{{route('Namespaces.show',$namespace['name'])}}"><i class="mdi mdi-information-outline"></i></a>
                        @if (!preg_match('/^kube-/', $namespace['name']))
                        <a class="btn btn-outline-danger btn-fw btn-rounded btn-sm" href="#" onclick="_delete('Are you sure you want to delete the Namespace &quot;{{$namespace["name"]}}?','{{ route("Namespaces.destroy", $namespace['name']) }}')"><i class="mdi mdi-trash-can-outline"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
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