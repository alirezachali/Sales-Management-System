<footer class="app-footer">

    <div class="footer-content">

        <div class="footer-info">
            
            <span>
                <?php echo e(setting('currency', '')); ?>

            </span>
        </div>

        <div class="footer-datetime">

            <span class="footer-date">
                <?php echo e(verta()->format('l j F Y')); ?>

            </span>

            <span class="footer-separator">||</span>

            <span id="liveClock" class="footer-clock">
                --:--:--
            </span>

        </div>

        <div class="footer-info">
             کاربر : <?php echo e(auth()->user()->name); ?>

        </div>

    </div>

</footer>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/partials/footer.blade.php ENDPATH**/ ?>