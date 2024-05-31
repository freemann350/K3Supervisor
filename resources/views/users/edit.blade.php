@extends('template.layout')

@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit User</h4>
            <p class="card-description">
                Here you can add edit a user
            </p>
            <form method="POST" action="{{route('Users.update',$user['id'])}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$user['name']}}" placeholder="user" required>
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
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{$user['email']}}" placeholder="user@example.com" required>
                    @error('email')
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
                        <option value="A" {{$user['role'] =='A' ? 'selected' : ""}}>Admin</option>
                        <option value="U" {{$user['role'] =='U' ? 'selected' : ""}}>User</option>
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
                        <input type="checkbox" class="form-check-input" name="namespaces" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Namespaces") ? 'checked' : ''}}>
                        Namespaces
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="pods" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Pods") ? 'checked' : ''}}>
                        Pods
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="deployments" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Deployments") ? 'checked' : ''}}>
                        Deployments
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="services" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Services") ? 'checked' : ''}}>
                        Services
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="ingresses" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Ingresses") ? 'checked' : ''}}>
                        Ingresses
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="customresources" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"CustomResources") ? 'checked' : ''}}>
                        Custom Resources
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="backups" value="true" {{$user['resources'] == '[*]' || str_contains($user['resources'],"Backups") ? 'checked' : ''}}>
                        Backups
                        <i class="input-helper"></i></label>
                    </div>
                </div>
                <label class="col-sm-3 col-form-label">Verbs</label>
                <div class="col-sm-12">
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="create" value="true" {{$user['verbs'] == '[*]' || str_contains($user['verbs'],"Create") ? 'checked' : ''}}>
                        Create
                        <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="delete" value="true" {{$user['verbs'] == '[*]' || str_contains($user['verbs'],"Delete") ? 'checked' : ''}}>
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