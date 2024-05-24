@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Ingress</h4>
            <p class="card-description">
                Here you can add a new Ingress
            </p>
            <form method="POST" action="{{route('Ingresses.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-ingress">
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
            <div class="form-group form-inline">
                <label class="col-sm-12 col-form-label">Rules *</label>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-dark" onClick="appendInput('rules','rules[]')">+ Add Rule</button>
                </div>
                @error("rules")
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="row" id="rules">
                @if(old('rules'))
                    <script>let ruleCount = 0;</script>
                    @foreach(old('rules') as $index => $key)
                    <div class="col-md-6 mb-4 dynamic-input">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Rule #{{$index}} Details</h5>
                                <hr>
                                <div class="form-group">
                                    <label class="col-form-label">Host name</label>
                                    <input type="text" name="rules[{{$index}}][host]" class="form-control @error("rules.$index.host") is-invalid @enderror" value="{{isset($key['host']) ? $key['host'] : ""}}" placeholder="example.com">
                                    @error("rules.$index.host")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <h6>Paths *</h6>
                                    <button type="button" class="btn btn-dark" onclick="addPath({{$index}})">+ Add Path</button>
                                    @error("rules.$index.path")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div id="paths-{{$index}}">
                                        @if(old("rules.$index.path.pathName"))
                                            @foreach(old("rules.$index.path.pathName") as $indexPath => $keyPath)
                                            <div class="input-group mb-3 dynamic-input">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Path</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("rules.$index.path.pathName.$indexPath") is-invalid @enderror" name="rules[{{$index}}][path][pathName][{{$indexPath}}]" placeholder="/nginx" value="{{$keyPath}}">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Type</span>
                                                    </div>
                                                    <select class="form-select fix-height @error("rules.$index.path.pathType.$indexPath") is-invalid @enderror" name="rules[{{$index}}][path][pathType][{{$indexPath}}]">
                                                        <option value="Prefix" {{ old("rules.$index.path.pathType.$indexPath") == "Prefix" ? 'selected' : '' }}>Prefix</option> 
                                                        <option value="Exact" {{ old("rules.$index.path.pathType.$indexPath") == "Exact" ? 'selected' : '' }}>Exact</option> 
                                                        <option value="ImplementationSpecific" {{ old("rules.$index.path.pathType.$indexPath") == "ImplementationSpecific" ? 'selected' : '' }}>ImplementationSpecific</option> 
                                                    </select>
                                                    @error("rules.$index.path.pathName.$indexPath")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @error("rules.$index.path.pathType.$indexPath")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Service</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("rules.$index.path.serviceName.$indexPath") is-invalid @enderror" name="rules[{{$index}}][path][serviceName][{{$indexPath}}]" placeholder="example-service" value="{{old("rules.$index.path.serviceName.$indexPath")}}">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Port</span>
                                                    </div>
                                                    <input type="text" class="form-control @error("rules.$index.path.portNumber.$indexPath") is-invalid @enderror" name="rules[{{$index}}][path][portNumber][{{$indexPath}}]" placeholder="80" value="{{old("rules.$index.path.portNumber.$indexPath")}}">
                                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
                                                    @error("rules.$index.path.serviceName.$indexPath")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @error("rules.$index.path.portNumber.$indexPath")
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger removeInput mt-3"><i class="ti-trash removeInput"></i> Remove Rule</button>
                            </div>
                        </div>
                    </div>
                    <script>ruleCount = {{$index}};</script>
                    @endforeach
                @endif
            </div>
            <div class="form-group">    
                <label>Default Backend</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Service Name</span>
                    </div>
                    <input type="text" class="form-control fix-height @error('defaultBackendName') is-invalid @enderror" name="defaultBackendName" placeholder="my-service" value="{{old('defaultBackendName')}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Port</span>
                    </div>
                    <input type="text" name="defaultBackendPort" class="form-control @error('defaultBackendPort') is-invalid @enderror" placeholder="80" value="{{old('defaultBackendPort')}}">
                    @error("defaultBackendName")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("defaultBackendPort")
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