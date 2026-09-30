<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    <style>
        /* ============ انتخاب روش پرداخت (مودال تسویه) ============ */
        .pay-type-row {
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            gap: 8px;
        }

        .pay-type-btn {
            --pay-accent: 32, 107, 196;
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 6px 10px;
            background: var(--lux-surface, rgba(255, 255, 255, 0.045));
            border: 2px solid var(--lux-border, rgba(148, 163, 184, 0.16));
            border-radius: 18px;
            color: inherit;
            cursor: pointer;
            outline: none;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease,
                background-color .2s ease;
        }

        /* آیکن در بزرگ‌ترین حالت ممکن، بدون بیرون زدن از کارت */
        .pay-type-btn .pay-type {
            display: block;
            width: min(96px, 100%);
            height: auto;
            aspect-ratio: 1 / 1;
            object-fit: contain;
            transition: transform .2s ease;
        }

        .pay-type-btn .label {
            font-size: .82rem;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
        }

        /* رنگ اختصاصی هر روش برای افکت هاور */
        .pay-type-btn.pay-cash {
            --pay-accent: 34, 197, 94;
        }

        .pay-type-btn.pay-card {
            --pay-accent: 62, 166, 255;
        }

        .pay-type-btn.pay-credit {
            --pay-accent: 249, 115, 22;
        }

        .pay-type-btn.pay-mixed {
            --pay-accent: 217, 70, 239;
        }

        /* هاور: بالا آمدن، هاله رنگی و روشن‌تر شدن کارت */
        .pay-type-btn:not(:disabled):hover,
        .pay-type-btn:not(:disabled):focus-visible {
            transform: translateY(-4px);
            border-color: rgb(var(--pay-accent));
            background-image: linear-gradient(180deg, rgba(var(--pay-accent), .16), rgba(var(--pay-accent), .04));
            box-shadow: 0 12px 24px -12px rgba(var(--pay-accent), .85);
        }

        .pay-type-btn:not(:disabled):hover .pay-type,
        .pay-type-btn:not(:disabled):focus-visible .pay-type {
            transform: scale(1.08);
        }

        .pay-type-btn:not(:disabled):active {
            transform: translateY(-1px) scale(.98);
        }

        /* حالت انتخاب‌شده: هاله و خط بردر سبز */
        .pay-type-btn.active {
            border-color: #22c55e;
            background-image: linear-gradient(180deg, rgba(34, 197, 94, .18), rgba(34, 197, 94, .05));
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .18), 0 0 18px 2px rgba(34, 197, 94, .45);
        }

        .pay-type-btn.active .label {
            color: #22c55e;
        }

        /* نشانگر تیک سبز روی کارت انتخاب‌شده */
        .pay-type-btn .pay-check {
            position: absolute;
            top: 8px;
            inset-inline-start: 8px;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #22c55e;
            color: #04210f;
            font-size: .68rem;
            line-height: 1;
            box-shadow: 0 6px 14px -6px rgba(34, 197, 94, 1);
            opacity: 0;
            transform: scale(.3);
            transition: opacity .2s ease, transform .25s cubic-bezier(.2, .9, .3, 1.5);
        }

        .pay-type-btn.active .pay-check {
            opacity: 1;
            transform: scale(1);
        }

        /* لرزش کوتاه کارت نسیه وقتی مشتری حذف می‌شود */
        @keyframes pay-shake {
            0%, 100% { transform: translateX(0); }
            15%  { transform: translateX(-7px); }
            30%  { transform: translateX(6px); }
            45%  { transform: translateX(-5px); }
            60%  { transform: translateX(4px); }
            75%  { transform: translateX(-2px); }
        }

        .pay-type-btn.is-shaking {
            animation: pay-shake .55s cubic-bezier(.36, .07, .19, .97);
            border-color: rgba(239, 68, 68, .7) !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .2) !important;
        }

        .pay-type-btn.active .pay-type {
            transform: scale(1.06);
        }

        /* گزینه غیرفعال (مثلاً نسیه بدون مشتری): قابل انتخاب نیست */
        .pay-type-btn:disabled {
            cursor: not-allowed;
            opacity: .45;
            filter: grayscale(.65);
            box-shadow: none;
        }

        /* روی نمایشگرهای باریک هم هر 4 کارت در یک ردیف می‌مانند */
        @media (max-width: 575.98px) {
            .pay-type-row {
                gap: 5px;
            }

            .pay-type-btn {
                padding: 8px 4px 7px;
                border-radius: 14px;
            }

            .pay-type-btn .pay-type {
                max-width: 58px;
            }

            .pay-type-btn .label {
                font-size: .68rem;
            }
        }

        /* ==========================================================
           مودال پرداخت و تسویه — پوسته مدرن (Glass / Aurora)
           ========================================================== */
        .checkout-modal {
            --co-text: #e2e8f0;
            --co-text-dim: #94a3b8;
            --co-field-bg: rgba(9, 13, 26, .55);
            --tblr-border-radius: 12px;
        }

        [data-bs-theme="light"] .checkout-modal {
            --co-text: #1e293b;
            --co-text-dim: #64748b;
            --co-field-bg: #ffffff;
        }

        /* پوسته مودال: گردی، هاله بنفش و انیمیشن ورود */
        .checkout-modal .modal-content {
            position: relative;
            overflow: hidden;
            color: var(--co-text);
            border: 1px solid rgba(124, 92, 255, .3) !important;
            box-shadow:
                0 30px 80px -24px rgba(0, 0, 0, .8),
                0 0 70px -18px rgba(124, 92, 255, .6);
            animation: co-pop .32s cubic-bezier(.2, .9, .3, 1.15) both;
        }

        @keyframes co-pop {
            from {
                opacity: 0;
                transform: translateY(20px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* هاله‌های رنگی پشت محتوا */
        .checkout-modal .modal-content::before,
        .checkout-modal .modal-content::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            filter: blur(75px);
            pointer-events: none;
            z-index: 0;
        }

        .checkout-modal .modal-content::before {
            top: -180px;
            inset-inline-end: -90px;
            background: rgba(124, 92, 255, .38);
        }

        .checkout-modal .modal-content::after {
            bottom: -190px;
            inset-inline-start: -70px;
            background: rgba(62, 166, 255, .3);
        }

        .checkout-modal form {
            position: relative;
            z-index: 1;
        }

        /* ---------- هدر ---------- */
        .checkout-modal .modal-header {
            position: relative;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--lux-border) !important;
            background: linear-gradient(180deg, rgba(124, 92, 255, .16), transparent);
        }

        /* نوار رنگی بالای مودال */
        .checkout-modal .modal-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--lux-gradient, linear-gradient(135deg, #7c5cff, #3ea6ff));
        }

        .checkout-modal .modal-title {
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: 1.02rem;
        }

        .checkout-modal .co-title-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 12px;
            font-size: 1.05rem;
            color: #fff;
            background: var(--lux-gradient, linear-gradient(135deg, #7c5cff, #3ea6ff));
            box-shadow: 0 10px 22px -10px rgba(124, 92, 255, 1);
        }

        .checkout-modal .btn-close {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 10px;
            background-size: 12px;
            background-color: rgba(148, 163, 184, .14);
            opacity: .85;
            transition: background-color .2s ease, transform .25s ease, opacity .2s ease;
        }

        .checkout-modal .btn-close:hover {
            opacity: 1;
            background-color: rgba(239, 68, 68, .25);
            transform: rotate(90deg);
        }

        /* ---------- بدنه ---------- */
        .checkout-modal .modal-body {
            max-height: min(74vh, 700px);
            overflow-y: auto;
            padding: 18px 20px;
        }

        /* ---------- پنل‌های بخش‌بندی ---------- */
        .checkout-modal .co-section {
            padding: 14px;
            margin-bottom: 14px;
            border-radius: 18px;
            border: 1px solid var(--lux-border);
            background: rgba(148, 163, 184, .055);
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        .checkout-modal .co-section:focus-within {
            border-color: rgba(124, 92, 255, .45);
            background: rgba(124, 92, 255, .07);
            box-shadow: 0 0 0 3px rgba(124, 92, 255, .1);
        }

        .checkout-modal .co-label {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin-bottom: .6rem;
            font-size: .82rem;
            font-weight: 700;
            color: var(--co-text);
        }

        .checkout-modal .co-label > i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 8px;
            font-size: .72rem;
            color: #c4b5fd;
            background: rgba(124, 92, 255, .18);
        }

        .checkout-modal .co-hint {
            display: flex;
            align-items: flex-start;
            gap: .35rem;
            margin-top: .65rem;
            font-size: .78rem;
            line-height: 1.6;
            color: var(--co-text-dim);
        }

        /* ---------- فرم‌ها ---------- */
        .checkout-modal .form-label {
            font-size: .78rem;
            font-weight: 700;
            color: var(--co-text-dim);
        }

        .checkout-modal .form-control,
        .checkout-modal .input-group-text {
            background-color: var(--co-field-bg);
            border-color: var(--lux-border);
            color: var(--co-text);
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .checkout-modal .input-group-text {
            color: var(--co-text-dim);
        }

        .checkout-modal .form-control:focus {
            background-color: var(--co-field-bg);
            border-color: rgba(124, 92, 255, .65);
            box-shadow: 0 0 0 .2rem rgba(124, 92, 255, .18);
            color: var(--co-text);
        }

        .checkout-modal .form-control::placeholder {
            color: var(--co-text-dim);
            opacity: .8;
        }

        /* ---------- فیلد جستجوی مشتری ---------- */
        .checkout-modal .cust-search {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: 6px;
            border-radius: 16px;
            border: 1px solid var(--lux-border);
            background: var(--co-field-bg);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .checkout-modal .cust-search:focus-within {
            border-color: rgba(124, 92, 255, .6);
            box-shadow: 0 0 0 .2rem rgba(124, 92, 255, .16), 0 14px 34px -22px rgba(124, 92, 255, 1);
        }

        .checkout-modal .cust-search-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 12px;
            font-size: .82rem;
            color: #c4b5fd;
            background: rgba(124, 92, 255, .18);
        }

        .checkout-modal .cust-search .form-control {
            padding-inline: 2px;
            font-size: .88rem;
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .checkout-modal .cust-search-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            border: 0;
            border-radius: 50%;
            background: rgba(148, 163, 184, .16);
            color: var(--co-text-dim);
            font-size: .68rem;
            cursor: pointer;
            transition: background-color .18s ease, color .18s ease;
        }

        .checkout-modal .cust-search-clear:hover {
            background: rgba(239, 68, 68, .2);
            color: #f87171;
        }

        .checkout-modal .cust-any {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: 8px 12px;
            border: 0;
            border-radius: 12px;
            background: rgba(148, 163, 184, .14);
            color: var(--co-text-dim);
            font-size: .74rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .checkout-modal .cust-any:hover {
            color: #7ec8ff;
            background: rgba(62, 166, 255, .2);
            transform: translateY(-1px);
        }

        /* ---------- لیست نتایج جستجوی مشتری ---------- */
        .checkout-modal .cust-results {
            margin-top: .6rem;
            border-radius: 16px;
            border: 1px solid rgba(124, 92, 255, .28);
            background: var(--lux-dropdown-bg, rgba(16, 21, 40, .96));
            box-shadow: 0 26px 55px -30px rgba(0, 0, 0, 1);
            overflow: hidden;
            animation: co-pop .2s ease both;
        }

        .checkout-modal .cust-results-head {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: 8px 12px;
            font-size: .72rem;
            font-weight: 700;
            color: var(--co-text-dim);
            background: rgba(124, 92, 255, .1);
            border-bottom: 1px solid var(--lux-border);
        }

        .checkout-modal .cust-list {
            max-height: 240px;
            overflow-y: auto;
        }

        .checkout-modal .cust-item {
            display: flex;
            width: 100%;
            align-items: center;
            gap: .7rem;
            padding: 10px 12px;
            border: 0;
            border-bottom: 1px solid var(--lux-border);
            background: transparent;
            color: var(--co-text);
            text-align: start;
            cursor: pointer;
            transition: background-color .16s ease;
        }

        .checkout-modal .cust-item:last-child {
            border-bottom: 0;
        }

        .checkout-modal .cust-item:hover {
            background: rgba(124, 92, 255, .16);
        }

        /* ---------- آواتار و مشخصات مشتری ---------- */
        .checkout-modal .cust-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 50%;
            font-size: .84rem;
            font-weight: 700;
            color: #c4b5fd;
            background: linear-gradient(135deg, rgba(124, 92, 255, .3), rgba(62, 166, 255, .16));
            box-shadow: inset 0 0 0 1px rgba(124, 92, 255, .35);
            transition: color .18s ease, background .18s ease;
        }

        .checkout-modal .cust-item:hover .cust-avatar,
        .checkout-modal .cust-item:focus-visible .cust-avatar {
            color: #fff;
            background: var(--lux-gradient, linear-gradient(135deg, #7c5cff, #3ea6ff));
        }

        .checkout-modal .cust-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1 1 auto;
            min-width: 0;
        }

        .checkout-modal .cust-name {
            font-size: .86rem;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .checkout-modal .cust-mobile {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .72rem;
            color: var(--co-text-dim);
        }

        .checkout-modal .cust-pick {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            border-radius: 9px;
            font-size: .8rem;
            color: var(--co-text-dim);
            background: rgba(148, 163, 184, .12);
            opacity: 0;
            transform: scale(.8);
            transition: opacity .18s ease, transform .18s ease, background-color .18s ease, color .18s ease;
        }

        .checkout-modal .cust-item:hover .cust-pick,
        .checkout-modal .cust-item:focus-visible .cust-pick {
            opacity: 1;
            transform: none;
            color: #22c55e;
            background: rgba(34, 197, 94, .2);
        }

        /* ---------- هیچ مشتری‌ای پیدا نشد ---------- */
        .checkout-modal .cust-empty {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-top: .6rem;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px dashed rgba(148, 163, 184, .32);
            background: rgba(148, 163, 184, .06);
            font-size: .78rem;
            color: var(--co-text-dim);
        }

        /* ---------- چیپ مشتری انتخاب‌شده ---------- */
        .checkout-modal .cust-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 8px 8px 8px 10px;
            border-radius: 16px;
            border: 1px solid rgba(34, 197, 94, .38);
            background: linear-gradient(135deg, rgba(34, 197, 94, .16), rgba(62, 166, 255, .07));
            animation: co-pop .25s ease both;
        }

        .checkout-modal .cust-chip-main {
            display: flex;
            align-items: center;
            gap: .65rem;
            min-width: 0;
        }

        .checkout-modal .cust-chip .cust-avatar {
            color: #22c55e;
            background: rgba(34, 197, 94, .18);
            box-shadow: inset 0 0 0 1px rgba(34, 197, 94, .4);
        }

        .checkout-modal .cust-chip-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border: 0;
            border-radius: 10px;
            background: rgba(148, 163, 184, .14);
            color: var(--co-text-dim);
            font-size: .74rem;
            cursor: pointer;
            transition: background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .checkout-modal .cust-chip-clear:hover {
            background: rgba(239, 68, 68, .22);
            color: #f87171;
            transform: rotate(90deg);
        }

        /* ---------- جعبه امتیاز وفاداری ---------- */
        .checkout-modal .loyalty-box {
            padding: 14px;
            margin-bottom: 14px;
            border-radius: 18px;
            border: 1px solid rgba(217, 70, 239, .35);
            background: linear-gradient(135deg, rgba(217, 70, 239, .14), rgba(124, 92, 255, .08));
        }

        /* ---------- دکمه دوحالته نسیه (نقدی / کارتخوان) ---------- */
        .checkout-modal .mini-pay-toggle {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 3px;
            border-radius: 12px;
            border: 1px solid var(--lux-border);
            background: rgba(148, 163, 184, .12);
        }

        .checkout-modal .mini-pay-toggle button {
            padding: 6px 10px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: var(--co-text-dim);
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: background-color .18s ease, color .18s ease, box-shadow .18s ease;
        }

        .checkout-modal .mini-pay-toggle button.active-cash {
            color: #22c55e;
            background: rgba(34, 197, 94, .2);
            box-shadow: inset 0 0 0 1px rgba(34, 197, 94, .45);
        }

        .checkout-modal .mini-pay-toggle button.active-card {
            color: #3ea6ff;
            background: rgba(62, 166, 255, .2);
            box-shadow: inset 0 0 0 1px rgba(62, 166, 255, .45);
        }

        /* ---------- پنل مبلغ بر اساس روش پرداخت ---------- */
        .checkout-modal .co-amount {
            position: relative;
            padding: 16px 16px 16px 18px;
            margin-bottom: 14px;
            border-radius: 18px;
            border: 1px solid rgba(124, 92, 255, .28);
            background: rgba(124, 92, 255, .06);
        }

        .checkout-modal .co-amount::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            top: 16px;
            bottom: 16px;
            width: 3px;
            border-radius: 999px;
            background: var(--lux-gradient, linear-gradient(135deg, #7c5cff, #3ea6ff));
        }

        /* ---------- نوار مبلغ نهایی ---------- */
        .checkout-modal .co-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 12px 16px;
            border-radius: 16px !important;
            border-color: rgba(124, 92, 255, .42) !important;
            background: linear-gradient(135deg, rgba(124, 92, 255, .22), rgba(62, 166, 255, .12)) !important;
            box-shadow: 0 14px 34px -20px rgba(124, 92, 255, 1);
        }

        /* ---------- فوتر ---------- */
        .checkout-modal .modal-footer {
            gap: .5rem;
            padding: 14px 20px;
            border-top: 1px solid var(--lux-border) !important;
            background: linear-gradient(0deg, rgba(124, 92, 255, .1), transparent);
        }

        .checkout-modal .btn-co-cancel {
            border-radius: 14px !important;
            border: 1px solid var(--lux-border) !important;
            background: rgba(148, 163, 184, .12) !important;
            color: var(--co-text) !important;
            transition: background-color .18s ease, transform .18s ease;
        }

        .checkout-modal .btn-co-cancel:hover {
            background: rgba(239, 68, 68, .18) !important;
            transform: translateY(-2px);
        }

        .checkout-modal .btn-co-submit {
            border: 0 !important;
            border-radius: 14px !important;
            color: #fff !important;
            background: linear-gradient(135deg, #16a34a, #22c55e) !important;
            box-shadow: 0 14px 28px -14px rgba(34, 197, 94, 1);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .checkout-modal .btn-co-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.07);
            box-shadow: 0 18px 32px -14px rgba(34, 197, 94, 1);
        }

        .checkout-modal .btn-co-submit:disabled {
            filter: grayscale(.5);
            transform: none;
        }
    </style>

    @include('partials.flash-messages')

    {{-- Page Header --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h2>
                    <i class="bi bi-cart-check-fill text-primary"></i>
                    صــنــدوق فــروش
                </h2>
            <small class="d-none d-sm-inline">سـبـد خـریـد مـشـتـری و صـدور فـاکـتـور خـریـد</small>
            </div>
            <div class="text-center mt-2">
                <small class="">
                    <i class="bi bi-person-badge me-1"></i>
                    صـنـدوقـدار: {{ auth()->user()->name ?? '—' }}
                </small>
            </div>
        </div>
    </div>

    @error('checkout')
        <div class="alert alert-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $message }}</div>
    @enderror

    <div class="row g-3">
        <div class="col-lg-7">

            {{-- جستجو و افزودن کالا --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>
                        <i class="bi bi-plus-circle-fill"></i>
                        افـزودن کـالـا
                    </h3>
                    <div>
                        @if (count($cart))
                            <span class="badge bg-primary-lt rounded-pill">
                                {{ count($cart) }} قلم کالا در سبد
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    placeholder="اسـکـن یـا وارد کـردن بـارکـد…" wire:model="barcode"
                                    wire:keydown.enter="addByBarcode" autofocus>
                                <span class="input-group-text"><i class="bi bi-upc-scan text-primary"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    wire:model.live.debounce.400ms="search" placeholder="جـسـتـجـو نـام یـا بـارکـد کـالـا …">
                                <span class="input-group-text"><i class="bi bi-search text-info"></i></span>
                            </div>
                        </div>
                    </div>

                    @if ($search && $products->count())
                        <div class="list-group list-group-flush">
                            @foreach ($products as $product)
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    wire:click="addProduct({{ $product->id }})">
                                    <span>
                                        <span style="font-size:1rem;">
                                            <span style="font-size:1.4rem;">📦</span>
                                            {{ $product->name }}
                                        </span>
                                        <small class="d-block text-info" style="font-size:.72rem;">
                                            بـارکـد: {{ $product->barcode }} 🔰 مـوجـودی: {{ $product->formatted_stock }}
                                        </small>
                                    </span>
                                    <span class="badge bg-success-lt rounded-pill text-success-emphasis">
                                        {{ number_format($product->sell_price) }} تـومـان
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @elseif ($search)
                        <div class="text-muted small mt-2 text-center py-2">
                            <i class="bi bi-emoji-frown me-1"></i>کـالـایـی یـافـت نـشـد.
                        </div>
                    @endif
                </div>
            </div>

            {{-- اخطار کمبود موجودی --}}
            @if ($stockError)
                <div class="alert alert-warning border-0 d-flex align-items-center gap-2 mb-3 shadow-sm" dir="rtl">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 25px; color:rgb(235, 202, 17)"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>کـمـبـود مـوجـودی❗</strong>
                        <span>مـوجـودی</span>
                        <strong class="text-danger">{{ $stockError }}</strong>
                        <span>کـافـی نـیـست.</span>
                    </div>
                    <button type="button" class="btn-close ms-0 me-auto" wire:click="$set('stockError', null)"
                        title="بستن"></button>
                </div>
            @endif

            {{-- سبد فروش --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>
                        <i class="bi bi-bag-fill"></i>
                        سبـد خـریـد مـشـتـری
                    </h3>
                    <div>
                        @if (count($cart))
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                wire:click="clearCart" wire:confirm="آیا از پاک کردن کل سبد خرید مطمئن هستید؟">
                                <i class="bi bi-x-circle me-1"></i>
                                خالی کردن سبد
                            </button>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>کـالـا</th>
                                <th>قـیـمـت (تومان)</th>
                                <th class="text-center">تـعـداد</th>
                                <th>جـمـع</th>
                                <th class="text-center">حـذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart as $item)
                                <tr wire:key="cart-{{ $item['id'] }}">
                                    <td>
                                        <span class="fw-semibold">{{ $item['name'] }}</span>
                                        <small class="text-muted d-block" style="font-size:.72rem;">
                                            {{ $item['barcode'] }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">
                                            {{ number_format($item['price']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button class="btn btn-sm btn-outline-danger" type="button"
                                                wire:click="decrementQty({{ $item['id'] }})">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <span class="pos-qty-value">{{ $item['quantity'] }}</span>
                                            <button class="btn btn-sm btn-outline-success" type="button"
                                                wire:click="incrementQty({{ $item['id'] }})">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($item['price'] * $item['quantity']) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="removeFromCart({{ $item['id'] }})" title="حذف کالا">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            🛒
                                            سـبـد فـروش خـالـی اسـت.<br>
                                            <small>بـا بـارکـدخـوان یـا جـسـتـجـو کـالـا اضـافـه کـنـیـد.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--=============== ستون جمع‌بندی و پرداخت ===============--}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="bi bi-receipt-cutoff"></i>
                        جـمـع‌بـنـدی فـاکـتـور
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">تـخـفـیـف (تـومـان)</label>
                        <input type="number" min="0" step="any" class="form-control pos-scan-input"
                            wire:model.live.debounce.500ms="discount" placeholder="0">
                    </div>

                    <div class="">
                        <span class="text-muted">جـمـع کـل ({{ number_format(count($cart)) }} قـلـم)</span>
                        <strong>{{ number_format($this->subtotal) }}</strong>
                    </div>

                    @if ($discount > 0)
                        <div class="text-success">
                            <span>تـخـفـیـف</span>
                            <strong>{{ number_format(min($discount, $this->subtotal)) }}</strong>
                        </div>
                    @endif

                    <hr class="my-2">

                    <div class=" mb-3">
                        <span class="fw-bold">مـبـلـغ قـابـل پـرداخـت</span>
                        <strong class="fs-4 text-success">{{ number_format($this->finalPrice) }}
                            <small class="fw-normal">تـومـان</small>
                        </strong>
                    </div>

                    <button type="button" class="btn btn-success text-dark w-100"
                        wire:click="openCheckoutModal" @if (empty($cart)) disabled @endif>
                        <i class="bi bi-cash-coin me-4"></i>
                        پـــرداخـــت و ثـــبـــت فـــاکـــتـــور
                    </button>

                    {{-- <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person-badge me-1"></i>
                            صـنـدوقـدار: {{ auth()->user()->name ?? '—' }}
                        </small>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>


    {{--===================== مودال پرداخت / تسویه ====================--}}
    @if ($showCheckoutModal)
        <div class="modal modal-blur fade show d-block pos-modal checkout-modal" tabindex="-1"
            style="background: rgba(9,13,26,.72);" wire:key="checkout-modal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form wire:submit="checkout">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <span class="co-title-icon"><i class="bi bi-credit-card-2-front-fill"></i></span>
                                تـسـویـه و ثـبـت فـاکـتـور فـروش
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                aria-label="بستن"></button>
                        </div>

                        <div class="modal-body">

                            {{-- ============ انتخاب مشتری با جستجوی لایو ============ --}}
                            <div class="co-section">
                                <span class="co-label">
                                    <i class="bi bi-person-fill"></i>مـشـتـری
                                </span>

                                @if ($customerId)
                                    <div class="cust-chip">
                                        <span class="cust-chip-main">
                                            <span class="cust-avatar">
                                                <i class="bi bi-patch-check-fill"></i>
                                            </span>
                                            <span class="cust-info">
                                                <span class="cust-name">{{ $customerName }}</span>
                                                <span class="cust-mobile">مـشـتـری ثـبـت‌شـده</span>
                                            </span>
                                        </span>
                                        <button type="button" class="cust-chip-clear"
                                            wire:click="clearCustomer" title="حذف مشتری">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="cust-search">
                                        <span class="cust-search-icon">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control"
                                            placeholder="نام یا موبایل مشتری را تایپ کنید…"
                                            wire:model.live.debounce.300ms="customerQuery" autocomplete="off">
                                        @if ($customerQuery !== '')
                                            <button type="button" class="cust-search-clear" wire:click="$set('customerQuery', '')"
                                                title="پاک کردن جستجو">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="cust-any" wire:click="clearCustomer"
                                            title="ثبت فاکتور بدون انتخاب مشتری">
                                            <i class="bi bi-person-dash"></i>
                                            <span>متفرقه</span>
                                        </button>
                                    </div>

                                    @if (trim($customerQuery) !== '')
                                        @if (count($this->customerResults))
                                            <div class="cust-results">
                                                <div class="cust-results-head">
                                                    <i class="bi bi-people-fill"></i>
                                                    <span>{{ number_format(count($this->customerResults)) }} مشتری یافت شد</span>
                                                </div>
                                                <div class="cust-list">
                                                    @foreach ($this->customerResults as $customer)
                                                        <button type="button" class="cust-item"
                                                            wire:click="selectCustomer({{ $customer['id'] }})">
                                                            <span class="cust-avatar">
                                                                {{ mb_substr(trim($customer['name']), 0, 1) ?: '؟' }}
                                                            </span>
                                                            <span class="cust-info">
                                                                <span class="cust-name">{{ $customer['name'] }}</span>
                                                                @if (!empty($customer['mobile']))
                                                                    <span class="cust-mobile">
                                                                        <i class="bi bi-telephone-fill"></i>
                                                                        {{ $customer['mobile'] }}
                                                                    </span>
                                                                @endif
                                                            </span>
                                                            <span class="cust-pick">
                                                                <i class="bi bi-check2"></i>
                                                            </span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="cust-empty">
                                                <i class="bi bi-person-x"></i>
                                                <span>مشتری‌ای با این مشخصات یافت نشد — با دکمه «متفرقه» بدون انتخاب مشتری ادامه دهید.</span>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>

                            {{-- ============ استفاده از امتیاز وفاداری ============ --}}
                            @if ($customerId && $customerAvailablePoints > 0 && setting('loyalty_enabled', '1') == '1')
                                <div class="loyalty-box">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <span class="fw-bold"><i class="bi bi-gem text-fuchsia me-1"></i>امتیاز قابل استفاده:
                                                {{ number_format($customerAvailablePoints) }}</span>
                                            <div class="text-muted small">هـر امـتـیـاز = {{ number_format($pointValue) }} تومان تخفیف</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" min="0" max="{{ $customerAvailablePoints }}"
                                                class="form-control form-control-sm text-center" style="width:120px"
                                                wire:model.live.debounce.400ms="pointsToRedeem">
                                            <button type="button" class="btn btn-sm btn-fuchsia text-white rounded-pill"
                                                wire:click="applyAllPoints"
                                                title="حداکثر امتیاز مجاز برای این فاکتور">
                                                <i class="bi bi-stars me-1"></i>حـداکـثـر
                                            </button>
                                        </div>
                                    </div>
                                    @if ($pointsToRedeem > 0)
                                        <div class="alert alert-fuchsia mt-2 mb-0 py-2 small d-flex justify-content-between">
                                            <span><i class="bi bi-ticket-perforated me-1"></i>تـخـفـیـف امـتـیـازی:</span>
                                            <strong>{{ number_format($this->pointsDiscount) }} تـومـان</strong>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- ============ روش پرداخت ============ --}}
                            <div class="co-section">
                                <span class="co-label">
                                    <i class="bi bi-wallet2"></i>روش پـرداخـت
                                    <span class="text-danger">*</span>
                                </span>
                                <div class="pay-type-row" dir="rtl">
                                    <button type="button"
                                        class="pay-type-btn pay-cash {{ $paymentType === 'cash' ? 'active' : '' }}"
                                        wire:click="setPaymentType('cash')" title="پرداخت نقدی">
                                        <x-icon name="cash" class="pay-type" alt="نقدی" />
                                        <span class="label">نـقـدی</span>
                                        <span class="pay-check"><i class="bi bi-check-lg"></i></span>
                                    </button>

                                    <button type="button"
                                        class="pay-type-btn pay-card {{ $paymentType === 'card' ? 'active' : '' }}"
                                        wire:click="setPaymentType('card')" title="پرداخت با کارتخوان">
                                        <x-icon name="bank_cards" class="pay-type" alt="کارتخوان" />
                                        <span class="label">کـارتـخـوان</span>
                                        <span class="pay-check"><i class="bi bi-check-lg"></i></span>
                                    </button>

                                    <button type="button"
                                        class="pay-type-btn pay-credit {{ $paymentType === 'credit' ? 'active' : '' }}"
                                        x-data="{ blocked: false }"
                                        @credit-blocked.window="blocked = true; setTimeout(() => blocked = false, 750)"
                                        :class="blocked && 'is-shaking'"
                                        @disabled(! $customerId)
                                        wire:click="setPaymentType('credit')"
                                        title="{{ $customerId ? 'فروش نسیه' : 'نسیه فقط برای مشتری ثبت‌شده امکان‌پذیر است' }}">
                                        <x-icon name="credit" class="pay-type" alt="نسیه" />
                                        <span class="label">نـسـیـه</span>
                                        <span class="pay-check"><i class="bi bi-check-lg"></i></span>
                                    </button>

                                    <button type="button"
                                        class="pay-type-btn pay-mixed {{ $paymentType === 'mixed' ? 'active' : '' }}"
                                        wire:click="setPaymentType('mixed')" title="پرداخت ترکیبی نقدی و کارتخوان">
                                        <x-icon name="cash_card" class="pay-type" alt="ترکیبی" />
                                        <span class="label">تـرکـیـبـی</span>
                                        <span class="pay-check"><i class="bi bi-check-lg"></i></span>
                                    </button>
                                </div>
                                <div class="co-hint">
                                    <i class="bi bi-lightbulb-fill text-warning"></i>
                                    <span>
                                        @switch($paymentType)
                                            @case('cash')
                                                دریافت کل یا بخشی بیش از مبلغ فاکتور به‌صورت نقدی (باقی محاسبه می‌شود).
                                            @break
                                            @case('card')
                                                پرداخت دقیقاً از طریق کارتخوان.
                                            @break
                                            @case('mixed')
                                                بخشی نقدی و بخشی با کارتخوان؛ مجموع باید برابر مبلغ فاکتور باشد.
                                            @break
                                            @case('credit')
                                                ثبت بدهی روی حساب مشتری؛ امکان پیش‌پرداخت نقدی یا کارتخوان وجود دارد.
                                            @break
                                        @endswitch
                                    </span>
                                </div>

                                @error('paymentType')
                                    <div class="alert alert-warning py-2 small mt-3"><i
                                            class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ============ فیلدهای مبلغ بر اساس روش پرداخت ============ --}}
                            <div class="co-amount">

                                @if ($paymentType === 'cash')
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">مـبـلـغ نـقـدی دریـافـتـی</label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('paidAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="paidAmount">
                                            @error('paidAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill w-100"
                                                wire:click="$set('paidAmount', {{ $this->finalPrice }})">
                                                <i class="bi bi-magic me-1"></i>پـرداخـت دقـیـق ({{ number_format($this->finalPrice) }})
                                            </button>
                                        </div>
                                    </div>

                                    @if ($this->change > 0)
                                        <div class="alert alert-success mt-3 mb-0 d-flex justify-content-between py-2">
                                            <span><i class="bi bi-arrow-repeat me-1"></i>بـاقـی وجـه مـشـتـری:</span>
                                            <strong>{{ number_format($this->change) }} تـومـان</strong>
                                        </div>
                                    @endif

                                @elseif ($paymentType === 'card')
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            <i class="bi bi-credit-card-fill text-primary me-1"></i>
                                            مـبـلـغ قـابـل کـشـیـدن از کـارتـخـوان
                                        </span>
                                        <strong class="fs-5 text-primary">{{ number_format($this->finalPrice) }} تومان</strong>
                                    </div>

                                @elseif ($paymentType === 'mixed')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-cash-stack text-success me-1"></i>مـبـلـغ نـقـدی
                                            </label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('cashAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="cashAmount" placeholder="0">
                                            @error('cashAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-credit-card-fill text-primary me-1"></i>مـبـلـغ کـارتـخـوان
                                            </label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('cardAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="cardAmount" placeholder="0">
                                            @error('cardAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if (!$errors->has('cardAmount'))
                                        <div class="mt-3 d-flex justify-content-between small">
                                            <span class="text-muted">مـجـمـوع وارد شـده:
                                                <strong>{{ number_format($this->cashAmount + $this->cardAmount) }}</strong>
                                            </span>
                                            <span
                                                class="{{ abs($this->mixedDiff) < 0.001 ? 'text-success' : ($this->mixedDiff > 0 ? 'text-danger' : 'text-warning') }}">
                                                @if (abs($this->mixedDiff) < 0.001)
                                                    <i class="bi bi-check-circle-fill me-1"></i>تـسـویـه کـامـل شـد
                                                @elseif ($this->mixedDiff > 0)
                                                    <i class="bi bi-arrow-down-circle me-1"></i>بـاقـی‌مـانـده:
                                                    {{ number_format($this->mixedDiff) }}
                                                @else
                                                    <i class="bi bi-arrow-up-circle me-1"></i>اضـافـه:
                                                    {{ number_format(abs($this->mixedDiff)) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($this->mixedDiff > 0.001)
                                        <button type="button" class="btn btn-sm btn-primary-lt rounded-pill mt-2"
                                            wire:click="$set('cardAmount', {{ $this->mixedDiff + $this->cardAmount }})">
                                            <i class="bi bi-magic me-1"></i>تـکـمـیـل خـودکـار بـا کـارتـخـوان
                                        </button>
                                    @endif

                                @elseif ($paymentType === 'credit')
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">
                                                مـبـلـغ پـیـش‌پـرداخـت <span class="text-muted fw-normal">(اخـتـیـاری)</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" min="0" step="any"
                                                    class="form-control @error('paidAmount') is-invalid @enderror"
                                                    wire:model.live.debounce.400ms="paidAmount" placeholder="0">
                                                <span
                                                    class="input-group-text p-0 border-0"
                                                    style="background: transparent;">
                                                    <span class="mini-pay-toggle h-100">
                                                        <button type="button"
                                                            class="{{ $creditPayMethod === 'cash' ? 'active-cash' : '' }}"
                                                            wire:click="$set('creditPayMethod', 'cash')"
                                                            title="پرداخت پیش‌پرداخت نقدی">
                                                            <i class="bi bi-cash-stack"></i> نقدی
                                                        </button>
                                                        <button type="button"
                                                            class="{{ $creditPayMethod === 'card' ? 'active-card' : '' }}"
                                                            wire:click="$set('creditPayMethod', 'card')"
                                                            title="پرداخت پیش‌پرداخت با کارتخوان">
                                                            <i class="bi bi-credit-card"></i> کارتخوان
                                                        </button>
                                                    </span>
                                                </span>
                                                @error('paidAmount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <span class="text-muted small d-block">ثبت نسیه روی حساب مشتری</span>
                                            <strong class="fs-5 text-danger">{{ number_format($this->creditRemain) }}</strong>
                                            <span class="small text-danger">تومان</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- خلاصه فاکتور --}}
                            <div class="alert alert-primary co-total mb-0">
                                <span class="fw-semibold">
                                    <i class="bi bi-receipt me-1"></i>مبلغ قابل پرداخت:
                                </span>
                                <strong class="fs-5">{{ number_format($this->finalPrice) }} تومان</strong>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-co-cancel" wire:click="closeModals">
                                <i class="bi bi-x-lg me-1"></i>انصراف
                            </button>
                            <button type="submit" class="btn btn-co-submit fw-bold px-4"
                                wire:loading.attr="disabled" wire:target="checkout">
                                <span wire:loading wire:target="checkout"
                                    class="spinner-border spinner-border-sm ms-1"></span>
                                <i class="bi bi-check2-circle me-1"></i>ثبت نهایی فاکتور
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{--=============================== مودال موفقیت + چاپ فاکتور ==============================--}}
    @if ($showInvoiceModal)
        <div class="modal modal-blur fade show d-block pos-modal" tabindex="-1"
            style="background: rgba(15,23,42,.55);" wire:key="invoice-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header justify-content-center position-relative">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-check-circle-fill text-success fs-4 me-1"></i>
                            فاکتور با موفقیت ثبت شد
                        </h5>
                        <button type="button" class="btn-close position-absolute" style="left:1rem;top:1rem;"
                            wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        @if ($lastSale)
                            <div class="pos-summary-line">
                                <span class="text-muted">شماره فاکتور</span>
                                <strong>{{ $lastSale->invoice_number }}</strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">مشتری</span>
                                <strong>{{ $lastSale->customer->full_name ?? 'متفرقه' }}</strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">روش پرداخت</span>
                                <strong>
                                    @switch($lastSale->payment_type)
                                        @case('cash')نقدی
                                        @break
                                        @case('card')کارتخوان
                                        @break
                                        @case('mixed')ترکیبی
                                        @break
                                        @case('credit')نسیه
                                        @break
                                        @default{{ $lastSale->payment_type }}
                                    @endswitch
                                </strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">مبلغ کل</span>
                                <strong class="text-primary">{{ number_format($lastSale->final_price) }} تومان</strong>
                            </div>
                            @if ($lastSale->payment_type === 'credit' && (float) $lastSale->paid_amount < (float) $lastSale->final_price)
                                <div class="pos-summary-line">
                                    <span class="text-muted">باقی‌مانده نسیه</span>
                                    <strong class="text-danger">
                                        {{ number_format((float) $lastSale->final_price - (float) $lastSale->paid_amount) }} تومان
                                    </strong>
                                </div>
                            @endif
                        @endif

                        <p class="text-muted mt-2 mb-3">در صورت نیاز فاکتور را برای مشتری چاپ کنید.</p>

                        <a href="{{ route('invoice', $lastSaleId) }}" target="_blank"
                            class="btn btn-primary btn-lg rounded-4 px-5">
                            <i class="bi bi-printer me-1"></i> چاپ فاکتور فروش
                        </a>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            wire:click="closeModals">بستن و فروش بعدی</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--============ باز کردن خودکار فاکتور در تب جدید هنگام کلیک روی دکمه چاپ ============--}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-invoice', (event) => {
                const url = event.url ?? event[0]?.url;
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    </script>

</div>
