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

                <input
                    id="barcode"
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

            <button
                id="checkout-btn"
                class="btn btn-success btn-lg">

                ثبت فروش

            </button>

        </div>

        </div>

    </div>

</div>

@push('scripts')
<script>

let cart = [];
function renderCart() {

    let tbody = document.getElementById('cart-body');

    tbody.innerHTML = '';

    let total = 0;

    cart.forEach((item, index) => {

        let rowTotal = item.quantity * item.price;

        total += rowTotal;

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

document
.getElementById('checkout-btn')
.addEventListener('click', checkout);

function checkout()
{

    if(cart.length === 0){

        alert('سبد خرید خالی است');

        return;
    }

    fetch('/pos/checkout', {

        method: 'POST',

        headers: {

            'Content-Type': 'application/json',

            'X-CSRF-TOKEN':
                document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                .content

        },

        body: JSON.stringify({
            cart: cart
        })

    })
    .then(res => res.json())
    .then(data => {

        console.log(data);

    });

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

this.value = '';

this.focus();

        });

});

</script>
@endpush
@endsection