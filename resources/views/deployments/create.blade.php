@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Deployment</h4>
            <p class="card-description">
                Here you can add a new Deployment
            </p>
            <form method="POST" action="{{route('Deployments.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-deployment">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Namespace *</label>
                <div class="col-sm-12">
                    <input type="text" name="namespace" class="form-control @error('namespace') is-invalid @enderror" value="{{old('namespace')}}" placeholder="my-namespace">
                    @error('namespace')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Replicas *</label>
                <div class="col-sm-12">
                    <input type="text" name="replicas" class="form-control @error('replicas') is-invalid @enderror" value="{{old('replicas')}}" placeholder="3">
                    @error('replicas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Label Matching *</label>
                <div class="col-sm-12" id="matchLabels">
                    <button type="button" class="btn btn-dark" onClick="appendInput('matchLabels', 'matchLabels[]')">+ Add Label Matching</button>
                    @error('key_matchLabels')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @if(old('key_matchLabels') || old('value_matchLabels'))
                        @foreach(old('key_matchLabels') as $index => $key)
                            <div class="input-group mb-3 dynamic-input">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("key_matchLabels.{$index}") is-invalid @enderror" name="key_matchLabels[]" value="{{ $key }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Value</span>
                                </div>
                                <input type="text" class="form-control @error("key_matchLabels.{$index}") is-invalid @enderror" name="value_matchLabels[]" value="{{ old('value_matchLabels')[$index] }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash dynamic-input"></i></button>
                                </div>
                                @error("key_matchLabels.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                @error("value_matchLabels.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @include("template/resource_creation/infoCreation")
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Template Labels *</label>
                <div class="col-sm-12" id="templateLabels">
                    <button type="button" class="btn btn-dark" onClick="appendInput('templateLabels', 'templateLabels[]')">+ Add Template Label</button>
                    @error('key_templateLabels')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @if(old('key_templateLabels'))
                        @foreach(old('key_templateLabels') as $index => $key)
                            <div class="input-group mb-3 dynamic-input">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("key_templateLabels.{$index}") is-invalid @enderror" name="key_templateLabels[]" value="{{ $key }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Value</span>
                                </div>
                                <input type="text" class="form-control @error("key_templateLabels.{$index}") is-invalid @enderror" name="value_templateLabels[]" value="{{ old('value_templateLabels')[$index] }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash dynamic-input"></i></button>
                                </div>
                                @error("key_templateLabels.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                @error("value_templateLabels.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Containers *</label>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-dark" onClick="appendInput('containers', 'containers[]')">+ Add Container</button>
                </div>
                @error('containers')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="row" id="containers">
                @if(old('containers'))
                    <script>let containerCount = 0;</script>
                    @foreach(old('containers') as $index => $key)
                    <div class="col-md-6 mb-4 dynamic-input">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Container #{{$index}} Details</h5>
                                <hr>
                                <div class="form-group">
                                    <label class="col-form-label">Container name *</label>
                                    <input type="text" name="containers[{{$index}}][name]" class="form-control @error("containers.$index.name") is-invalid @enderror" value="{{ isset($key['name']) ? $key['name'] : ''}}" placeholder="my-container">
                                </div>
                                @error("containers.$index.name")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-group">
                                    <label class="col-form-label">Container image *</label>
                                    <input type="text" name="containers[{{$index}}][image]" class="form-control @error("containers.$index.image") is-invalid @enderror" value="{{ isset($key['image']) ? $key['image'] : ''}}" placeholder="my-image">
                                </div>
                                @error("containers.$index.image")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div>
                                    <h6>Ports</h6>
                                    <button type="button" class="btn btn-dark" onclick="addPort({{$index}})">Add Port</button>
                                    <div id="ports-{{$index}}">
                                        @if(old("containers.$index.ports"))
                                            @foreach(old("containers.$index.ports") as $indexPort => $keyPort)
                                            <div class="input-group mb-3 dynamic-input">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Port</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("containers.$index.ports.$indexPort") is-invalid @enderror" name="containers[{{$index}}][ports][{{$indexPort}}]" value="{{ isset($keyPort) ? $keyPort : ''}}" placeholder="80">
                                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
                                                </div>
                                                @error("containers.$index.ports.$indexPort")
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <h6>Environment Variables</h6>
                                    <button type="button" class="btn btn-dark" onclick="addEnv({{$index}})">Add Environment Variable</button>
                                    <div id="env-{{$index}}">
                                        @if(old("containers.$index.env.key") && old("containers.$index.env.value"))
                                            @foreach(old("containers.$index.env.key") as $indexEnv => $keyEnv)
                                            <div class="input-group mb-3 dynamic-input">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Key</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("containers.$index.env.key.$indexEnv") is-invalid @enderror" name="containers[{{$index}}][env][key][{{$indexEnv}}]" value="{{ old("containers.$index.env.key.$indexEnv") }}" placeholder="Key">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Value</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("containers.$index.env.value.$indexEnv") is-invalid @enderror" name="containers[{{$index}}][env][value][{{$indexEnv}}]" value="{{ old("containers.$index.env.value.$indexEnv") }}" placeholder="Value">
                                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
                                                </div>
                                                @error("containers.$index.env.key.$indexEnv")
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                @error("containers.$index.env.value.$indexEnv")
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger removeInput mt-3"><i class="ti-trash removeInput"></i> Remove Container</button>
                            </div>
                        </div>
                    </div>
                    <script>containerCount = {{$index}};</script>
                    @endforeach
                @endif
            </div>
            <div class="form-group">
                <label for="updateStrategyType">Update Strategy Type</label>
                <div class="col-sm-12">
                    <select id="strategy" class="form-select" name="strategy" onchange="handleStrategyChange()">
                        <option {{old('strategy') == "Auto" ? 'selected' : ''}} value="Auto">Auto</option> 
                        <option {{old('strategy') == "RollingUpdate" ? 'selected' : ''}} value="RollingUpdate">RollingUpdate</option> 
                        <option {{old('strategy') == "Recreate" ? 'selected' : ''}} value="Recreate">Recreate</option> 
                    </select>
                    @error('strategy')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div id="strategyParameters">
                @if (old('strategy') == 'RollingUpdate')
                <div class="form-group">    
                    <label for="maxUnavailable">Max Unavailable *</label>
                    <input type="text" id="maxUnavailable" name="maxUnavailable" class="form-control @error('maxUnavailable') is-invalid @enderror" placeholder="1" value="{{old('maxUnavailable')}}">
                    @error('maxUnavailable')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">    
                    <label for="maxSurge">Max Surge *</label>
                    <input type="text" id="maxSurge" name="maxSurge" class="form-control @error('maxSurge') is-invalid @enderror" placeholder="1" value="{{old('maxSurge')}}">
                    @error('maxSurge')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Minimum Ready Time (seconds)</label>
                <div class="col-sm-12">
                    <input type="text" name="minReadySeconds" class="form-control @error('minReadySeconds') is-invalid @enderror" value="{{old('minReadySeconds')}}" placeholder="30">
                    @error('minReadySeconds')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Revision History Limit</label>
                <div class="col-sm-12">
                    <input type="text" name="revisionHistoryLimit" class="form-control @error('revisionHistoryLimit') is-invalid @enderror" value="{{old('revisionHistoryLimit')}}" placeholder="5">
                    @error('revisionHistoryLimit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Progress Deadline (seconds)</label>
                <div class="col-sm-12">
                    <input type="text" name="progressDeadlineSeconds" class="form-control @error('progressDeadlineSeconds') is-invalid @enderror" value="{{old('progressDeadlineSeconds')}}" placeholder="600">
                    @error('progressDeadlineSeconds')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection