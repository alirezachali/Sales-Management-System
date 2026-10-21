<style>
    @php
        /*
        |------------------------------------------------------------------
        | عرض کاغذ حرارتی:
        | کاغذ ۸۰ میلی‌متری در عمل ۷۲ میلی‌متر ناحیه قابل چاپ دارد و کاغذ
        | ۵۸ میلی‌متری حدود ۴۸ میلی‌متر. اگر عرض محتوا ۸۰ میلی‌متر باشد،
        | پرینتر لبه‌های چپ و راست فیش را می‌برد (مشکل قبلی).
        |------------------------------------------------------------------
        */
        $is58 = ($settings['paper_size'] ?? '80') === '58';
        $paperWidth = $is58 ? '58mm' : '80mm';
        $printWidth = $is58 ? '48mm' : '72mm';
        $baseFont = $is58 ? '10.5px' : '12px';
    @endphp

    @page {
        size: {{ $paperWidth }} auto;
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        background: #fff;
    }

    body {
        /* عرض دقیقاً برابر ناحیه قابل چاپ، نه عرض اسمی کاغذ */
        width: {{ $printWidth }};
        margin: 0 auto;
        direction: rtl;
        color: #000;
        font-family: Tahoma, "Segoe UI", Arial, sans-serif;
        font-size: {{ $baseFont }};
        line-height: 1.5;
        /* حروف فارسی نباید letter-spacing بگیرند (اتصال حروف می‌شکند) */
        letter-spacing: normal;
        word-spacing: normal;
        -webkit-font-smoothing: antialiased;
    }

    /* روی کاغذ حرارتی هر رنگ غیرمشکی خاکستری محو می‌شود؛ همه‌چیز مشکی. */
    .receipt,
    .receipt * {
        color: #000 !important;
    }

    .receipt {
        width: 100%;
        padding: 0;
    }

    .receipt + .receipt {
        margin-top: 6mm;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    /* ---------- Header ---------- */
    .store {
        text-align: center;
        padding-bottom: 2mm;
    }

    .store .logo {
        display: block;
        max-width: 22mm;
        max-height: 14mm;
        margin: 0 auto 1.5mm;
        filter: grayscale(100%);
    }

    .store-name {
        font-size: {{ $is58 ? '13px' : '15px' }};
        font-weight: bold;
        line-height: 1.35;
        word-break: break-word;
    }

    .store-meta {
        font-size: {{ $is58 ? '9.5px' : '10px' }};
        line-height: 1.6;
        margin-top: 1mm;
        word-break: break-word;
    }

    .divider {
        border-top: 1px dashed #000;
        margin: 2mm 0;
    }

    .divider-solid {
        border-top: 1px solid #000;
        margin: 2mm 0;
    }

    .doc-type {
        text-align: center;
        font-weight: bold;
        font-size: {{ $is58 ? '11px' : '12px' }};
        padding: 1mm 0 2mm;
    }

    /* ---------- Invoice meta ---------- */
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2mm;
    }

    .meta-table td {
        padding: .8mm 0;
        vertical-align: top;
    }

    .meta-table .k {
        width: 32%;
        font-size: {{ $is58 ? '9.5px' : '10px' }};
    }

    .meta-table .v {
        font-weight: bold;
        font-size: {{ $is58 ? '10.5px' : '11.5px' }};
        text-align: left;
        word-break: break-word;
    }

    /* ---------- Section title ---------- */
    .section-title {
        text-align: center;
        font-weight: bold;
        font-size: {{ $is58 ? '10.5px' : '11.5px' }};
        padding: 1mm 0;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        margin-bottom: 1.5mm;
    }

    /* ---------- Items table ---------- */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: {{ $is58 ? '9.5px' : '10.5px' }};
        margin-bottom: 2mm;
    }

    .items-table thead th {
        font-weight: bold;
        font-size: {{ $is58 ? '9px' : '10px' }};
        padding: .8mm 0;
        text-align: right;
        border-bottom: 1px dashed #000;
        border-top: none;
        background: none;
    }

    .items-table tbody td {
        padding: 1mm 0;
        border-bottom: 1px dotted #000;
        vertical-align: top;
        line-height: 1.45;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .items-table .item-index {
        width: 7%;
        text-align: center;
    }

    .items-table .col-name {
        width: 45%;
        font-weight: bold;
        padding-inline-end: 1.5mm;
    }

    .items-table .col-detail {
        width: 26%;
        text-align: center;
        white-space: nowrap;
    }

    .items-table .col-total {
        width: 22%;
        text-align: left;
        font-weight: bold;
        white-space: nowrap;
    }

    .items-table thead .col-total {
        text-align: left;
    }

    /* ---------- Totals ---------- */
    .totals {
        border-top: 1px solid #000;
        padding-top: 1.5mm;
        margin-bottom: 2mm;
    }

    .totals .trow {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 2mm;
        font-size: {{ $is58 ? '10px' : '11px' }};
        padding: .8mm 0;
    }

    .totals .trow .tlabel {
        word-break: break-word;
    }

    .totals .trow .tval {
        font-weight: bold;
        white-space: nowrap;
        text-align: left;
    }

    .pay-method {
        font-weight: bold;
        white-space: nowrap;
    }

    .grand-total {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 2mm;
        font-weight: bold;
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
        padding: 1.5mm 0;
        margin-top: 1.5mm;
    }

    .grand-total .glabel {
        font-size: {{ $is58 ? '11px' : '12px' }};
    }

    .grand-total .gvalue {
        font-size: {{ $is58 ? '13px' : '15px' }};
        white-space: nowrap;
        text-align: left;
    }

    .grand-total .gunit {
        font-size: 9px;
        font-weight: normal;
        margin-inline-start: 1mm;
    }

    /* ---------- Footer ---------- */
    .footer {
        text-align: center;
        font-size: {{ $is58 ? '9.5px' : '10px' }};
        line-height: 1.7;
    }

    .footer .note {
        font-weight: bold;
        margin-top: .5mm;
    }

    .footer .site {
        font-weight: bold;
        margin-top: .5mm;
        word-break: break-word;
    }

    .footer .thanks {
        margin-bottom: 1mm;
    }

    .copy-badge {
        text-align: center;
        font-size: 9.5px;
        border: 1px solid #000;
        border-radius: 1mm;
        padding: 0 2mm;
        display: inline-block;
        margin-bottom: 1mm;
    }

    .doc-codes {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1mm;
        margin-top: 2mm;
    }

    .doc-codes .barcode svg,
    .doc-codes .qrcode svg {
        display: block;
        width: 100%;
        height: auto;
    }

    .doc-codes .barcode {
        width: 44mm;
        max-width: 100%;
    }

    .doc-codes .qrcode {
        width: {{ $is58 ? '18mm' : '22mm' }};
    }

    .doc-codes .caption {
        font-size: 8.5px;
        text-align: center;
        word-break: break-word;
    }

    /* ---------- Screen preview ---------- */
    @media screen {
        body {
            width: auto;
            background: #e9e9e9;
            padding: 16px;
        }

        .receipt {
            background: #fff;
            width: {{ $printWidth }};
            margin: 0 auto;
            padding: 4mm 3mm;
            border-radius: 4px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .18);
        }

        .receipt + .receipt {
            margin-top: 16px;
        }

        .print-toolbar {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 0 auto 14px;
            max-width: {{ $printWidth }};
        }

        .print-toolbar .btn {
            flex: 1;
            padding: 8px 10px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid #333;
            background: #fff;
        }

        .print-toolbar .btn-primary {
            background: #111;
            color: #fff;
            border-color: #111;
        }
    }

    /* ---------- Print ---------- */
    @media print {
        body {
            background: #fff;
            padding: 0;
            margin: 0 auto;
        }

        .print-toolbar {
            display: none !important;
        }

        .receipt {
            box-shadow: none;
            border-radius: 0;
            padding: 0;
            margin: 0 auto;
        }

        .totals,
        .grand-total,
        .footer,
        .meta-table,
        .store {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>
