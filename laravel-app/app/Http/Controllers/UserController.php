<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::with('role')->latest()->paginate(15);

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|confirmed|min:6',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        User::create($data);

        return redirect()->route('users.index')->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    
    public function update(Request $request, User $user)
    {
        $data = $request->validate([

            'name' => 'required|max:255',

            'username' => 'required|unique:users,username,' . $user->id,

            'email' => 'nullable|email|unique:users,email,' . $user->id,

            'phone' => 'nullable|max:20',

            'role_id' => 'required|exists:roles,id',

        ]);

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('users.index')->with('success','کاربر با موفقیت ویرایش شد.');
    }

    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {

        return back()->with(
            'error',
            'امکان حذف کاربر وارد شده وجود ندارد.'
        );

    }

    $user->delete();

    return back()->with(
        'success',
        'کاربر با موفقیت حذف شد.'
    );
    }

    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate(['password' => 'required|confirmed|min:6',]);

        $user->update(['password' => $data['password'],]);

        return redirect()->route('users.index')->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}