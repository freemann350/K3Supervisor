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
        return view('Users.create');
    }

    public function store(UserRequest $request)
    {
        $formData = $request->validated();

        User::create([
            'name' => $formData['name'],
            'password' => $formData['password'],
            'email' => $formData['email'],
            'role' => $formData['role'],
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
        $user->update($formData);
        
        $name = $formData['name'];
        return redirect()->route('Users.index')->withInput()->with('success-msg', "$name was updated with success");
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
        
        $name=$user->name;
        
        $user->delete();

        return redirect()->route('Users.index')->with('success-msg', "$name was deleted with success");
    }
}
