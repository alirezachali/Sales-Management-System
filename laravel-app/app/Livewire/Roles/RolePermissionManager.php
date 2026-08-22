<?php

namespace App\Livewire\Roles;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Livewire\Component;

class RolePermissionManager extends Component
{
    public Role $role;

    /** @var array<int, string> شناسه‌ی مجوزهای انتخاب‌شده */
    public array $selected = [];

    public function mount(Role $role): void
    {
        $this->role = $role;

        $this->selected = $role->permissions()
            ->pluck('permissions.id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    /**
     * انتخاب/لغو انتخاب همه‌ی مجوزهای یک گروه به‌صورت یکجا
     */
    public function toggleGroup(int $groupId): void
    {
        $permissionIds = Permission::where('permission_group_id', $groupId)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $allSelected = $permissionIds !== []
            && count(array_intersect($permissionIds, $this->selected)) === count($permissionIds);

        if ($allSelected) {
            $this->selected = array_values(array_diff($this->selected, $permissionIds));
        } else {
            $this->selected = array_values(array_unique(array_merge($this->selected, $permissionIds)));
        }
    }

    public function save(): void
    {
        $this->role->permissions()->sync($this->selected);

        session()->flash('success', 'مجوزهای نقش با موفقیت ذخیره شدند.');
    }

    public function render()
    {
        return view('livewire.roles.role-permission-manager', [
            'groups' => PermissionGroup::with('permissions')->orderBy('sort_order')->get(),
        ]);
    }
}
