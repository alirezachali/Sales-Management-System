<div class="line"></div>

<div class="row">
    <span>جمع کل</span>
    <span><?php echo e(number_format($sale->total_price)); ?></span>
</div>

<div class="row">
    <span>تخفیف</span>
    <span><?php echo e(number_format($sale->discount)); ?></span>
</div>

<div class="line"></div>

<div class="row total">
    <span>قابل پرداخت</span>
    <span><?php echo e(number_format($sale->final_price)); ?></span>
</div>

<div class="line"></div>

<div class="row">
    <span>نوع پرداخت</span>

    <span>
        <?php switch($sale->payment_type):

            case ('cash'): ?>
                نقدی
                <?php break; ?>

            <?php case ('card'): ?>
                کارت
                <?php break; ?>

            <?php case ('mixed'): ?>
                ترکیبی
                <?php break; ?>

            <?php default: ?>
                <?php echo e($sale->payment_type); ?>


        <?php endswitch; ?>
    </span>
</div>

<div class="row">
    <span>صندوق‌دار</span>
    <span><?php echo e($sale->user->name ?? '-'); ?></span>
</div>

<div class="line"></div><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice/totals.blade.php ENDPATH**/ ?>