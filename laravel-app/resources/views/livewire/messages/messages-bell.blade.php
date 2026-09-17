<div x-data="{ open: @js($open) }" @click.outside="open = false" @keydown.escape.window="open = false" class="alerts-bell">

    <button type="button" class="nav-icon-btn alerts-toggle" @click="open = !open" title="پیام‌های من">
        <i class="bi bi-envelope{{ $this->count > 0 ? '-exclamation' : '' }}"></i>
        @if ($this->count > 0)
            <span class="alerts-badge">{{ $this->count > 9 ? '9+' : $this->count }}</span>
        @endif
    </button>

    <div class="alerts-panel" x-show="open" x-cloak x-transition.opacity.duration.150ms>
        <div class="alerts-head d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-envelope-paper text-info"></i>
                پیام‌های من
            </span>
            @if ($this->count > 0)
                <span class="badge bg-warning text-dark">{{ $this->count }} خوانده‌نشده</span>
            @endif
        </div>

        <div class="alerts-body">
            @forelse ($this->messages as $item)
                <a href="{{ route('messages.inbox') }}" class="alerts-item">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="fw-semibold small">
                            @if (! $item->isRead())
                                <span class="badge bg-primary text-dark">جدید</span>
                            @endif
                            {{ $item->message?->subject ?: '(بدون موضوع)' }}
                        </div>
                    </div>
                    <div class="text-muted" style="font-size:.72rem">
                        {{ \Illuminate\Support\Str::limit($item->message?->body ?? '', 60) }}
                    </div>
                    <div class="text-muted" style="font-size:.68rem">
                        {{ relativeTimeFa($item->message?->created_at) }}
                    </div>
                </a>
            @empty
                <div class="alerts-empty">
                    <i class="bi bi-envelope-open"></i>
                    <div>پیام جدیدی ندارید.</div>
                </div>
            @endforelse
        </div>

        <a href="{{ route('messages.inbox') }}" class="alerts-item text-center fw-bold border-top"
            style="border-color: var(--lux-border) !important">
            مشاهده همه پیام‌ها
        </a>
    </div>
</div>
