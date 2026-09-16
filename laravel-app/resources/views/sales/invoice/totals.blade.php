<div class="totals">
    <div class="trow">
        <span>جمع کل</span>
        <span class="tval">{{ number_format($sale->total_price) }}</span>
    </div>

    @if ($sale->discount > 0)
        <div class="trow discount">
            <span>تخفیف</span>
            <span class="tval">{{ number_format($sale->discount) }}</span>
        </div>
    @endif

    <div class="trow">
        <span>نوع پرداخت</span>
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
            @endswitch
        </span>
    </div>

    @if ($sale->relationLoaded('payments'))
        @foreach ($sale->payments as $payment)
            <div class="trow">
                <span>{{ $payment->payment_type === 'cash' ? 'پرداخت نقدی' : 'پرداخت کارتخوان' }}</span>
                <span class="tval">{{ number_format($payment->amount) }}</span>
            </div>
        @endforeach
    @endif

    @if ($sale->payment_type === 'credit')
        <div class="trow">
            <span>باقی‌مانده نسیه</span>
            <span class="tval">{{ number_format((float) $sale->final_price - (float) $sale->paid_amount) }}</span>
        </div>
    @endif

    @if ($sale->change_amount > 0)
        <div class="trow">
            <span>باقی وجه</span>
            <span class="tval">{{ number_format($sale->change_amount) }}</span>
        </div>
    @endif

    <div class="grand-total">
        <span class="glabel">قابل پرداخت</span>
        <span class="gvalue">{{ number_format($sale->final_price) }}<span class="gunit">تومان</span></span>
    </div>
</div>
