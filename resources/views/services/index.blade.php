@extends('template.layout')
@section('main-content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Services</h4>
            <p class="card-description">
                List of all <b>Services</b> on the cluster
            </p>
            @if (!isset($conn_error))
            <hr>
            <h5>Filters</h5>
            <form method="GET">
                <div class="form-check form-check-flat form-check-primary">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="showDefault" value="true" {{app('request')->input('showDefault')!=null ? "checked" : ""}}>
                    Show Services from default Namespaces
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
                    <th>Ports</th>
                    <th>Selector</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @if (count($services) > 0)
                @foreach ($services as $service)                    
                <tr>
                    <td>{{$service['name']}}</td>
                    <td>{{$service['namespace']}}</td>
                    <td>{{$service['ports']}}&nbsp;</td>
                    <td>
                    @if ($service['selector'] != '-')
                    @foreach ($service['selector'] as $key => $selector)
                        <b>{{$selector}}</b> <p class="text-muted">({{$key}})</p>
                    @endforeach
                    @else
                        -
                    @endif
                    </td>
                    <td>{{$service['type']}}</td>
                    <td>
                        <a class="btn btn-outline-info btn-fw btn-rounded btn-sm"  href="{{route('Services.show',$service['name'])}}"><i class="mdi mdi-information-outline"></i></a>
                        @if (!preg_match('/^kube-/', $service['namespace']))
                        <a class="btn btn-outline-danger btn-fw btn-rounded btn-sm" href="#" onclick="_delete('Are you sure you want to delete the Service &quot;{{$service["name"]}}?','{{ route("Services.destroy", [$service['namespace'], $service['name']]) }}')"><i class="mdi mdi-trash-can-outline"></i></a>
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
@if (isset($services))
<div class="d-grid gap-2">
  <a class="btn btn-success btn-lg btn-block" href="{{ route('Services.create') }}"><i class="mdi mdi-plus-circle"></i> Add new Service</a>
</div>
@endif
@endsection