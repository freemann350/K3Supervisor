<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserSelfUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\View\View;
use App\Models\User;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::all();
        return view('users.index', ['users' =>$users]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $formData = $request->validated();
        
        $resources = $this->setResources($formData);
        $verbs = $this->setVerbs($formData);
        
        User::create([
            'name' => $formData['name'],
            'password' => $formData['password'],
            'email' => $formData['email'],
            'role' => $formData['role'],
            'resources' => $resourcesString,
            'verbs' => $verbsString,
        ]);

        return redirect()->route('Users.index')->with('success-msg', "An User was added with success");
    }

    public function edit($id): View
    {
        $user = User::findOrFail($id);
        return view('users.edit', ['user' => $user]);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $formData = $request->validated();

        $user = User::findOrFail($id);

        $resources = $this->setResources($formData);
        $verbs = $this->setVerbs($formData);
        
        $user->name = $formData['name'];
        $user->email = $formData['email'];
        $user->role = $formData['role'];
        $user->resources = $resources;
        $user->verbs = $verbs;
        
        $user->save();

        return redirect()->route('Users.index')->withInput()->with('success-msg', $formData['name'] ." was updated with success");
    }

    public function editMe(): View
    {
        $user = User::findOrFail(auth()->user()->id);

        return view('users.editMe', ['user' => $user]);
    }

    public function updateMe(UserSelfUpdateRequest $request)
    {
        $formData = $request->validated();
        $user = User::findOrFail(auth()->user()->id);
        $user->update($formData);
        
        return redirect()->back()->withInput()->with('success-msg', "Your information was updated with success");
    }

    public function updatePassword(PasswordRequest $request, $id)
    {
        $formData = $request->validated();

        $user = User::findOrFail($id);
        $user->update($formData);

        return redirect()->back()->withInput()->with('success-msg', "Your password was updated with success");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        $user->delete();

        return redirect()->route('Users.index')->with('success-msg', "$user->name; was deleted with success");
    }

    private function setResources($formData) {
        $resources = [];

        if (isset($formData['namespaces'])) {
            $resources[] = 'Namespaces';
        }
        if (isset($formData['pods'])) {
            $resources[] = 'Pods';
        }
        if (isset($formData['deployments'])) {
            $resources[] = 'Deployments';
        }
        if (isset($formData['services'])) {
            $resources[] = 'Services';
        }
        if (isset($formData['ingresses'])) {
            $resources[] = 'Ingresses';
        }
        if (isset($formData['customresources'])) {
            $resources[] = 'CustomResources';
        }
        if (isset($formData['backups'])) {
            $resources[] = 'Backups';
        }
                
        if (count($resources) === 7) {
            $resourcesString = '[*]';
        } else {
            $resourcesString = '[' . implode(',', $resources) . ']';
        }

        return $resourcesString;
    }

    private function setVerbs($formData) {
        $verbs = [];

        if (isset($formData['create'])) {
            $verbs[] = 'Create';
        }
        if (isset($formData['delete'])) {
            $verbs[] = 'Delete';
        }

        if (count($verbs) === 2) {
            $verbsString = '[*]';
        } else {
            $verbsString = '[' . implode(',', $verbs) . ']';
        }

        return $verbsString;
    }
}
