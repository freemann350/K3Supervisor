@extends('template.layout')
@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new User</h4>
            <p class="card-description">
                Here you can add add a new user
            </p>
            <form method="POST" action="{{route('Users.store')}}" enctype="multipart/form-data">
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
                        <option value="U">User</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Resources</label>
                <div class="col-sm-12">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="namespaces" value="true">
                        Namespaces
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="pods" value="true">
                        Pods
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="deployments" value="true">
                        Deployments
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="services" value="true">
                        Services
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="ingresses" value="true">
                        Ingresses
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="customresources" value="true">
                        Custom Resources
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="backups" value="true">
                        Backups
                        <i class="input-helper"></i></label>
                    </div>
                </div>
                <label class="col-sm-3 col-form-label">Verbs</label>
                <div class="col-sm-12">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="create" value="true">
                        Create
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="delete" value="true">
                        Delete
                        <i class="input-helper"></i></label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection