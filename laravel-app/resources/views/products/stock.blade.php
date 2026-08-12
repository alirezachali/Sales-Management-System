@extends('layouts.app')
@section('title', 'گردش انبار')
@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                گردش کالا:
                {{ $product->name }}
            </h3>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                موجودی فعلی:
                <strong>
                    {{ $product->stock }}
                </strong>
                {{ $product->unit }}
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter">

                    <thead>
                        <tr>
                            <th> تاریخ</th>
                            <th> نوع عملیات</th>
                            <th> مقدار</th>
                            <th> توضیحات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at }}</td>
                                <td>
                                    @switch($movement->type)
                                        @case('initial')
                                            <span class="badge bg-info">
                                                موجودی اولیه
                                            </span>
                                        @break
                                        @case('purchase')
                                            <span class="badge bg-success">
                                                خرید
                                            </span>
                                        @break
                                        @case('sale')
                                            <span class="badge bg-danger">
                                                فروش
                                            </span>
                                        @break
                                        @case('adjust')
                                            <span class="badge bg-warning">
                                                اصلاح
                                            </span>
                                        @break
                                    @endswitch
                                </td>
                                <td>{{ $movement->quantity }}</td>
                                <td>{{ $movement->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

            <div class="mt-3">{{ $movements->links() }}</div>
            
        </div>

    </div>
@endsection
