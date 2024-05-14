@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Static Route</h4>
            <p class="card-description">
                Here you can add a new Static Route
            </p>
            <form method="POST" action="{{route('Namespaces.create')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-namespace">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Labels (optional)</label>
                <div class="col-sm-12" id="labels">
                <button type="button" class="btn btn-dark" onClick="appendInput('labels','labels[]','Label')">+ Add Label</button>
                    @error('label[]')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-12 col-form-label">Annotations (optional)</label>
                <div class="col-sm-12" id="annotations">
                <button type="button" class="btn btn-dark" onClick="appendInput('annotations','annotations[]','Annotation')">+ Add Annotation</button>
                    @error('annotations[]')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-12 col-form-label">Finalizers (optional)</label>
                <div class="col-sm-12" id="finalizers">
                <button type="button" class="btn btn-dark" onClick="appendInput('finalizers','finalizers[]','Finalizer')">+ Add Finalizer</button>
                    @error('finalizers[]')
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