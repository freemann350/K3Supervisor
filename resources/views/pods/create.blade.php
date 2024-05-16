@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Static Route</h4>
            <p class="card-description">
                Here you can add a new Static Route
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
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Labels</label>
                <div class="col-sm-12" id="labels">
                    @if(old('key_labels'))
                        @foreach(old('key_labels') as $index => $key)
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("key_labels.{$index}") is-invalid @enderror" name="key_labels[]" value="{{ $key }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Value</span>
                                </div>
                                <input type="text" class="form-control @error("value_labels.{$index}") is-invalid @enderror" name="value_labels[]" value="{{ old('value_labels')[$index] }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <button type="button" class="btn btn-dark" onClick="appendInput('labels', 'labels[]')">+ Add Label</button>
                    @error('key_labels.*')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('value_labels.*')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Annotations</label>
                <div class="col-sm-12" id="annotations">
                    @if(old('key_annotations'))
                        @foreach(old('key_annotations') as $index => $key)
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("key_annotations.{$index}") is-invalid @enderror" name="key_annotations[]" value="{{ $key }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Value</span>
                                </div>
                                <input type="text" class="form-control @error("value_annotations.{$index}") is-invalid @enderror" name="value_annotations[]" value="{{ old('value_annotations')[$index] }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <button type="button" class="btn btn-dark" onClick="appendInput('annotations', 'annotations[]', 2)">+ Add Annotation</button>
                    @error('key_annotations.*')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('value_annotations.*')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Containers *</label>
                <div class="col-sm-12" id="containers">
                    <button type="button" class="btn btn-dark" onClick="appendInput('containers', 'containers[]')">+ Add Container</button>
                </div>
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
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
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
                    @error('name')
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