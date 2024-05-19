@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Service</h4>
            <p class="card-description">
                Here you can add a new Service
            </p>
            <form method="POST" action="{{route('Services.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-service">
                    @error("name")
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
                    @error("namespace")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            @include("template/resource_creation/infoCreation")
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Selector Labels</label>
                <div class="col-sm-12" id="selectorLabels">
                    <button type="button" class="btn btn-dark" onClick="appendInput('selectorLabels', 'selectorLabels[]')">+ Add Selector Label</button>
                    @if(old('key_selectorLabels'))
                        @foreach(old('key_selectorLabels') as $index => $key)
                            <div class="input-group mb-3 dynamic-input">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("key_selectorLabels.{$index}") is-invalid @enderror fix-height" name="key_selectorLabels[]" value="{{ $key }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Value</span>
                                </div>
                                <input type="text" class="form-control @error("value_selectorLabels.{$index}") is-invalid @enderror fix-height" name="value_selectorLabels[]" value="{{ old('value_selectorLabels')[$index] }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger fix-height removeInput"><i class="ti-trash dynamic-input"></i></button>
                                </div>
                                @error("key_selectorLabels.{$index}")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                @error("value_selectorLabels.{$index}")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-12 col-form-label">Ports</label>
                <div class="col-sm-12" id="ports">
                <button type="button" class="btn btn-dark" onClick="appendInput('ports','ports[]')">+ Add Port</button>
                    @if (old('protocol'))
                    @foreach (old('protocol') as $index => $portData)
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Name</span>
                            </div>
                            <input type="text" class="form-control fix-height" name="portName[]" value="{{old("portName.$index")}}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Protocol</span>
                            </div>
                            <select class="form-select fix-height" name="protocol[]">
                                <option value="TCP" {{ $portData == "TCP" ? 'selected' : '' }}>TCP</option> 
                                <option value="UDP" {{ $portData == "UDP" ? 'selected' : '' }}>UDP</option> 
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Port</span>
                            </div>
                            <input type="text" class="form-control fix-height" name="port[]" value="{{old("port.$index")}}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Target</span>
                            </div>
                            <input type="text" class="form-control fix-height" name="target[]" value="{{old("target.$index")}}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Node</span>
                            </div>
                            <input type="text" class="form-control fix-height" name="nodePort[]" value="{{old("nodePort.$index")}}">
                            <button type="button" class="btn btn-danger removeInput fix-height"><i class="ti-trash removeInput"></i></button>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Type *</label>
                <select class="form-select fix-height" name="type" id="type" onchange="handleChange('type')">
                    <option value="Auto" {{old('type') == "Auto" ? 'selected' : ''}}>Auto</option> 
                    <option value="ClusterIP" {{old('type') == "ClusterIP" ? 'selected' : ''}}>ClusterIP</option> 
                    <option value="NodePort" {{old('type') == "NodePort" ? 'selected' : ''}}>NodePort</option> 
                    <option value="LoadBalancer" {{old('type') == "LoadBalancer" ? 'selected' : ''}}>LoadBalancer</option> 
                    <option value="ExternalName" {{old('type') == "ExternalName" ? 'selected' : ''}}>ExternalName</option> 
                </select>
            </div>
            <div id="typeParameter">
                @if (old('type') == 'ExternalName')
                <div class="form-group">    
                    <label>External Name *</label>
                    <input type="text" name="externalName" class="form-control" placeholder="my-name.domain.test" value="{{old('externalName')}}">
                </div>
                @error("externalName")
                        {{ $message }}
                @enderror
                @endif
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">External Traffic Policy *</label>
                <select class="form-select fix-height" name="externalTrafficPolicy">
                    <option value="Auto" {{old('externalTrafficPolicy') == "Auto" ? 'selected' : ''}}>Auto</option> 
                    <option value="Cluster" {{old('externalTrafficPolicy') == "Cluster" ? 'selected' : ''}}>Cluster</option> 
                    <option value="Local" {{old('externalTrafficPolicy') == "Local" ? 'selected' : ''}}>Local</option> 
                </select>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Session Affinity *</label>
                <select class="form-select fix-height" name="sessionAffinity" id="sessionAffinity" onchange="handleChange('sessionAffinity')">
                    <option value="Auto" {{old('sessionAffinity') == "Auto" ? 'selected' : ''}}>Auto</option> 
                    <option value="None" {{old('sessionAffinity') == "None" ? 'selected' : ''}}>None</option> 
                    <option value="ClientIP" {{old('sessionAffinity') == "ClientIP" ? 'selected' : ''}}>ClientIP</option> 
                </select>
            </div>
            <div id="sessionAffinityParameter">
                @if (old('sessionAffinityTimeoutSeconds'))
                <div class="form-group">    
                    <label>Session Affinity Timeout</label>
                    <input type="text" name="sessionAffinityTimeoutSeconds" class="form-control" placeholder="10800 " value="{{old('sessionAffinityTimeoutSeconds')}}">
                    @error("sessionAffinityTimeoutSeconds")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection