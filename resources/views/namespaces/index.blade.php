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
            <hr>
            <form method="GET">
                <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="showDefault" value="true" {{app('request')->input('showDefault')!=null ? "checked" : ""}}>
                    Show default Namespaces
                    <i class="input-helper"></i></label>
                </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
            <hr>
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
                @if (count($namespaces) > 0)
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
                        @if ((Auth::user()->resources == '[*]' || str_contains(Auth::user()->resources,'Namespaces')) && (Auth::user()->verbs == '[*]' || str_contains(Auth::user()->verbs,'Delete')) )
                        @if (!preg_match('/^kube-/', $namespace['name']))
                        <a class="btn btn-outline-danger btn-fw btn-rounded btn-sm" href="#" onclick="_delete('Are you sure you want to delete the Namespace &quot;{{$namespace["name"]}}?','{{ route("Namespaces.destroy", $namespace['name']) }}')"><i class="mdi mdi-trash-can-outline"></i></a>
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
@if (isset($namespaces))
@if ((Auth::user()->resources == '[*]' || str_contains(Auth::user()->resources,'Namespaces')) && (Auth::user()->verbs == '[*]' || str_contains(Auth::user()->verbs,'Create')) )
<div class="d-grid gap-2">
  <a class="btn btn-success btn-lg btn-block" href="{{ route('Namespaces.create') }}"><i class="mdi mdi-plus-circle"></i> Add new Namespace</a>
</div>
@endif
@endif
@endsection