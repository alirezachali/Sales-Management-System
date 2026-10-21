<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فیش {{ $sale->invoice_number }}</title>
    @include('sales.invoice.style')
</head>

<body>
    {{--
        تعداد نسخه چاپ و چاپ خودکار از تنظیمات (تب چاپ) خوانده می‌شوند.
        هر نسخه یک بلوک مستقل است تا روی کاغذ رول پشت سر هم چاپ شود.
    --}}
    @php
        $copies = max(1, (int) ($settings['print_copies'] ?? 1));
        $autoPrint = (bool) ($settings['auto_print'] ?? 0);
    @endphp

    <div class="print-toolbar">
        <button type="button" class="btn btn-primary" onclick="printReceipt()">چاپ</button>
        <button type="button" class="btn" onclick="window.close()">بستن</button>
    </div>

    @for ($copy = 1; $copy <= $copies; $copy++)
        <div class="receipt">
            @if ($copies > 1)
                <div class="center">
                    <span class="copy-badge">نسخه {{ $copy }} از {{ $copies }}</span>
                </div>
            @endif

            @include('sales.invoice.header')
            @include('sales.invoice.items')
            @include('sales.invoice.totals')
            @include('sales.invoice.footer')
        </div>
    @endfor

    <script>
        function printReceipt() {
            window.print();
        }

        window.addEventListener('load', function() {
            // صبر کوتاه تا لوگو و فونت‌ها رندر شوند، سپس چاپ در صورت فعال بودن.
            if (@json($autoPrint)) {
                setTimeout(printReceipt, 250);
            }
        });

        // فقط وقتی صفحه به‌صورت تب جداگانه باز شده باشد بسته می‌شود؛
        // نباید هنگام لغو چاپ ناگهان بسته شود.
        window.addEventListener('afterprint', function() {
            if (@json($autoPrint) && window.opener) {
                setTimeout(function() {
                    window.close();
                }, 400);
            }
        });
    </script>
</body>

</html>
