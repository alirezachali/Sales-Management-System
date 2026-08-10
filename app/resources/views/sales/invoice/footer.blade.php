<div class="center footer">

    <div class="line"></div>

    @if (!empty($settings['receipt_footer']))
        <div>
            {{ $settings['receipt_footer'] }}
        </div>
    @endif

    <br>

    <div>
        لطفاً فاکتور خود را تا زمان تعویض کالا نگهداری نمایید.
    </div>

    @if (!empty($settings['website']))
        <br>

        <div>
            {{ $settings['website'] }}
        </div>
    @endif

    @if (!empty($settings['phone']) && ($settings['print_phone'] ?? 0))
        <div>
            {{ $settings['phone'] }}
        </div>
    @endif

    <div>
        نسخه نرم افزار : 1.0
    </div>

</div>
