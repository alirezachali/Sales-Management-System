<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $username): User
    {
        $role = Role::create(['name' => $username . '-role', 'display_name' => 'نقش تست']);

        return User::create([
            'name'      => 'کاربر ' . $username,
            'username'  => $username,
            'email'     => $username . '@example.com',
            'phone'     => '09120000000',
            'password'  => 'secret-password',
            'role_id'   => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_profile_page_is_shown_for_owner(): void
    {
        $user = $this->makeUser('ali');

        $this->actingAs($user)
            ->get(route('profile.show', 'ali'))
            ->assertOk()
            ->assertSee('مشخصات کاربر');
    }

    public function test_other_user_cannot_see_profile_without_permission(): void
    {
        $owner = $this->makeUser('owner');
        $stranger = $this->makeUser('stranger');

        $this->actingAs($stranger)
            ->get(route('profile.show', 'owner'))
            ->assertForbidden();

        unset($owner);
    }

    public function test_user_with_users_view_permission_can_see_profile(): void
    {
        $owner = $this->makeUser('owner2');

        $viewerRole = Role::create(['name' => 'viewer', 'display_name' => 'بیننده']);
        $viewer = $this->makeUser('viewer1');
        $viewer->role_id = $viewerRole->id;
        $viewer->save();

        $group = \App\Models\PermissionGroup::create(['name' => 'کاربران']);
        $permission = \App\Models\Permission::create([
            'permission_group_id' => $group->id,
            'name'                => 'users.view',
            'display_name'        => 'مشاهده کاربران',
        ]);
        $viewerRole->permissions()->attach($permission->id);

        $this->actingAs($viewer->fresh())
            ->get(route('profile.show', 'owner2'))
            ->assertOk();
    }

    public function test_owner_can_update_profile_fields(): void
    {
        $user = $this->makeUser('editme');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'editme'])
            ->call('startEditing')
            ->set('name', 'نام جدید')
            ->set('email', 'new@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertSame('نام جدید', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('09120000000', $user->phone);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = $this->makeUser('passme');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'passme'])
            ->call('startEditing')
            ->set('password', 'brand-new-pass')
            ->set('password_confirmation', 'brand-new-pass')
            ->call('save')
            ->assertHasErrors(['current_password' => 'required']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'passme'])
            ->call('startEditing')
            ->set('current_password', 'wrong-password')
            ->set('password', 'brand-new-pass')
            ->set('password_confirmation', 'brand-new-pass')
            ->call('save')
            ->assertHasErrors(['current_password' => 'current_password']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'passme'])
            ->call('startEditing')
            ->set('current_password', 'secret-password')
            ->set('password', 'brand-new-pass')
            ->set('password_confirmation', 'brand-new-pass')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(\Hash::check('brand-new-pass', $user->fresh()->password));
    }

    public function test_username_validation_rejects_invalid_characters(): void
    {
        $user = $this->makeUser('validname');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'validname'])
            ->call('startEditing')
            ->set('username', 'نام فارسی')
            ->call('save')
            ->assertHasErrors(['username' => 'regex']);
    }

    public function test_changing_username_redirects_to_new_url(): void
    {
        $user = $this->makeUser('oldname');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'oldname'])
            ->call('startEditing')
            ->set('username', 'newname')
            ->call('save')
            ->assertRedirect(route('profile.show', 'newname'));

        $this->assertSame('newname', $user->fresh()->username);
    }

    public function test_employee_data_is_displayed_readonly(): void
    {
        $user = $this->makeUser('empuser');
        $employee = Employee::create([
            'first_name'    => 'رضا',
            'last_name'     => 'محمودی',
            'national_code' => '0012345678',
            'address'       => 'تهران، خیابان تست',
        ]);
        $user->employee_id = $employee->id;
        $user->save();

        $this->actingAs($user)
            ->get(route('profile.show', 'empuser'))
            ->assertOk()
            ->assertSee('0012345678')
            ->assertSee('تهران، خیابان تست');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\ProfilePage::class, ['username' => 'empuser'])
            ->call('startEditing')
            ->assertSet('name', 'کاربر empuser');
    }

    public function test_non_owner_cannot_save_profile(): void
    {
        $this->makeUser('realowner');
        $other = $this->makeUser('otherguy');

        // بازدید پروفایل دیگران بدون مجوز users.view در همان mount با 403 رد می‌شود
        $this->actingAs($other)
            ->get(route('profile.show', 'realowner'))
            ->assertForbidden();
    }
}
