<footer class="app-footer">
    <div class="footer-content">

        <!-- ساعت و تاریخ شمسی-->
        <div class="footer-datetime">

            <span class="footer-date">
                <i class="bi bi-calendar2-date"></i>
                {{ verta()->format('l j F Y') }}
            </span>

            <span class="footer-separator">||</span>

            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>
            <i class="bi bi-clock"></i>

        </div>

        <div>
            <a href="#" class="btn" title="نوتیفیکیشن">
                <i class="bi bi-bell-fill"></i>
            </a>
        </div>

    </div>
</footer>
