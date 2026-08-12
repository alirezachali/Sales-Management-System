<style>
    <?php $paperSize =($settings['paper_size'] ?? '80') === '58' ? '58mm'
    : '80mm';
    ?>

    @page {
        
        size: <?php echo e($paperSize); ?> auto;

        margin: 0;
    }

    * {

        box-sizing: border-box;
    }

    html,
    body {

        margin: 0 auto;

        padding: 4mm;

        /* margin: 0;

        padding: 0; */

        width: <?php echo e($paperSize); ?>;
    }

    body {

        direction: rtl;

        font-family: Tahoma, sans-serif;

        font-size: 12px;

        color: #000;

    }

    .receipt {

        width: 100%;

    }

    .center {

        text-align: center;

    }

    .right {

        text-align: right;

    }

    .line {

        border-top: 1px dashed #000;

        margin: 6px 0;

    }

    .row {

        display: flex;

        justify-content: space-between;

    }

    .bold {

        font-weight: bold;

    }

    .big {

        font-size: 14px;

    }

    .item {

        margin-bottom: 6px;

    }

    .item {

        padding: 4px 0;

    }

    .item .bold {

        margin-bottom: 4px;

    }

    .row {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 10px;

    }

    .line {

        border-top: 1px dashed #555;

        margin: 6px 0;

    }

    .footer {

        text-align: center;

        margin-top: 15px;

        font-size: 11px;

    }

    .total {

        font-size: 15px;

        font-weight: bold;

    }

    .total span:last-child {

        font-size: 18px;

    }

    .row {

        margin: 4px 0;

    }

    .line {

        border-top: 1px dashed #777;

        margin: 7px 0;

    }

    .footer {

        margin-top: 15px;

        text-align: center;

        font-size: 11px;

        line-height: 20px;

        color: #444;

    }

    @media screen {

        body {

            background: #ececec;

            padding: 20px;

        }

        .receipt {

            background: white;

            padding: 15px;

            margin: auto;

            box-shadow: 0 0 10px rgba(0, 0, 0, .15);

        }

    }
</style>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/sales/invoice/style.blade.php ENDPATH**/ ?>