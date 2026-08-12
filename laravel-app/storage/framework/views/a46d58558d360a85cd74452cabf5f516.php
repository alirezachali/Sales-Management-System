<div class="bold center">

    کالاهای خریداری شده

</div>

<div class="line"></div>

<?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div class="item">

    <div class="bold">

        <?php echo e($item->product->name); ?>


    </div>

    <div class="row">

        <span>

            <?php echo e(number_format($item->quantity, 0)); ?>

            ×
            <?php echo e(number_format($item->unit_price)); ?>


        </span>

        <span>

            <?php echo e(number_format($item->line_total)); ?>


        </span>

    </div>

</div>

<div class="line"></div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice/items.blade.php ENDPATH**/ ?>