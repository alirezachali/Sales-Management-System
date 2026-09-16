<?php

namespace App\Livewire\Messages;

use App\Models\MessageRecipient;
use Livewire\Component;

/**
 * زنگ پیام‌های نوار بالا؛ آخرین پیام‌های دریافتی کاربر و تعداد
 * پیام‌های خوانده‌نشده را نشان می‌دهد. (مثل AlertsBell، بدون polling
 * و با به‌روزرسانی هنگام جابجایی بین صفحات)
 */
class MessagesBell extends Component
{
    public bool $open = false;

    /** حداکثر تعداد پیامی که در پنل بازشونده نمایش داده می‌شود */
    private const PREVIEW_LIMIT = 5;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function getMessagesProperty()
    {
        return MessageRecipient::with('message.sender:id,name')
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(self::PREVIEW_LIMIT)
            ->get();
    }

    public function getCountProperty(): int
    {
        return MessageRecipient::where('user_id', auth()->id())->unread()->count();
    }

    public function render()
    {
        return view('livewire.messages.messages-bell');
    }
}
