@extends('layouts.app')

@section('title','صندوق فروش')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h2>🛒 صندوق فروش</h2>

        </div>

        <div class="card-body">

            <div class="mb-3">

                <label class="form-label">

                    بارکد کالا

                </label>

                <input id="barcode"
                       class="form-control form-control-lg"
                       placeholder="بارکد را اسکن کنید"
                       autocomplete="off"
                       autofocus>

            </div>

            <table class="table table-bordered">

                <thead>

                <tr>

                    <th>کالا</th>

                    <th width="120">تعداد</th>

                    <th width="150">قیمت</th>

                    <th width="170">جمع</th>

                    <th width="60">عملیات</th>

                </tr>

                </thead>

                <tbody id="cart-body">

                </tbody>

            </table>

            <div class="text-end">

                <h2>

                    جمع کل:

                    <span id="grand-total">

                        0

                    </span>

                    تومان

                </h2>

            </div>


         <div class="mt-3 text-end">

            <button id="checkout-btn"
                    class="btn btn-success btn-lg">

                    ثبت فروش

            </button>

        </div>

        <div class="modal fade" id="paymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            پرداخت فاکتور
                        </h5>

                        <button class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label>جمع کل</label>
                            <input
                                id="totalPrice"
                                class="form-control"
                                readonly>

                        </div>

                        <div class="mb-3">

                            <label>تخفیف</label>
                            <input
                                id="discount"
                                type="number"
                                class="form-control"
                                value="0">

                        </div>

                        <div class="mb-3">

                            <label>مبلغ نهایی</label>
                            <input
                                id="finalPrice"
                                class="form-control"
                                readonly>

                        </div>

                        <div class="mb-3">

                            <label>مبلغ دریافتی</label>
                            <input
                                id="paidAmount"
                                type="number"
                                class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>باقی‌مانده</label>
                            <input
                                id="changeAmount"
                                class="form-control"
                                readonly>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                                انصراف

                        </button>

                        <button
                            class="btn btn-success"
                            id="confirmSale">

                                ثبت فروش

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <div id="success-alert" 
             class="alert alert-success d-none position-fixed top-0 end-0 m-3 shadow" 
             style="z-index:9999">

            ✅ فروش با موفقیت ثبت شد.

        </div>

    </div>

</div>

@push('scripts')
<script>


let cart = [];


function calculateTotal()
{
    return cart.reduce((sum, item) => {

        return sum + (item.price * item.quantity);

    }, 0);
}


function renderCart() {

    let tbody = document.getElementById('cart-body');

    tbody.innerHTML = '';

    cart.forEach((item) => {

    let rowTotal = item.quantity * item.price;

        tbody.innerHTML += `

        <tr>

            <td>${item.name}</td>

            <td>

                <button
                    class="btn btn-sm btn-secondary"
                    onclick="decrease(${item.id})">

                    -

                </button>

                <strong class="mx-2">

                    ${item.quantity}

                </strong>

                <button
                    class="btn btn-sm btn-success"
                    onclick="increase(${item.id})">

                    +

                </button>

            </td>

            <td>${Number(item.price).toLocaleString()}</td>

            <td>${Number(rowTotal).toLocaleString()}</td>

            <td>
                <button
                    class="btn btn-sm btn-danger"
                    onclick="removeItem(${item.id})">

                    ✖

                </button>
            </td>

        </tr>

        `;

    });

    const total = calculateTotal();
    document.getElementById('grand-total').innerHTML =
        Number(total).toLocaleString();

}



function removeItem(id)
{
    cart = cart.filter(item => item.id != id);

    renderCart();
}

function increase(id)
{
    let item = cart.find(x => x.id == id);

    item.quantity++;

    renderCart();
}

function decrease(id)
{
    let item = cart.find(x => x.id == id);

    item.quantity--;

    if(item.quantity <= 0){

        removeItem(id);

        return;
    }

    renderCart();
}



const barcode = document.getElementById('barcode');

barcode.addEventListener('keydown', function (e) {

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





const modal = new bootstrap.Modal(
    document.getElementById('paymentModal')
);

document.getElementById('checkout-btn').addEventListener('click', function () {

    const total = calculateTotal();

    document.getElementById('totalPrice').value = total.toLocaleString();

    document.getElementById('finalPrice').value = total.toLocaleString();

    modal.show();

});


document.getElementById('confirmSale').addEventListener('click', function () {

    fetch('/pos/checkout', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        cart: cart,
        discount: Number(document.getElementById('discount').value),
        payment_type: 'cash'
    })
    })
    .then(async response => {

        console.log('STATUS:', response.status);
        const text = await response.text();
        console.log(text);

        const success = document.getElementById('success-alert');

        success.classList.remove('d-none');

        setTimeout(() => {
        success.classList.add('d-none');
        },3000);

        cart = [];
        renderCart();

        modal.hide();

        document.getElementById('barcode').focus();

    })
    .catch(error => {

    console.error(error);

    });

});



</script>
@endpush
@endsection