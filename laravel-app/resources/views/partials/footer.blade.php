<footer class="app-footer">
    <div class="footer-content">

        <!-- انتخاب زبان سیستم -->
        <div class="footer-lang">
             زبان سیستم
        </div>

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

        <!-- جعبه ابزار فوتر -->
        <div class="footer-tools">

            <a href="#" class="btn" title="ماشین حساب">
                <i class="bi bi-calculator-fill"></i>
            </a>

            <a href="#" class="btn" title="یادداشت">
                <i class="bi bi-stickies-fill"></i>
            </a>

            <a href="#" class="btn" title="پیام">
                <i class="bi bi-chat-left-dots-fill"></i>
            </a>

            <a href="#" class="btn" title="نوتیفیکیشن">
                <i class="bi bi-bell-fill"></i>
            </a>

        </div>

    </div>
</footer>
