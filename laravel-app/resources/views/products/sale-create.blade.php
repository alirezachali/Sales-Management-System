@extends('layouts.app')
@section('title', 'خروج کالا از انبار')
@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                خروج کالا از انبار
            </h3>
        </div>

        <div class="card-body">

            <div class="alert alert-warning">
                کالا:
                <strong>
                    {{ $product->name }}
                </strong>
                <br>
                موجودی فعلی:
                <strong>
                    {{ $product->stock }}
                </strong>
                {{ $product->unit }}
            </div>

            <form method="POST" action="{{ route('products.sale.store', $product) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        تعداد خروج
                    </label>
                    <input type="number" step="0.001" name="quantity" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        توضیحات
                    </label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <button class="btn btn-danger">
                    ثبت خروج کالا
                </button>

            </form>

        </div>
        
    </div>
@endsection
