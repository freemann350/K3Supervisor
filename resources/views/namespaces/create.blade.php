@extends('template.layout')
@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new Namespace</h4>
            <p class="card-description">
                Here you can add a new Namespace
            </p>
            <form method="POST" action="{{route('Namespaces.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="my-namespace">
                    @error("name")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            @include("template/resource_creation/infoCreation")
            <div class="form-group form-inline">
                <label class="col-sm-12 col-form-label">Finalizers</label>
                <div class="col-sm-12" id="finalizers">
                <button type="button" class="btn btn-dark" onClick="appendInput('finalizers','finalizers[]')">+ Add Finalizer</button>
                    @if (old('finalizers'))
                        @foreach (old('finalizers') as $index => $finalizer)
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Key</span>
                                </div>
                                <input type="text" class="form-control @error("finalizers.{$index}") is-invalid @enderror" name="finalizers[]" value="{{ $finalizer }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
                                </div>
                                @error("finalizers.{$index}")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection