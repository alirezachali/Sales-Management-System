<div x-data="{ open: @js($open) }" @click.outside="open = false" @keydown.escape.window="open = false" class="alerts-bell">

    <button type="button" class="nav-icon-btn alerts-toggle" @click="open = !open" title="هشدارهای هوشمند">
        {{-- <i class="bi bi-bell"></i> --}}
        📢
        @if ($this->count > 0)
            <span class="alerts-badge">{{ $this->count > 9 ? '9+' : $this->count }}</span>
        @endif
    </button>

    <div class="alerts-panel" x-show="open" x-cloak x-transition.opacity.duration.150ms>
        <div class="alerts-head">
            <span class="alerts-head-title">
                {{-- <i class="bi bi-spark2s text-warning"></i> --}}
                📢
                <span>مـرکـز هـشـدار هـا</span>
            </span>

            <button type="button" class="alerts-refresh-btn" wire:click="refreshAlerts"
                wire:loading.attr="disabled" wire:target="refreshAlerts" title="به‌روزرسانی هشدارها">
                <span wire:loading wire:target="refreshAlerts" class="spinner-border spinner-border-sm"></span>
                <i class="bi bi-arrow-clockwise" wire:loading.remove wire:target="refreshAlerts"></i>
            </button>
        </div>

        <div class="alerts-body">
            @forelse ($this->alerts as $group)
                <div class="alerts-group">
                    <div class="alerts-group-title text-{{ $group['color'] }}">
                        <i class="bi {{ $group['icon'] }}"></i> {{ $group['group'] }}
                    </div>
                    @foreach ($group['items'] as $item)
                        <a href="{{ $item['url'] }}" class="alerts-item">
                            <div class="fw-semibold small">{{ $item['title'] }}</div>
                            <div class="text-muted" style="font-size:.72rem">{{ $item['meta'] }}</div>
                        </a>
                    @endforeach
                </div>
            @empty
                <div class="alerts-empty">
                    <i class="bi bi-emoji-smile"></i>
                    <div>هـمـه‌چـیـز تـحـت کـنـتـرلـه! 🎯</div>
                </div>
            @endforelse
        </div>

        @if ($this->generatedAt)
            <div class="alerts-foot">
                <i class="bi bi-clock-history"></i>
                آخرین به‌روزرسانی: {{ relativeTimeFa($this->generatedAt) }}
            </div>
        @endif
    </div>
</div>
