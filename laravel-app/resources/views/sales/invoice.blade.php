<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاکتور {{ $sale->invoice_number }}</title>
    @include('sales.invoice.style')
</head>
<body>
<div class="receipt">
    @include('sales.invoice.header')
    @include('sales.invoice.items')
    @include('sales.invoice.totals')
    @include('sales.invoice.footer')
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
</html>