<div class="store">
    @if (!empty($settings['store_logo']) && ($settings['print_logo'] ?? 0))
        <img class="logo" src="{{ asset('storage/' . $settings['store_logo']) }}"
            alt="{{ $settings['store_name'] ?? 'فروشگاه' }}">
    @endif

    <div class="store-name">{{ $settings['store_name'] ?? 'فروشگاه' }}</div>
    <div class="store-divider"></div>

    <div class="store-meta">
        @if (!empty($settings['phone']) && ($settings['print_phone'] ?? 0))
            <div>{{ $settings['phone'] }}</div>
        @endif

        @if (!empty($settings['address']) && ($settings['print_address'] ?? 0))
            <div>{{ $settings['address'] }}</div>
        @endif
    </div>
</div>

<div class="center">
    <span class="doc-badge">فاکتور فروش</span>
</div>

<div class="meta-grid">
    <div class="cell">
        <span class="label">شماره فاکتور</span>
        <span class="value">{{ $sale->invoice_number }}</span>
    </div>

    <div class="cell">
        <span class="label">صندوق‌دار</span>
        <span class="value">{{ $sale->user->name ?? '-' }}</span>
    </div>

    @if ($settings['print_datetime'] ?? 0)
        <div class="cell">
            <span class="label">تاریخ</span>
            <span class="value">{{ jalaliDate($sale->created_at) }}</span>
        </div>

        <div class="cell">
            <span class="label">ساعت</span>
            <span class="value">{{ jalaliTime($sale->created_at) }}</span>
        </div>
    @endif
</div>
