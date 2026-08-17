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
                <?php echo e(verta()->format('l j F Y')); ?>

            </span>
            <span class="footer-separator">||</span>
            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>
            <i class="bi bi-clock"></i>
        </div>

        <!-- جعبه ابزار فوتر -->
        <div class="footer-tools">

            

            <a href="#" class="btn" title="نوتیفیکیشن">
                <i class="bi bi-bell-fill"></i>
            </a>

        </div>

    </div>
</footer>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/partials/footer.blade.php ENDPATH**/ ?>