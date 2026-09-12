<style>
    @php $paperSize = ($settings['paper_size'] ?? '80') === '58' ? '58mm' : '80mm'; @endphp

    @page {
        size: {{ $paperSize }} auto;
        margin: 0;
    }

    * {
        box-sizing: border-box;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    html,
    body {
        margin: 0 auto;
        padding: 4mm;
        width: {{ $paperSize }};
    }

    body {
        direction: rtl;
        font-family: Tahoma, sans-serif;
        font-size: 12px;
        color: #111;
    }

    .receipt {
        width: 100%;
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
        padding: 2px 0 10px;
    }

    .store .logo {
        max-width: 110px;
        max-height: 70px;
        margin-bottom: 6px;
    }

    .store-name {
        font-size: 17px;
        font-weight: bold;
        letter-spacing: .5px;
    }

    .store-divider {
        width: 26px;
        height: 3px;
        background: #111;
        border-radius: 2px;
        margin: 6px auto;
    }

    .store-meta {
        font-size: 10px;
        color: #555;
        line-height: 1.8;
    }

    .doc-badge {
        display: inline-block;
        border: 1.5px solid #111;
        border-radius: 4px;
        padding: 2px 14px;
        font-size: 10.5px;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border: 1px solid #111;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .meta-grid .cell {
        padding: 5px 8px;
    }

    .meta-grid .cell:nth-child(odd) {
        border-inline-start: 1px solid #ddd;
    }

    .meta-grid .cell:nth-child(n+3) {
        border-top: 1px solid #ddd;
    }

    .meta-grid .label {
        font-size: 9px;
        color: #777;
        display: block;
        margin-bottom: 2px;
    }

    .meta-grid .value {
        font-size: 11.5px;
        font-weight: bold;
    }

    /* ---------- Section title ---------- */
    .section-title {
        background: #111;
        color: #fff;
        text-align: center;
        font-size: 11.5px;
        font-weight: bold;
        letter-spacing: 1px;
        padding: 5px 0;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    /* ---------- Items table ---------- */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-bottom: 14px;
    }

    .items-table thead th {
        background: #111;
        color: #fff;
        font-weight: bold;
        font-size: 10px;
        letter-spacing: .5px;
        padding: 5px 4px;
        text-align: right;
    }

    .items-table thead th:first-child {
        border-start-start-radius: 5px;
    }

    .items-table thead th:last-child {
        border-start-end-radius: 5px;
    }

    .items-table tbody td {
        padding: 6px 4px;
        border-bottom: 1px solid #e2e2e2;
        vertical-align: top;
        line-height: 1.5;
    }

    .items-table tbody tr:nth-child(even) {
        background: #f4f4f4;
    }

    .items-table .item-index {
        width: 7%;
        font-size: 9px;
        color: #999;
        text-align: center;
    }

    .items-table .col-name {
        width: 38%;
        font-weight: bold;
    }

    .items-table .col-detail {
        width: 33%;
        text-align: center;
        color: #444;
        font-size: 10px;
        white-space: nowrap;
    }

    .items-table .col-total {
        width: 22%;
        text-align: left;
        font-weight: bold;
        white-space: nowrap;
    }

    /* ---------- Totals ---------- */
    .totals {
        border-top: 2px solid #111;
        padding-top: 8px;
        margin-bottom: 14px;
    }

    .totals .trow {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11.5px;
        padding: 4px 2px;
        color: #222;
    }

    .totals .trow .tval {
        font-weight: bold;
    }

    .totals .trow.discount .tval {
        color: #444;
    }

    .totals .trow.discount .tval::before {
        content: "- ";
    }

    .pay-method {
        display: inline-block;
        border: 1px solid #111;
        border-radius: 20px;
        padding: 1px 10px;
        font-size: 10px;
        font-weight: bold;
    }

    .grand-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #111;
        color: #fff;
        border-radius: 6px;
        padding: 9px 10px;
        margin-top: 6px;
    }

    .grand-total .glabel {
        font-size: 11.5px;
        font-weight: bold;
        letter-spacing: .5px;
    }

    .grand-total .gvalue {
        font-size: 14.5px;
        font-weight: bold;
    }

    .grand-total .gunit {
        font-size: 9px;
        opacity: .75;
        margin-inline-start: 3px;
    }

    /* ---------- Footer ---------- */
    .foot-divider {
        border-top: 1px dashed #999;
        margin: 12px 0 8px;
    }

    .footer {
        text-align: center;
        font-size: 10px;
        color: #555;
        line-height: 1.9;
    }

    .footer .note {
        font-weight: bold;
        color: #222;
    }

    .footer .site {
        font-size: 10.5px;
        font-weight: bold;
        color: #111;
        letter-spacing: .5px;
    }

    @media screen {
        body {
            background: #e9e9e9;
            padding: 20px;
        }

        .receipt {
            background: #fff;
            padding: 15px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .15);
        }
    }
</style>
