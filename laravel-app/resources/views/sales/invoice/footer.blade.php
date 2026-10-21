<div class="divider"></div>

<div class="footer">
    @if (!empty($settings['receipt_footer']))
        <div class="thanks">{{ $settings['receipt_footer'] }}</div>
    @endif

    <div class="note">لطفاً فاکتور خود را تا زمان تعویض کالا نگهداری نمایید.</div>

    @if (!empty($settings['website']))
        <div class="site">{{ $settings['website'] }}</div>
    @endif

    @if (!empty($barcodeSvg) || !empty($qrSvg))
        <div class="doc-codes">
            @if (!empty($barcodeSvg))
                <div class="barcode">{!! $barcodeSvg !!}</div>
                <div class="caption">{{ $sale->invoice_number }}</div>
            @endif

            @if (!empty($qrSvg))
                <div class="qrcode">{!! $qrSvg !!}</div>
            @endif
        </div>
    @endif
</div>
