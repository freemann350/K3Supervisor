@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Deployment "{{$deployment['name']}}" Info</h4>
            <p class="card-description">
                Shows all information about the Deployment {{$deployment['name']}}
            </p>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Main Info</h4>
                        <p class="mb-2"><b><u>Name:</u></b> {{ isset($deployment['name']) ? $deployment['name'] : '-'}}</p>
                        <p class="mb-2"><b><u>Namespace:</u></b> {{ isset($deployment['namespace']) ? $deployment['namespace'] : '-'}}</p>
                        <p class="mb-2"><b><u>UID:</u></b> {{ isset($deployment['uid']) ? $deployment['uid'] : '-'}}</p>
                        <p class="mb-2"><b><u>Creation Date:</u></b> {{ isset($deployment['creationTimestamp']) ? $deployment['creationTimestamp'] : '-'}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Labels</h4>
                        @if (isset($deployment['labels']))
                        @foreach ($deployment['labels'] as $key => $label)
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
                        @if (isset($deployment['annotations']))
                        @foreach ($deployment['annotations'] as $key => $annotation)
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
                        <h4 class="card-title">Extra Deployment info</h4>
                            <p class="mb-2"><b><u>Restart Policy</u></b>: {{$deployment['restartPolicy']}}</p>
                            <p class="mb-2"><b><u>Termination Grace Period</u></b>: {{$deployment['terminationGracePeriodSeconds']}} seconds</p>
                            <p class="mb-2"><b><u>Strategy</u></b>: {{$deployment['strategy']}}</p>
                            <ul>
                            @if ($deployment['rollingUpdate'] != null)
                            @foreach ($deployment['rollingUpdate'] as $key => $rollingUpdate)
                                <li><p class="mb-2"><b><u>{{$key}}</u></b>: {{$rollingUpdate}}</p></li>
                            @endforeach
                            @else
                                <li><p class="mb-2">There are no RollingUpdate on this resource</p></li>
                            @endif
                            </ul>
                            <p class="mb-2"><b><u>Match Labels (Selector)</u></b>:</p>
                            <ul>
                            @if ($deployment['selectorMatchLabels'] != null)
                            @foreach ($deployment['selectorMatchLabels'] as $key => $matchLabel)
                                <li><p class="mb-2"><b><u>{{$key}}</u></b>: {{$matchLabel}}</p></li>
                            @endforeach
                            @else
                                <li><p class="mb-2">There are no Match Labels on this resource</p></li>
                            @endif
                            </ul>
                        </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Common container info</h4>
                            <p class="mb-2"><b><u>Revision History Limit</u></b>: {{$deployment['revisionHistoryLimit']}}</p>
                            <p class="mb-2"><b><u>Progress Deadline Seconds</u></b>: {{$deployment['progressDeadlineSeconds']}} seconds</p>
                            <p class="mb-2"><b><u>Termination Grace Period</u></b>: {{$deployment['terminationGracePeriodSeconds']}} seconds</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                    <h4 class="card-title">Status</h4>
                    <p class="mb-2"><b><u>Replicas</u></b>: {{$deployment['replicas']}}</p>
                    <p class="mb-2"><b><u>Ready Replicas</u></b>: {{$deployment['readyReplicas']}} </p>
                    <p class="mb-2"><b><u>Available Replicas</u></b>: {{$deployment['availableReplicas']}}</p>
                    <p class="mb-2"><b><u>Updated Replicas</u></b>: {{$deployment['updatedReplicas']}}</p>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    @foreach ($deployment['containers'] as $key => $container)        
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
            @if ($deployment != null)
            <div class="col-md-12">
                <h4 class="card-title">Deployment {{$deployment['name']}}'s JSON Data</h4>
                <p class="card-description">
                    Shows all information of the Deployment "{{$deployment['name']}}", in an unformatted manner
                </p>
                <pre id="json">{{$json}}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection