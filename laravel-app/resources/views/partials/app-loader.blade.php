{{--
    لودر سراسری — «اسکنر نئونی»
    جای دایره چرخشی، یک پلاک شیشه‌ای بارکد با پرتوی اسکن که هنگام عبور هر میله را روشن می‌کند.

    نام‌های کلاس با public/js/app-loader.js هم‌خوان است:
      .app-loader__text و .app-loader__caption (تغییر متن + انیمیشن تعویض پیام)
      #app-loader و .app-loader.is-visible (نمایش/پنهان‌سازی)

    عرض هر میله مانند یک بارکد واقعی تناوب نازک/ضخیم دارد و متغیر --at موقعیت افقی میله
    است؛ CSS با همان نسبت، لحظه رسیدن پرتو به میله را محاسبه می‌کند.
--}}
@php
    // عرض میله‌ها بر حسب پیکسل (الگوی بارکد: میله‌های باریک و ضخیم)
    $loaderBars = [3, 1, 2, 1, 4, 1, 2, 3, 1, 2, 4, 1, 1, 3, 2, 4, 1, 2, 1, 3, 2];
    $loaderBarCount = count($loaderBars);
@endphp

<div id="app-loader" class="app-loader" role="status" aria-live="polite" aria-busy="false" hidden>
    <div class="app-loader__backdrop"></div>
    <div class="app-loader__stage">
        <div class="app-loader__scanner" aria-hidden="true">
            <div class="app-loader__code">
                @foreach ($loaderBars as $index => $width)
                    <span
                        class="app-loader__bar{{ $index % 5 === 2 ? ' app-loader__bar--tall' : '' }}"
                        style="--at: {{ round($index / max(1, $loaderBarCount - 1), 4) }}; --bar-w: {{ $width }}px"></span>
                @endforeach
            </div>
            <span class="app-loader__beam"></span>
        </div>
        <div class="app-loader__caption"><span class="app-loader__text">در حال پردازش</span><span class="app-loader__dots"></span></div>
    </div>
</div>
