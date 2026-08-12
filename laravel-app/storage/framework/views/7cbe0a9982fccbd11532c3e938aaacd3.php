<div class="center footer">

    <div class="line"></div>

    <?php if(!empty($settings['receipt_footer'])): ?>
        <div>
            <?php echo e($settings['receipt_footer']); ?>

        </div>
    <?php endif; ?>

    <br>

    <div>
        لطفاً فاکتور خود را تا زمان تعویض کالا نگهداری نمایید.
    </div>

    <?php if(!empty($settings['website'])): ?>
        <br>

        <div>
            <?php echo e($settings['website']); ?>

        </div>
    <?php endif; ?>

    <?php if(!empty($settings['phone']) && ($settings['print_phone'] ?? 0)): ?>
        <div>
            <?php echo e($settings['phone']); ?>

        </div>
    <?php endif; ?>

    <div>
        نسخه نرم افزار : 1.0
    </div>

</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice/footer.blade.php ENDPATH**/ ?>