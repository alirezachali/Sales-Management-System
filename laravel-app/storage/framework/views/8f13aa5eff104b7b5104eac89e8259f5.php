<div class="center">

    <?php if(!empty($settings['store_logo']) && ($settings['print_logo'] ?? 0)): ?>
        <div class="mb-2">
            <img src="<?php echo e(asset('storage/' . $settings['store_logo'])); ?>" alt="<?php echo e($settings['store_name'] ?? 'فروشگاه'); ?>"
                style="max-width:120px; max-height:80px;">
        </div>
    <?php endif; ?>

    <h3><?php echo e($settings['store_name'] ?? 'فروشگاه'); ?></h3>

    <?php if(!empty($settings['phone']) && ($settings['print_phone'] ?? 0)): ?>
        <div><?php echo e($settings['phone']); ?></div>
    <?php endif; ?>

    <?php if(!empty($settings['address']) && ($settings['print_address'] ?? 0)): ?>
        <div><?php echo e($settings['address']); ?></div>
    <?php endif; ?>

</div>

<div class="line"></div>

<div class="row">

    <span>شماره:</span>

    <span><?php echo e($sale->invoice_number); ?></span>

</div>

<?php if($settings['print_datetime'] ?? 0): ?>
    <div class="row">

        <span>تاریخ:</span>

        <span>
            <?php echo e($sale->created_at->format('Y/m/d H:i')); ?>

        </span>

    </div>
<?php endif; ?>

<div class="row">

    <span>صندوق دار:</span>

    <span><?php echo e($sale->user->name ?? '-'); ?></span>

</div>

<div class="line"></div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice/header.blade.php ENDPATH**/ ?>