<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        // مدیریت نقش‌ها به‌صورت زنده توسط کامپوننت Livewire (roles.role-manager) انجام می‌شود.
        return view('roles.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => 'required|max:255',
            'name' => 'required|unique:roles,name',
            'description' => 'nullable|max:500',
        ]);

        Role::create($data);

        return redirect()->route('roles.index')->with('success', 'نقش با موفقیت ایجاد شد.');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => 'required|max:255',
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|max:500',
        ]);

        $role->update($data);

        return redirect()->route('roles.index')->with('success', 'نقش با موفقیت ویرایش شد.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')->with('error', 'این نقش به یک یا چند کاربر اختصاص داده شده و قابل حذف نیست.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'نقش با موفقیت حذف شد.');
    }

    public function permissions(Role $role)
    {
        // ویرایش مجوزها به‌صورت زنده توسط کامپوننت Livewire (roles.role-permission-manager) انجام می‌شود.
        return view('roles.permissions', compact('role'));
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $permissionIds = $request->input('permissions', []);

        $role->permissions()->sync($permissionIds);

        return redirect()
            ->route('roles.permissions', $role)
            ->with('success', 'مجوزهای نقش با موفقیت ذخیره شدند.');
    }
}