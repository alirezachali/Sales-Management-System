<?php $__env->startSection('title','ویرایش کالا'); ?>


<?php $__env->startSection('content'); ?>

<div class="card">


<div class="card-header">

<h3 class="card-title">
ویرایش کالا
</h3>

</div>


<div class="card-body">


<form method="POST"
      action="<?php echo e(route('products.update',$product)); ?>">


<?php echo csrf_field(); ?>

<?php echo method_field('PUT'); ?>



<div class="row">


<div class="col-md-6 mb-3">

<label class="form-label">
بارکد
</label>


<input type="text"
name="barcode"
class="form-control"
value="<?php echo e($product->barcode); ?>">


</div>



<div class="col-md-6 mb-3">

<label class="form-label">
نام کالا
</label>


<input type="text"
name="name"
class="form-control"
value="<?php echo e($product->name); ?>">


</div>



</div>


<button class="btn btn-primary">

ذخیره تغییرات

</button>


</form>


</div>


</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/products/edit.blade.php ENDPATH**/ ?>