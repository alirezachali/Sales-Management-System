<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>فاکتور <?php echo e($sale->invoice_number); ?></title>

    <?php echo $__env->make('sales.invoice.style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</head>

<body>

<div class="receipt">

    <?php echo $__env->make('sales.invoice.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('sales.invoice.items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('sales.invoice.totals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('sales.invoice.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>

<div style="margin-top:10px">

<?php echo e(now()->format('Y/m/d H:i:s')); ?>


</div>

<script>

window.onload = function () {
    window.print();
};

window.onafterprint = function () {
    window.close();
};

</script>

</body>

</html><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice.blade.php ENDPATH**/ ?>