@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Ingress "{{$ingress['name']}}" Info</h4>
            <p class="card-description">
                Shows all information about the Ingress {{$ingress['name']}}
            </p>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Main Info</h4>
                        <p class="mb-2"><b><u>Name:</u></b> {{ isset($ingress['name']) ? $ingress['name'] : '-'}}</p>
                        <p class="mb-2"><b><u>Namespace:</u></b> {{ isset($ingress['namespace']) ? $ingress['namespace'] : '-'}}</p>
                        <p class="mb-2"><b><u>UID:</u></b> {{ isset($ingress['uid']) ? $ingress['uid'] : '-'}}</p>
                        <p class="mb-2"><b><u>Creation Date:</u></b> {{ isset($ingress['creationTimestamp']) ? $ingress['creationTimestamp'] : '-'}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Labels</h4>
                        @if (isset($ingress['labels']))
                        @foreach ($ingress['labels'] as $key => $label)
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
                        @if (isset($ingress['annotations']))
                        @foreach ($ingress['annotations'] as $key => $annotation)
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
                        <h4 class="card-title">Extra Service info</h4>
                        <p class="mb-2"><b><u>Default Backend Name</u></b>: {{ $ingress['defaultBackendName'] }}</p>
                        <p class="mb-2"><b><u>Default Backend Port</u></b>: {{ $ingress['defaultBackendPort'] }}</p>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    @foreach ($ingress['rules'] as $keyRule => $rule)      
    <div class="col-md-6 mb-4">
        <div class="card h-100 d-flex flex-column">
            <div class="card-header">
                Rule #{{$keyRule+1}}
            </div>
            <div class="card-body flex-grow-1">
                @if (isset($rule['http']['paths']))
                @foreach ($rule['http']['paths'] as $keyPath => $path)
                @if ($keyPath > 0)
                    <hr>
                @endif
                <div class="row">
                    <div class="col">
                        <address>
                            <h4 class="card-title">Path #{{$keyPath + 1}}</h4>
                            <p class="mb-2"><b><u>Path:</u></b> {{ isset($path['path']) ? $path['path'] : '-'}}</p>
                            <p class="mb-2"><b><u>Type:</u></b> {{ isset($path['pathType']) ? $path['pathType'] : '-'}}</p>
                            <p class="mb-2"><b><u>Service:</u></b> {{ isset($path['backend']['service']['name']) ? $path['backend']['service']['name'] : '-'}}</p>
                            <p class="mb-2"><b><u>Port:</u></b> {{ isset($path['backend']['service']['port']['number']) ? $path['backend']['service']['port']['number'] : '-'}}</p>
                        </address>
                    </div>
                </div>
                @endforeach
                @else
                <p class="mb-2">There are no Paths on this resource</p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            @if ($ingress != null)
            <div class="col-md-12">
                <h4 class="card-title">Ingress {{$ingress['name']}}'s JSON Data</h4>
                <p class="card-description">
                    Shows all information of the Ingress "{{$ingress['name']}}", in an unformatted manner
                </p>
                <pre id="json">{{$json}}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection