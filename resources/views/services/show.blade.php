@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Service "{{$service['name']}}" Info</h4>
            <p class="card-description">
                Shows all information about the Service {{$service['name']}}
            </p>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Main Info</h4>
                        <p class="mb-2"><b><u>Name:</u></b> {{ isset($service['name']) ? $service['name'] : '-'}}</p>
                        <p class="mb-2"><b><u>Namespace:</u></b> {{ isset($service['namespace']) ? $service['namespace'] : '-'}}</p>
                        <p class="mb-2"><b><u>UID:</u></b> {{ isset($service['uid']) ? $service['uid'] : '-'}}</p>
                        <p class="mb-2"><b><u>Creation Date:</u></b> {{ isset($service['creationTimestamp']) ? $service['creationTimestamp'] : '-'}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Labels</h4>
                        @if (isset($service['labels']))
                        @foreach ($service['labels'] as $key => $label)
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
                        @if (isset($service['annotations']))
                        @foreach ($service['annotations'] as $key => $annotation)
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
                        <p class="mb-2"><b><u>Type</u></b>: {{$service['type']}}</p>
                        <p class="mb-2"><b><u>Session Affinity</u></b>: {{$service['sessionAffinity']}}</p>
                        <p class="mb-2"><b><u>External Traffic Policy</u></b>: {{$service['externalTrafficPolicy']}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Ports</h4>
                            @if (isset($service['ports']))
                            @foreach ($service['ports'] as $keyPort => $port)
                                <p class="mb-2"><b><u>{{ isset($port['name']) ? $port['name'] : '-'}}</u></b>: {{ isset($port['port']) ? $port['port'] : '-'}}:{{ isset($port['targetPort']) ? $port['targetPort'] : '-'}}/{{ isset($port['protocol']) ? $port['protocol'] : '-'}}->{{ isset($port['nodePort']) ? $port['nodePort'] : '-'}}</p>
                            @endforeach
                            @else
                                <p class="mb-2">There are no Ports on this resource</p>
                            @endif
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                    <h4 class="card-title">Selectors</h4>
                    @if (isset($service['selector']))
                        @foreach ($service['selector'] as $keySelector => $selector)
                            <p class="mb-2"><b><u>{{$keySelector}}</u></b>: {{$selector}}</p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no Selectors on this resource</p>
                        @endif
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            @if ($service != null)
            <div class="col-md-12">
                <h4 class="card-title">Service {{$service['name']}}'s JSON Data</h4>
                <p class="card-description">
                    Shows all information of the Service "{{$service['name']}}", in an unformatted manner
                </p>
                <pre id="json">{{$json}}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection