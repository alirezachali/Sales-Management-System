<div x-data="{ open: @js($open) }" @click.outside="open = false" @keydown.escape.window="open = false" class="alerts-bell">

    <button type="button" class="nav-icon-btn alerts-toggle" @click="open = !open" title="هشدارهای هوشمند">
        <i class="bi bi-bell"></i>
        @if ($this->count > 0)
            <span class="alerts-badge">{{ $this->count > 9 ? '9+' : $this->count }}</span>
        @endif
    </button>

    <div class="alerts-panel" x-show="open" x-cloak x-transition.opacity.duration.150ms>
        <div class="alerts-head">
            <i class="bi bi-spark2s text-warning"></i>
            <span>مرکز هشدارها</span>
        </div>

        <div class="alerts-body">
            @forelse ($this->alerts as $group)
                <div class="alerts-group">
                    <div class="alerts-group-title text-{{ $group['color'] }}">
                        <i class="bi {{ $group['icon'] }}"></i> {{ $group['group'] }}
                    </div>
                    @foreach ($group['items'] as $item)
                        <a href="{{ $item['route'] }}" class="alerts-item">
                            <div class="fw-semibold small">{{ $item['title'] }}</div>
                            <div class="text-muted" style="font-size:.72rem">{{ $item['meta'] }}</div>
                        </a>
                    @endforeach
                </div>
            @empty
                <div class="alerts-empty">
                    <i class="bi bi-emoji-smile"></i>
                    <div>همه‌چیز تحت کنترله! 🎯</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
