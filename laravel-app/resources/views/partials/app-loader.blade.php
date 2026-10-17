{{--
    لودر سراسری — «اسکنر نئونی»
    جای دایره چرخشی، یک پلاک شیشه‌ای بارکد با پرتوی اسکن که هنگام عبور هر میله را روشن می‌کند.

    نام‌های کلاس با public/js/app-loader.js هم‌خوان است:
      .app-loader__text و .app-loader__caption (تغییر متن + انیمیشن تعویض پیام)
      #app-loader و .app-loader.is-visible (نمایش/پنهان‌سازی)
      .app-loader__progress / __percent / __fill / __bar.is-lit (پیشرفت واقعی از سرور)

    عرض هر میله مانند یک بارکد واقعی تناوب نازک/ضخیم دارد و متغیر --at موقعیت افقی میله
    است؛ CSS با همان نسبت، لحظه رسیدن پرتو به میله را محاسبه می‌کند. در حالت پیشرفت
    واقعی (is-determinate) همان «--at» مشخص می‌کند کدام میله‌ها روشن بمانند.
--}}
@php
    // عرض میله‌ها بر حسب پیکسل (الگوی بارکد: میله‌های باریک و ضخیم)
    $loaderBars = [3, 1, 2, 1, 4, 1, 2, 3, 1, 2, 4, 1, 1, 3, 2, 4, 1, 2, 1, 3, 2];
    $loaderBarCount = count($loaderBars);
@endphp

{{--
    آدرس خواندن درصد پیشرفت؛ جاگیرنده با توکن عملیات در جاوااسکریپت عوض می‌شود.
    آرگومان سوم route() باعث می‌شود آدرس نسبی باشد تا پولینگ همیشه هم‌مبدأ (same-origin) باشد؛
    وگرنه با تفاوت APP_URL و آدرسی که کاربر باز کرده، درخواست پولینگ بلاک می‌شد.
--}}
<div id="app-loader" class="app-loader" role="status" aria-live="polite" aria-busy="false"
    data-progress-url="{{ route('progress.show', ['token' => 'PLACEHOLDER'], false) }}" hidden>
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

        {{-- نوار درصد پیشرفت واقعی؛ تا وقتی سرور درصد نداده پنهان است --}}
        <div class="app-loader__progress" aria-hidden="true" hidden>
            <span class="app-loader__track"><span class="app-loader__fill"></span></span>
            <span class="app-loader__percent"></span>
        </div>

        <div class="app-loader__caption"><span class="app-loader__text">در حال پردازش</span><span class="app-loader__dots"></span></div>
    </div>
</div>
