<footer class="app-footer">

    <div class="footer-content">

        <div class="footer-info">
            {{-- فروش امروز : {{ number_format($todaySales) }}  --}}
            <span>
                {{ setting('currency', '') }}
            </span>
        </div>

        <div class="footer-datetime">

            <span class="footer-date">
                {{ verta()->format('l j F Y') }}
            </span>

            <span class="footer-separator">||</span>

            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>

        </div>

        <div class="footer-info">
             کاربر : {{ auth()->user()->name }}
        </div>

    </div>

</footer>
