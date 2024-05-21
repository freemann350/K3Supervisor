@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Pod</h4>
            <p class="card-description">
                Here you can add a new Pod
            </p>
            <form method="POST" action="{{route('Pods.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-pod">
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
            @include("template/resource_creation/infoCreation")
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
                <label class="col-sm-3 col-form-label">Volume type</label>
                <div class="col-sm-12">
                    <select class="form-select" name="volumetype[]">
                        <option>secret</option> 
                        <option>persistentVolumeClaim</option> 
                        <option>hostPath</option> 
                        <option>nfs</option> 
                        <option>emptyDir</option> 
                        <option>configMap</option> 
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Restart Policy *</label>
                <div class="col-sm-12">
                    <select class="form-select" name="restartpolicy">
                        <option {{old('restartpolicy') == "Always" ? 'selected' : ''}}>Always</option> 
                        <option {{old('restartpolicy') == "OnFailure" ? 'selected' : ''}}>OnFailure</option> 
                        <option {{old('restartpolicy') == "Never" ? 'selected' : ''}}>Never</option> 
                    </select>
                    @error('restartpolicy')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Termination Grace Period (seconds)</label>
                <div class="col-sm-12">
                    <input type="text" name="graceperiod" class="form-control @error('graceperiod') is-invalid @enderror" value="{{old('graceperiod')}}" placeholder="30">
                    @error('graceperiod')
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