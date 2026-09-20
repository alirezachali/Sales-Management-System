<footer class="app-footer">
    <div class="footer-content">

        <!-- ساعت و تاریخ شمسی-->
        <div class="footer-datetime">
            <span class="footer-date">
                {{-- <i class="bi bi-calendar2-date"></i> --}}
                {{ verta()->format('l j F Y') }}
            </span>
            <span class="footer-separator">📅&emsp;🕖</span>
            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>
        </div>

        {{--===== سمت چپ فوتر: زنگ پیام‌های من و هشدارهای هوشمند =====--}}
        @auth
            <div class="footer-actions">
                @can('messages.inbox')
                    <livewire:messages.messages-bell />
                @endcan
                <livewire:alerts-bell />
            </div>
        @endauth

    </div>
</footer>

<script>

    function updateClock() {
        const now = new Date();
        document.getElementById("liveClock").innerHTML =
            now.toLocaleTimeString("fa-IR");
    }

    setInterval(updateClock, 1000);
    updateClock();

</script>
