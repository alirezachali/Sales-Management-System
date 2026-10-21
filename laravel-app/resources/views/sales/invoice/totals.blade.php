@php
    $currency = $settings['currency'] ?? 'تومان';
@endphp

<div class="totals">
    <div class="trow">
        <span class="tlabel">جمع کل</span>
        <span class="tval">{{ number_format((float) $sale->total_price) }}</span>
    </div>

    @if ((float) $sale->discount > 0)
        <div class="trow">
            <span class="tlabel">تخفیف</span>
            <span class="tval">-{{ number_format((float) $sale->discount) }}</span>
        </div>
    @endif

    @if ((float) $sale->points_redeemed > 0)
        <div class="trow">
            <span class="tlabel">امتیاز مصرف‌شده ({{ number_format((float) $sale->points_redeemed) }} امتیاز)</span>
            <span class="tval">-{{ number_format((float) $sale->loyalty_discount) }}</span>
        </div>
    @endif

    <div class="trow">
        <span class="tlabel">نوع پرداخت</span>
        <span class="pay-method">
            @switch($sale->payment_type)
                @case('cash')
                    نقدی
                @break

                @case('card')
                    کارتخوان
                @break

                @case('mixed')
                    ترکیبی
                @break

                @case('credit')
                    نسیه
                @break

                @default
                    {{ $sale->payment_type }}
            @endswitch
        </span>
    </div>

    @if ($sale->relationLoaded('payments'))
        @foreach ($sale->payments as $payment)
            <div class="trow">
                <span class="tlabel">{{ $payment->payment_type === 'cash' ? 'پرداخت نقدی' : 'پرداخت کارتخوان' }}</span>
                <span class="tval">{{ number_format((float) $payment->amount) }}</span>
            </div>
        @endforeach
    @endif

    @if ($sale->payment_type === 'credit')
        <div class="trow">
            <span class="tlabel">باقی‌مانده نسیه</span>
            <span class="tval">{{ number_format((float) $sale->final_price - (float) $sale->paid_amount) }}</span>
        </div>
    @endif

    @if ((float) $sale->change_amount > 0)
        <div class="trow">
            <span class="tlabel">باقی وجه</span>
            <span class="tval">{{ number_format((float) $sale->change_amount) }}</span>
        </div>
    @endif

    @if ($sale->relationLoaded('customer') && $sale->customer && (float) $sale->points_redeemed > 0)
        <div class="trow">
            <span class="tlabel">مانده امتیاز مشتری</span>
            <span class="tval">{{ number_format((float) ($sale->customer->available_points ?? 0)) }}</span>
        </div>
    @endif

    <div class="grand-total">
        <span class="glabel">قابل پرداخت</span>
        <span class="gvalue">{{ number_format((float) $sale->final_price) }}<span
                class="gunit">{{ $currency }}</span></span>
    </div>
</div>
