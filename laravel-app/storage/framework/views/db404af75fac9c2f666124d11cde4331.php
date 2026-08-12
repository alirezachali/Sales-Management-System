<?php $__env->startSection('title','گردش انبار'); ?>


<?php $__env->startSection('content'); ?>


<div class="card">


    <div class="card-header">

        <h3 class="card-title">

            گردش کالا:
            <?php echo e($product->name); ?>


        </h3>


    </div>



    <div class="card-body">


        <div class="alert alert-info">

            موجودی فعلی:

            <strong>
                <?php echo e($product->stock); ?>

            </strong>

            <?php echo e($product->unit); ?>


        </div>



        <div class="table-responsive">


            <table class="table table-vcenter">


                <thead>

                <tr>

                    <th>
                        تاریخ
                    </th>


                    <th>
                        نوع عملیات
                    </th>


                    <th>
                        مقدار
                    </th>


                    <th>
                        توضیحات
                    </th>


                </tr>

                </thead>



                <tbody>


                <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tr>


                    <td>

                        <?php echo e($movement->created_at); ?>


                    </td>


                    <td>


                        <?php switch($movement->type):


                            case ('initial'): ?>

                                <span class="badge bg-info">

                                    موجودی اولیه

                                </span>

                            <?php break; ?>



                            <?php case ('purchase'): ?>

                                <span class="badge bg-success">

                                    خرید

                                </span>

                            <?php break; ?>



                            <?php case ('sale'): ?>

                                <span class="badge bg-danger">

                                    فروش

                                </span>

                            <?php break; ?>



                            <?php case ('adjust'): ?>

                                <span class="badge bg-warning">

                                    اصلاح

                                </span>

                            <?php break; ?>


                        <?php endswitch; ?>


                    </td>



                    <td>

                        <?php echo e($movement->quantity); ?>


                    </td>



                    <td>

                        <?php echo e($movement->description); ?>


                    </td>


                </tr>


                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                </tbody>


            </table>


        </div>


        <?php echo e($movements->links()); ?>



    </div>


</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/products/stock.blade.php ENDPATH**/ ?>