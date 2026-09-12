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
                    کارت
                @break

                @case('credit')
                    نسیه
                @break
            @endswitch
        </span>
    </div>

    <div class="grand-total">
        <span class="glabel">قابل پرداخت</span>
        <span class="gvalue">{{ number_format($sale->final_price) }}<span class="gunit">تومان</span></span>
    </div>
</div>
