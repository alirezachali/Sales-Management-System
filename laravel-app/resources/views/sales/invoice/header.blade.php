<div class="store">
    @if (!empty($settings['store_logo']) && ($settings['print_logo'] ?? 0))
        <img class="logo" src="{{ asset('storage/' . $settings['store_logo']) }}"
            alt="{{ $settings['store_name'] ?? 'فروشگاه' }}">
    @endif

    <div class="store-name">{{ $settings['store_name'] ?: 'فروشگاه' }}</div>

    <div class="store-meta">
        @if (!empty($settings['phone']) && ($settings['print_phone'] ?? 0))
            <div>تلفن: {{ $settings['phone'] }}</div>
        @endif

        @if (!empty($settings['address']) && ($settings['print_address'] ?? 0))
            <div>{{ $settings['address'] }}</div>
        @endif
    </div>
</div>

<div class="divider"></div>

<div class="doc-type">فاکتور فروش</div>

<table class="meta-table">
    <tr>
        <td class="k">شماره فاکتور</td>
        <td class="v">{{ $sale->invoice_number }}</td>
    </tr>

    <tr>
        <td class="k">صندوق‌دار</td>
        <td class="v">{{ $sale->user->name ?? '-' }}</td>
    </tr>

    @if (!empty($sale->customer))
        <tr>
            <td class="k">مشتری</td>
            <td class="v">{{ $sale->customer->full_name ?? '-' }}</td>
        </tr>
    @endif

    @if ($settings['print_datetime'] ?? 0)
        <tr>
            <td class="k">تاریخ</td>
            <td class="v">{{ jalaliDate($sale->created_at) }}</td>
        </tr>

        <tr>
            <td class="k">ساعت</td>
            <td class="v">{{ jalaliTime($sale->created_at) }}</td>
        </tr>
    @endif
</table>

<div class="divider"></div>
