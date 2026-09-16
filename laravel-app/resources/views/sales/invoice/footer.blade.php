<div class="footer">
    <div class="foot-divider"></div>

    @if (!empty($settings['receipt_footer']))
        <div>{{ $settings['receipt_footer'] }}</div>
    @endif

    <div class="note">لطفاً فاکتور خود را تا زمان تعویض کالا نگهداری نمایید.</div>

    @if (!empty($settings['website']))
        <div class="site">{{ $settings['website'] }}</div>
    @endif
</div>
