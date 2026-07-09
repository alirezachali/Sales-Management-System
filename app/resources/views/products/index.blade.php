@extends('layouts.app')

@section('title','مدیریت کالاها')

@section('content')

<div class="row row-cards mb-4">


    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="subheader">
                        تعداد کالاها
                    </div>

                </div>


                <div class="h1 mb-0">
                    {{ $totalProducts }}
                </div>


            </div>

        </div>

    </div>



    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    کالاهای فعال
                </div>


                <div class="h1 mb-0 text-success">

                    {{ $activeProducts }}

                </div>

            </div>

        </div>

    </div>



    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    کالاهای غیرفعال
                </div>


                <div class="h1 mb-0 text-danger">

                    {{ $inactiveProducts }}

                </div>

            </div>

        </div>

    </div>



    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    موجودی کم
                </div>


                <div class="h1 mb-0 text-warning">

                    {{ $lowStockProducts }}

                </div>

            </div>

        </div>

    </div>


</div>

<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h4 class="mb-0">

            مدیریت کالاها

        </h4>

        <a href="{{ route('products.create') }}" class="btn btn-primary">

            افزودن کالا

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

            <tr>

                <th>#</th>

                <th>بارکد</th>

                <th>نام کالا</th>

                <th>دسته بندی</th>

                <th>قیمت فروش</th>

                <th>موجودی</th>

                <th width="170">عملیات</th>

            </tr>

            </thead>

            <tbody>

            @forelse($products as $product)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $product->barcode }}</td>

                    <td>{{ $product->name }}</td>

                    <td>{{ $product->category?->name }}</td>

                    <td>{{ number_format($product->sell_price) }}</td>

                    <td>{{ $product->stock }}</td>

                    <td>

                        <a href="{{ route('products.edit',$product) }}"
                           class="btn btn-warning btn-sm">

                            ویرایش

                        </a>

                        <form action="{{ route('products.destroy',$product) }}"
                              method="POST"
                              class="d-inline">

                            @csrf

                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('حذف شود؟')">

                                حذف

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        هیچ کالایی ثبت نشده است.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="mt-3">
            {{ $products->links() }}
        </div>

    </div>

</div>

@endsection