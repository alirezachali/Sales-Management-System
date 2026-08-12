<div class="line"></div>

<div class="row">
    <span>جمع کل</span>
    <span>{{ number_format($sale->total_price) }}</span>
</div>

<div class="row">
    <span>تخفیف</span>
    <span>{{ number_format($sale->discount) }}</span>
</div>

<div class="line"></div>

<div class="row total">
    <span>قابل پرداخت</span>
    <span>{{ number_format($sale->final_price) }}</span>
</div>

<div class="line"></div>

<div class="row">
    <span>نوع پرداخت</span>

    <span>
        @switch($sale->payment_type)
            @case('cash')
                نقدی
            @break

            @case('card')
                کارت
            @break

            @case('mixed')
                ترکیبی
            @break

            @default
                {{ $sale->payment_type }}
        @endswitch
    </span>
</div>

<div class="row">
    <span>صندوق‌دار</span>
    <span>{{ $sale->user->name ?? '-' }}</span>
</div>

<div class="line"></div>
