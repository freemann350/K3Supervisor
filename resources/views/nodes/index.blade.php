@extends('template.layout')
@section('main-content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Nodes</h4>
            <p class="card-description">
                List of all <b>Nodes</b> on the cluster
            </p>
            @if (!isset($conn_error))
            <div class="table-responsive">
            <table class="table table-hover table-striped" style="text-align:center" id="dt">
                <thead>
                <tr>
                    <th>Hostname</th>
                    <th>Role</th>
                    <th>Architecture</th>
                    <th>OS</th>
                    <th>Instance Type</th>
                    <th>Pod CIDR</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @if (count($nodes) > 0)
                @foreach ($nodes as $node)                    
                <tr>
                    <td>{{$node['hostname']}}</td>
                    <td>{{ $node['master'] == "true" ? "Master" : "Worker" }}</td>
                    <td>{{$node['arch']}}</td>
                    <td>{{$node['os']}}</td>
                    <td>{{$node['instance']}}</td>
                    <td>
                        @foreach ($node['podCIDRs'] as $podCIDR)
                            {{ $podCIDR . " " }}
                        @endforeach
                    </td>
                    @if (isset($node['status']) && $node['status'] == "True")
                    <td class="text-success"> Ready <i class="ti-arrow-up"></i></td>
                    @else
                    <td class="text-danger"> Not ready <i class="ti-arrow-down"></i></td>
                    @endif
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