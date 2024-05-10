@extends('template.layout')

@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new User</h4>
            <p class="card-description">
                Here you can add add a new user
            </p>
            <form method="POST" action="{{route('User.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="user" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-12">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}" placeholder="user@example.com" required>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-12">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}" required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-12">
                    <select class="form-select" name="role">
                        <option value="A">Admin</option>
                        <option value="L">Lecturer</option>
                        <option value="T">Trainee</option>
                    </select>
                    @error('role')
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