<?php $__env->startSection('title', 'صندوق فروش'); ?>
<?php $__env->startSection('content'); ?>

    <!-- Success Alert Section -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    <?php endif; ?>

    <!-- Error Alert Section -->
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    <?php endif; ?>

    <div class="container-fluid">
        <div class="card">

            <!-- بخش هدر صفحه صندوق فروش -->
            <div class="card-header">
                <h2>🛒 صندوق فروش</h2>
            </div>

            <!-- بخش بدنه صفحه صندوق فروش -->
            <div class="card-body">

                <!-- بخش ورود بارکد محصولات -->
                <div class="mb-3">
                    <label class="form-label" for="barcode">
                        بارکد کالا
                    </label>
                    <input id="barcode" class="form-control form-control-lg" placeholder="بارکد را اسکن کنید"
                        autocomplete="off" autofocus>
                </div>

                <!-- بخش انتخاب مشتری -->
                <div class="mb-3 position-relative">
                    <label class="form-label" for="customer-search">
                        مشتری
                    </label>
                    <input id="customer-search" type="text" class="form-control"
                        placeholder="نام یا شماره موبایل مشتری را جستجو کنید..." autocomplete="off">
                    <input type="hidden" id="customer-id" value="">
                    <div id="customer-results" class="list-group position-absolute w-100 shadow d-none"
                        style="z-index: 1050;">
                    </div>
                </div>

                <!-- شروع کدهای جدول آیتم های سبد خرید -->
                <table class="table table-bordered">

                    <!-- هدر جدول -->
                    <thead>
                        <tr>
                            <th>کالا</th>
                            <th width="120">تعداد</th>
                            <th width="150">قیمت</th>
                            <th width="170">جمع</th>
                            <th width="60">عملیات</th>
                        </tr>
                    </thead>

                    <!-- بدنه جدول -->
                    <tbody id="cart-body"></tbody>

                </table>
                <!-- پایان کدهای جدول آیتم های سبد خرید -->

                <!-- بخش نمایش جمع کل مبلغ سبدخرید -->
                <div class="text-end">
                    <h2>
                        جمع کل:
                        <span id="grand-total">
                            0
                        </span>
                        تومان
                    </h2>
                </div>

                <!-- دکمه ثبت فروش -->
                <div class="mt-3 text-end">
                    <button id="checkout-btn" class="btn btn-success btn-lg">
                        ثبت فروش
                    </button>
                </div>

            </div>
        </div>

        <!-- اینپورت کردن مودال پرداخت -->
        <?php echo $__env->make('sales.modals.payment', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // شروع کدهای مربوط به جستجوی مشتری
                const customerSearch = document.getElementById('customer-search');
                const customerResults = document.getElementById('customer-results');
                let customerSearchTimeout = null;
                customerSearch.addEventListener('input', function() {
                    const search = this.value.trim();
                    clearTimeout(customerSearchTimeout);

                    if (search.length < 2) {
                        customerResults.innerHTML = '';
                        customerResults.classList.add('d-none');
                        return;
                    }
                    customerSearchTimeout = setTimeout(() => {
                        searchCustomers(search);
                    }, 300);
                });

                async function searchCustomers(search) {
                    try {
                        const response = await fetch(
                            `/customers/search?search=${encodeURIComponent(search)}`, {
                                headers: {
                                    'Accept': 'application/json',
                                }
                            }
                        );

                        if (!response.ok) {
                            throw new Error('Customer search failed.');
                        }

                        const data = await response.json();
                        renderCustomerResults(data.customers);
                    } catch (error) {
                        console.error(error);
                        customerResults.innerHTML = '';
                        customerResults.classList.add('d-none');
                    }
                }

                function renderCustomerResults(customers) {
                    customerResults.innerHTML = '';
                    if (!customers.length) {
                        customerResults.innerHTML = `
                <div class="list-group-item text-muted">
                    مشتری‌ای پیدا نشد.
                </div>
            `;
                        customerResults.classList.remove('d-none');
                        return;
                    }

                    customers.forEach(customer => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                <div class="fw-bold">${customer.name}</div>
                <small class="text-muted">${customer.mobile ?? ''}</small>
            `;
                        item.addEventListener('click', function() {
                            selectCustomer(customer);
                        });
                        customerResults.appendChild(item);
                    });

                    function selectCustomer(customer) {
                        const customerSearch = document.getElementById('customer-search');
                        const customerId = document.getElementById('customer-id');
                        const customerResults = document.getElementById('customer-results');
                        customerSearch.value = customer.name;
                        customerId.value = customer.id;
                        customerResults.innerHTML = '';
                        customerResults.classList.add('d-none');
                        console.log('Selected customer:', customer);
                    }
                    customerResults.classList.remove('d-none');
                }
                // پایان کدهای مربوط به جستجوی مشتری

                // تعریف آرایه برای ذخیره آیتم های سبد خرید
                let cart = [];

                // تابع حساب کردن قیمت محصول نسبت به تعداد آن
                function calculateTotal() {
                    return cart.reduce((sum, item) => {
                        return sum + (item.price * item.quantity);
                    }, 0);
                }

                // تابع اضافه کردن محصولات به جدول سبد خرید
                function renderCart() {
                    let tbody = document.getElementById('cart-body');
                    tbody.innerHTML = '';
                    cart.forEach((item) => {
                        let rowTotal = item.quantity * item.price;
                        tbody.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="decrease(${item.id})">
                            -
                        </button>
                        <strong class="mx-2">
                            ${item.quantity}
                        </strong>
                        <button class="btn btn-sm btn-success" onclick="increase(${item.id})">
                            +
                        </button>
                    </td>
                    <td>${Number(item.price).toLocaleString()}</td>
                    <td>${Number(rowTotal).toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="removeItem(${item.id})">
                            ✖
                        </button>
                    </td>
                </tr>
            `;
                    });
                    const total = calculateTotal();
                    document.getElementById('grand-total').innerHTML = Number(total).toLocaleString();
                }

                // گرفتن اطلاعات محصولات از بک اند
                const barcode = document.getElementById('barcode');
                barcode.addEventListener('keydown', function(e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    fetch(`/pos/product?barcode=${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('کالا پیدا نشد');
                                return;
                            }
                            let product = data.product;
                            let found = cart.find(item => item.id == product.id);

                            if (found) {
                                found.quantity++;
                            } else {
                                cart.push({
                                    id: product.id,
                                    name: product.name,
                                    price: Number(product.price),
                                    quantity: 1
                                });
                            }
                            renderCart();
                            this.value = '';
                            this.focus();
                        });
                });

                // تابع حذف محصول از سبد خرید
                function removeItem(id) {
                    cart = cart.filter(item => item.id != id);
                    renderCart();
                }

                // تابع افزایش تعداد محصول
                function increase(id) {
                    let item = cart.find(x => x.id == id);
                    item.quantity++;
                    renderCart();
                }

                // تابع کاهش تعداد محصول
                function decrease(id) {
                    let item = cart.find(x => x.id == id);
                    item.quantity--;
                    if (item.quantity <= 0) {
                        removeItem(id);
                        return;
                    }

                    renderCart();
                }

                // کدهای مربوط به دکمه ثبت فروش و بازکردن مودال پرداخت
                const checkoutButton = document.getElementById('checkout-btn');
                const paymentModalElement = document.getElementById('paymentModal');
                const paymentModal = new bootstrap.Modal(paymentModalElement);
                checkoutButton.addEventListener('click', function() {
                    if (cart.length === 0) {
                        alert('سبد خرید خالی است.');
                        return;
                    }
                    preparePaymentModal();
                    paymentModal.show();
                });

                // 
                function preparePaymentModal() {
                    const total = calculateTotal();
                    const customerName =
                        document.getElementById('customer-search').value.trim();
                    document.getElementById('payment-total').textContent =
                        formatMoney(total) + ' تومان';
                    document.getElementById('payment-customer-name').textContent =
                        customerName || 'مشتری عمومی';
                    const paidAmount = document.getElementById('paid-amount');
                    paidAmount.value = total;
                    calculatePaymentResult();
                    document.getElementById('payment-error')
                        .classList.add('d-none');
                }

                // تابع مربوط به فرمت مبلغ
                function formatMoney(amount) {
                    return Number(amount).toLocaleString('fa-IR');
                }

                // تابع محاسبه مبلغ دریافتی از مشتری
                const paidAmountInput =
                    document.getElementById('paid-amount');
                paidAmountInput.addEventListener(
                    'input',
                    calculatePaymentResult
                );

                function calculatePaymentResult() {
                    const total = calculateTotal();
                    const paidAmount =
                        Number(paidAmountInput.value) || 0;
                    const remaining =
                        Math.max(total - paidAmount, 0);
                    const change =
                        Math.max(paidAmount - total, 0);
                    document.getElementById('payment-remaining').textContent =
                        formatMoney(remaining) + ' تومان';
                    document.getElementById('payment-change').textContent =
                        formatMoney(change) + ' تومان';
                }

                // تابع مربوط به پرداخت به روش نسیه یا قرضی
                const paymentTypeInputs =
                    document.querySelectorAll(
                        'input[name="payment_type"]'
                    );

                paymentTypeInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const paidAmount =
                            document.getElementById('paid-amount');
                        if (this.value === 'credit') {
                            paidAmount.value = 0;
                            paidAmount.disabled = true;
                        } else {
                            paidAmount.disabled = false;
                            if (!paidAmount.value) {
                                paidAmount.value = calculateTotal();
                            }
                        }
                        calculatePaymentResult();
                    });
                });

                // اجرای تابع فرستادن اطلاعات به بک اند توسط دکمه موجود در مودال پرداخت
                document
                    .getElementById('confirm-checkout-btn')
                    .addEventListener('click', checkout);

                // تابع مربوط به فرستادن اطلاعات سبدخرید به بک اند
                async function checkout() {
                    const paymentType =
                        document.querySelector(
                            'input[name="payment_type"]:checked'
                        ).value;

                    const paidAmount =
                        Number(document.getElementById('paid-amount').value) || 0;

                    const payload = {
                        cart: cart.map(item => ({
                            id: item.id,
                            quantity: item.quantity,
                            price: item.price,
                        })),

                        discount: 0,
                        payment_type: paymentType,
                        customer_id: document.getElementById('customer-id').value || null,
                        paid_amount: paidAmount,
                    };

                    console.log('Checkout payload:', payload);
                    try {
                        const response = await fetch('/pos/checkout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(
                                data.message ?? 'ثبت فروش انجام نشد.'
                            );
                        }

                        console.log('Checkout successful:', data);
                        alert('فروش با موفقیت ثبت شد.');
                        cart = [];
                        renderCart();
                        document.getElementById('customer-search').value = '';
                        document.getElementById('customer-id').value = '';

                    } catch (error) {
                        console.error('Checkout error:', error);
                        alert(error.message);
                    }
                }
            });
        </script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/sales/pos.blade.php ENDPATH**/ ?>