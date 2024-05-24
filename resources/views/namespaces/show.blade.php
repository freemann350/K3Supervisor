@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Namespace "{{$namespace['name']}}" Info</h4>
            <p class="card-description">
                Shows all information about the Namespace {{$namespace['name']}}
            </p>
            <div class="row">
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Main Info</h4>
                        <p class="mb-2"><b><u>Name:</u></b> {{ isset($namespace['name']) ? $namespace['name'] : '-'}}</p>
                        <p class="mb-2"><b><u>UID:</u></b> {{ isset($namespace['uid']) ? $namespace['uid'] : '-'}}</p>
                        <p class="mb-2"><b><u>Creation Date:</u></b> {{ isset($namespace['creationTimestamp']) ? $namespace['creationTimestamp'] : '-'}}</p>
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Labels</h4>
                        @if (isset($namespace['labels']))
                        @foreach ($namespace['labels'] as $key => $label)
                            <p class="mb-2"><b><u>{{$key}}</u></b>: {{$label}}</p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no labels on this resource</p>
                        @endif
                    </address>
                </div>
                <div class="col-md-4">
                    <address>
                        <h4 class="card-title">Annotations</h4>
                        @if (isset($namespace['annotations']))
                        @foreach ($namespace['annotations'] as $key => $annotation)
                            <p class="mb-2"><b><u>{{$key}}</u></b>: {{$annotation}}</p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no annotations on this resource</p>
                        @endif
                    </address>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <address>
                        <h4 class="card-title">Finalizers</h4>
                        @if (isset($namespace['finalizers']))
                        @foreach ($namespace['finalizers'] as $finalizer)
                            <p class="mb-2"><b><u>{{$finalizer}}</u></b></p>
                        @endforeach
                        @else
                            <p class="mb-2">There are no finalizers on this resource</p>
                        @endif
                    </address>
                </div>
                <div class="col-md-6">
                    <address>
                    <h4 class="card-title">Status</h4>
                    <p class="mb-2"><b><u>Phase</u></b>: {{$namespace['status']}}</p>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            @if ($namespace != null)
            <div class="col-md-12">
                <h4 class="card-title">Namespace {{$namespace['name']}}'s JSON Data</h4>
                <p class="card-description">
                    Shows all information of the Namespace "{{$namespace['name']}}", in an unformatted manner
                </p>
                <pre>{{$json}}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection