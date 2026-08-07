<!-- Payment Modal -->
<div
    class="modal fade"
    id="paymentModal"
    tabindex="-1"
    aria-labelledby="paymentModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header -->
            <div class="modal-header border-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">
                        تکمیل فروش
                    </h5>

                    <small class="text-muted">
                        اطلاعات پرداخت را بررسی و فروش را نهایی کنید
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="بستن"
                ></button>
            </div>

            <div class="modal-body px-4 pb-4">

                <!-- Summary -->
                <div class="payment-summary mb-4">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <div class="text-muted small">
                                    مشتری
                                </div>

                                <div
                                    id="payment-customer-name"
                                    class="fw-bold mt-1"
                                >
                                    مشتری عمومی
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <div class="text-muted small">
                                    مبلغ قابل پرداخت
                                </div>

                                <div
                                    id="payment-total"
                                    class="payment-total mt-1"
                                >
                                    ۰ تومان
                                </div>
                            </div>
                        </div>

                    </div>

                </div>


                <!-- Payment Type -->
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        روش پرداخت
                    </label>

                    <div class="row g-2">

                        <div class="col-4">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_type"
                                id="payment-cash"
                                value="cash"
                                checked
                            >

                            <label
                                class="payment-method"
                                for="payment-cash"
                            >
                                <span class="payment-method-icon">
                                    💵
                                </span>

                                <span>
                                    <strong>نقدی</strong>
                                    <small>پرداخت نقدی</small>
                                </span>
                            </label>
                        </div>


                        <div class="col-4">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_type"
                                id="payment-card"
                                value="card"
                            >

                            <label
                                class="payment-method"
                                for="payment-card"
                            >
                                <span class="payment-method-icon">
                                    💳
                                </span>

                                <span>
                                    <strong>کارت</strong>
                                    <small>کارت بانکی</small>
                                </span>
                            </label>
                        </div>


                        <div class="col-4">
                            <input
                                type="radio"
                                class="btn-check"
                                name="payment_type"
                                id="payment-credit"
                                value="credit"
                            >

                            <label
                                class="payment-method"
                                for="payment-credit"
                            >
                                <span class="payment-method-icon">
                                    🧾
                                </span>

                                <span>
                                    <strong>نسیه</strong>
                                    <small>حساب مشتری</small>
                                </span>
                            </label>
                        </div>

                    </div>

                </div>


                <!-- Paid Amount -->
                <div class="mb-4">

                    <label
                        for="paid-amount"
                        class="form-label fw-bold"
                    >
                        مبلغ دریافتی
                    </label>

                    <div class="input-group input-group-lg">

                        <input
                            type="number"
                            id="paid-amount"
                            class="form-control text-end"
                            min="0"
                            step="1000"
                            placeholder="مبلغ دریافتی را وارد کنید"
                        >

                        <span class="input-group-text">
                            تومان
                        </span>

                    </div>

                </div>


                <!-- Payment Result -->
                <div
                    id="payment-result"
                    class="payment-result"
                >

                    <div>
                        <span class="text-muted">
                            باقی‌مانده
                        </span>

                        <strong id="payment-remaining">
                            ۰ تومان
                        </strong>
                    </div>

                    <div>
                        <span class="text-muted">
                            مبلغ برگشتی
                        </span>

                        <strong id="payment-change">
                            ۰ تومان
                        </strong>
                    </div>

                </div>


                <!-- Error -->
                <div
                    id="payment-error"
                    class="alert alert-danger d-none mt-3 mb-0"
                ></div>

            </div>


            <!-- Footer -->
            <div class="modal-footer border-0 px-4 pb-4 pt-0">

                <button
                    type="button"
                    class="btn btn-light px-4"
                    data-bs-dismiss="modal"
                >
                    انصراف
                </button>

                <button
                    type="button"
                    id="confirm-checkout-btn"
                    class="btn btn-success px-5"
                >
                    تأیید و ثبت فروش
                </button>

            </div>

        </div>
    </div>
</div>