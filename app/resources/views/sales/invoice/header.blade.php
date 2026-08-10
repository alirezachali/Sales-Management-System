<div class="center">

    @if (!empty($settings['store_logo']) && ($settings['print_logo'] ?? 0))
        <div class="mb-2">
            <img src="{{ asset('storage/' . $settings['store_logo']) }}" alt="{{ $settings['store_name'] ?? 'فروشگاه' }}"
                style="max-width:120px; max-height:80px;">
        </div>
    @endif

    <h3>{{ $settings['store_name'] ?? 'فروشگاه' }}</h3>

    @if (!empty($settings['phone']) && ($settings['print_phone'] ?? 0))
        <div>{{ $settings['phone'] }}</div>
    @endif

    @if (!empty($settings['address']) && ($settings['print_address'] ?? 0))
        <div>{{ $settings['address'] }}</div>
    @endif

</div>

<div class="line"></div>

<div class="row">

    <span>شماره:</span>

    <span>{{ $sale->invoice_number }}</span>

</div>

@if ($settings['print_datetime'] ?? 0)
    <div class="row">

        <span>تاریخ:</span>

        <span>
            {{ $sale->created_at->format('Y/m/d H:i') }}
        </span>

    </div>
@endif

<div class="row">

    <span>صندوق دار:</span>

    <span>{{ $sale->user->name ?? '-' }}</span>

</div>

<div class="line"></div>
