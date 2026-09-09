<?php

namespace App\Livewire\Concerns;

/**
 * چک مجوز (permission) در متدهای کامپوننت‌های Livewire.
 *
 * میدل‌ور can: روی روت‌ها فقط «ورود به صفحه» را کنترل می‌کند؛ اکشن‌های
 * Livewire از endpoint جداگانه‌ی خود Livewire اجرا می‌شوند و از آن روت
 * عبور نمی‌کنند، بنابراین هر اکشن حساس باید داخل خودش هم مجوز را چک کند.
 */
trait AuthorizesActions
{
    /**
     * در صورت نداشتن مجوز، درخواست را با خطای 403 متوقف می‌کند.
     */
    protected function authorizeAction(string $permission): void
    {
        if (! auth()->user()?->hasPermission($permission)) {
            abort(403, 'شما اجازه‌ی انجام این عملیات را ندارید.');
        }
    }
}
