<?php

namespace App\Livewire\Roles;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Livewire\Component;

class RolePermissionManager extends Component
{
    use AuthorizesActions;

    public Role $role;

    /** @var array<int, string> شناسه‌ی مجوزهای انتخاب‌شده */
    public array $selected = [];

    public function mount(Role $role): void
    {
        $this->authorizeAction('roles.permissions');

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
        $this->authorizeAction('roles.permissions');

        $this->role->permissions()->sync($this->selected);

        /* کش مجوزهای نقش باید باطل شود تا تغییرات بلافاصله اعمال شوند */
        cache()->forget("role-permissions-{$this->role->id}");

        session()->flash('success', 'مجوزهای نقش با موفقیت ذخیره شدند.');
    }

    public function render()
    {
        /* آمار کارت‌های بالای صفحه؛ بر اساس وضعیت فعلی فرم (selected) محاسبه
           می‌شود تا با تیک زدن/برداشتن مجوزها بلافاصله به‌روز شوند */
        $groups = PermissionGroup::with('permissions')->orderBy('sort_order')->get();

        $selectedIds = array_map('intval', $this->selected);

        /* تعداد مجوزهای رول جاری که در گروه‌های مختلف پخش شده‌اند */
        $selectedGroupIds = Permission::query()
            ->whereIn('id', $selectedIds)
            ->distinct()
            ->pluck('permission_group_id');

        return view('livewire.roles.role-permission-manager', [
            'groups' => $groups,
            'totalPermissions' => Permission::count(),
            'selectedCount' => count($selectedIds),
            'remainingCount' => Permission::count() - count($selectedIds),
            'touchedGroupsCount' => $selectedGroupIds->count(),
        ]);
    }
}
