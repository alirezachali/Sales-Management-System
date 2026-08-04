@extends('layouts.app')


@section('title','ویرایش کالا')


@section('content')

<div class="card">


<div class="card-header">

<h3 class="card-title">
ویرایش کالا
</h3>

</div>


<div class="card-body">


<form method="POST"
      action="{{ route('products.update',$product) }}">


@csrf

@method('PUT')



<div class="row">


<div class="col-md-6 mb-3">

<label class="form-label">
بارکد
</label>


<input type="text"
name="barcode"
class="form-control"
value="{{ $product->barcode }}">


</div>



<div class="col-md-6 mb-3">

<label class="form-label">
نام کالا
</label>


<input type="text"
name="name"
class="form-control"
value="{{ $product->name }}">


</div>



</div>


<button class="btn btn-primary">

ذخیره تغییرات

</button>


</form>


</div>


</div>


@endsection