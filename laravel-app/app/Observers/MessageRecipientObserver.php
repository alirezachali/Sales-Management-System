<?php

namespace App\Observers;

use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Cache;

/**
 * زنگ پیام‌ها (MessagesBell) نتیجه‌ی خود را به‌صورت per-user کش می‌کند.
 * بعد از هر تغییر روی گیرنده (پیام جدید یا خواندن پیام) کشِ همان کاربر
 * باطل می‌شود تا شمارنده و پیش‌نمایش بلافاصله تازه شوند.
 */
class MessageRecipientObserver
{
    public function saved(MessageRecipient $recipient): void
    {
        Cache::forget('bell-messages-'.$recipient->user_id);
        Cache::forget('bell-messages-count-'.$recipient->user_id);
    }

    public function deleted(MessageRecipient $recipient): void
    {
        Cache::forget('bell-messages-'.$recipient->user_id);
        Cache::forget('bell-messages-count-'.$recipient->user_id);
    }
}
