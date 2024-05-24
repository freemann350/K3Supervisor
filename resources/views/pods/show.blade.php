@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Pod "{{$pod['name']}}" Info</h4>
            <p class="card-description">
                Shows all information about the Pod {{$pod['name']}}
            </p>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Main Info</h4>
                        <p class="mb-2"><b><u>Name:</u></b> {{ isset($pod['name']) ? $pod['name'] : '-'}}</p>
                        <p class="mb-2"><b><u>Namespace:</u></b> {{ isset($pod['namespace']) ? $pod['namespace'] : '-'}}</p>
                        <p class="mb-2"><b><u>UID:</u></b> {{ isset($pod['uid']) ? $pod['uid'] : '-'}}</p>
                        <p class="mb-2"><b><u>Creation Date:</u></b> {{ isset($pod['creationTimestamp']) ? $pod['creationTimestamp'] : '-'}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Labels</h4>
                        @if (isset($pod['labels']))
                        @foreach ($pod['labels'] as $key => $label)
                            <p class="mb-2"><b><u>{{$key}}</u></b>: {{$label}}</p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no Labels on this resource</p>
                        @endif
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Annotations</h4>
                        @if (isset($pod['annotations']))
                        @foreach ($pod['annotations'] as $key => $annotation)
                            <p class="mb-2"><b><u>{{$key}}</u></b>: {{$annotation}}</p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no Annotations on this resource</p>
                        @endif
                    </address>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Common container info</h4>
                            <p class="mb-2"><b><u>Restart Policy</u></b>: {{$pod['restartPolicy']}}</p>
                            <p class="mb-2"><b><u>Termination Grace Period</u></b>: {{$pod['terminationGracePeriodSeconds']}} seconds</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                    <h4 class="card-title">Status</h4>
                    <p class="mb-2"><b><u>Phase</u></b>: {{$pod['status']}}</p>
                    <p class="mb-2"><b><u>Host</u></b>: {{$pod['nodeName']}} ({{$pod['hostIp']}})</p>
                    <p class="mb-2"><b><u>Pod IP</u></b>: {{$pod['podIp']}}</p>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    @foreach ($pod['containers'] as $key => $container)        
    <div class="col-md-6 mb-4">
        <div class="card h-100 d-flex flex-column">
            <div class="card-header">
                Container #{{$key+1}}
            </div>
            <div class="card-body flex-grow-1">
                <div class="row">
                    <div class="col">
                        <address>
                            <h4 class="card-title">Main Info</h4>
                            <p class="mb-2"><b><u>Name:</u></b> {{ isset($container['name']) ? $container['name'] : '-'}}</p>
                            <p class="mb-2"><b><u>Image:</u></b> {{ isset($container['image']) ? $container['image'] : '-'}}</p>
                        </address>
                    </div>
                    <div class="col">
                        <address>
                            <h4 class="card-title">Ports</h4>
                            @if (isset($container['ports']))
                            @foreach ($container['ports'] as $keyPort => $port)
                                <p class="mb-2">:{{ isset($port['containerPort']) ? $port['containerPort'] : '-'}}/{{ isset($port['protocol']) ? $port['protocol'] : '-'}}</p>
                            
                            @endforeach
                            @else
                                <p class="mb-2">There are no Ports on this resource</p>
                            @endif
                        </address>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <address>
                            <h4 class="card-title">Environment Variables</h4>
                            @if (isset($container['env']))
                            @foreach ($container['env'] as $keyEnv => $env)
                                <p class="mb-2"><b><u>{{ $env['name'] }}:</u></b> {{ isset($env['value']) ? $env['value'] : '-' }}</p>
                            @endforeach
                            @else
                                <p class="mb-2">There are no Environment Variables on this resource</p>
                            @endif
                        </address>
                    </div>
                    <div class="col-md-6">
                        <address>
                            <h4 class="card-title">Volumes</h4>
                            @if (isset($container['volumeMounts']))
                            @foreach ($container['volumeMounts'] as $keyVolume => $volume)
                            <p class="mb-2"><b><u>{{isset($volume['readOnly']) && $volume['readOnly'] == true ? "(RO)" : ''}} {{ isset($volume['name']) ? $volume['name'] : '-'}}</u></b> : {{ isset($volume['mountPath']) ? $volume['mountPath'] : '-'}}</p>
                            @endforeach
                            @else
                            <p class="mb-2">There are no mapped Volumes on this resource</p>
                            @endif
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            @if ($pod != null)
            <div class="col-md-12">
                <h4 class="card-title">Pod {{$pod['name']}}'s JSON Data</h4>
                <p class="card-description">
                    Shows all information of the Pod "{{$pod['name']}}", in an unformatted manner
                </p>
                <pre id="json">{{$json}}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection