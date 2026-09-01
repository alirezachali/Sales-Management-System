<footer class="app-footer">
    <div class="footer-content">

        <!-- ساعت و تاریخ شمسی-->
        <div class="footer-datetime">

            <span class="footer-date">
                <i class="bi bi-calendar2-date"></i>
                {{ verta()->format('l j F Y') }}
            </span>

            <span class="footer-separator">|</span>

            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>
            <i class="bi bi-clock"></i>

        </div>

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
