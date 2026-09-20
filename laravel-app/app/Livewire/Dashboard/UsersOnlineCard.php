<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

/**
 * کارت «کاربران» داشبورد مدیر.
 *
 * وضعیت آنلاین/آفلاین هر کاربر را با میدل‌ور TrackUserOnline (کلید کش
 * user-online-{id}) و ستون last_seen_at نشان می‌دهد. poll مستقل ۱۰ ثانیه‌ای
 * دارد تا بدون اجرای دوباره‌ی آمار سنگین داشبورد، وضعیت‌ها سریع تازه شوند.
 */
class UsersOnlineCard extends Component
{
    public int $pollingSeconds = 10;

    public function render()
    {
        // آنلاین‌ها اول؛ بقیه بر اساس تازه‌ترین زمان دیده‌شدن
        // فقط ستون‌های لازم واکشی می‌شوند تا هر poll ۱۰ ثانیه‌ای سبک بماند.
        $users = User::query()
            ->with('role:id,name,color,icon')
            ->select('id', 'name', 'username', 'avatar', 'last_seen_at', 'last_login_at')
            ->get()
            ->sortByDesc(fn (User $u) => [$u->isOnline() ? 1 : 0, $u->lastSeen()?->getTimestamp() ?? 0])
            ->values();

        return view('livewire.dashboard.users-online-card', [
            'users' => $users,
        ]);
    }
}
