@extends('template.layout')
@section('main-content')
<br>
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card h-100 d-flex flex-column">
            <div class="card-header">
                Nodes
            </div>
            <div class="card-body flex-grow-1">
                <div class="row row-cols-1 row-cols-md-3 justify-content-center">
                    @foreach ($nodes as $node)
                    <div class="col mb-4">
                        <div class="card h-100">
                            <?php
                                switch(true) {
                                    case(preg_match('/\bdebian\b/i', $node['os'])):
                                        $img = url('img/logos/debian.png');
                                        break;
                                    case(preg_match('/\brocky\b/i', $node['os'])):
                                        $img = url('img/logos/rocky.png');
                                        break;
                                    default:
                                        $img = url('img/logos/k8s.png');
                                }
                            ?>
                            <div class="card-body">
                                <h5 class="card-title text-center">{{$node['name']}} {{$node['master'] ? '(master)' : ''}}</h5>
                                <img style="display: block;margin-left: auto;margin-right: auto;" src="{{$img}}" width="70" height="70" alt="Card image cap">
                                <br>
                                <p class="card-text text-center">{{$node['os']}}</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><p class="text-center"><b>{{$node['ip']}}</b></p></li>
                                <li class="list-group-item"><p class="text-center"><b>{{$node['cpus']}} CPUs ({{$node['arch']}})</b></p></li>
                                <li class="list-group-item"><p class="text-center"><b>~{{$node['memory']}}GBs</b></p></li>
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                Total Resources
            </div>
            <div class="card-body">
                <canvas id="totalResources"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Recent events</h4>
            @if (!isset($conn_error))
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" style="text-align:center" id="dt">
                    <thead>
                    <tr>
                        <th>Kind</th>
                        <th>Object</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th style="text-align:left">Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($events != null)
                    @foreach ($events as $event)
                    <tr>
                        <td>{{$event['kind']}}</td>
                        <td>{{$event['name'] . '@' .$event['namespace']}}</td>
                        <td>{{$event['type']}}</td>
                        @if($event['time'] != null)
                        <td>{{$event['time']}}</td>
                        @else    
                        <td style="text-align:left">From:{{$event['startTime']}}<br>To: {{$event['endTime']}}</td>
                        @endif
                        <td style="text-align:justify">{{$event['message']}}</td>
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