<?php $__env->startSection('title', 'مدیریت دسته‌بندی محصولات'); ?>

<?php $__env->startSection('content'); ?>

<div class="container">

    <div class="card dashboard-card">


    <!-- Success Alert Section -->
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <?php echo e(session('success')); ?>


        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                title="یستن"></button>

    </div>
<?php endif; ?>

<!-- Error Alert Section -->
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        <?php echo e(session('error')); ?>


        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                title="یستن"></button>

    </div>
<?php endif; ?>



        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-tags-fill me-2"></i>
                مدیریت دسته‌بندی محصولات
            </h4>

            <button class="btn btn-success" id="btnAddCategory" title="افزودن دسته بندی جدید">
                <i class="bi bi-plus-circle"></i>
                افزودن دسته‌بندی
            </button>

        </div>

        <div class="card-body">

            <div class="row mb-4">

                <div class="col-md-4">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text"
                               class="form-control"
                               placeholder="جستجوی دسته‌بندی...">

                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <!-- جدول لیست دسته بندی ها -->
                <table class="table table-hover align-middle text-center">

                    <thead class="table">

                        <tr>

                            <th width="70">ردیف</th>

                            <th>نام دسته‌بندی</th>

                            <th>توضیحات</th>

                            <th width="140">تعداد کالا</th>

                            <th width="180">تاریخ ایجاد</th>

                            <th>وضعیت</th>

                            <th width="140">عملیات</th>

                        </tr>

                    </thead>

                    <tbody>

                    <!-- شروع حلقه -->
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <!-- ستون ردیف -->
                            <td>
                                <?php echo e($loop->iteration + (($categories->currentPage()-1) * $categories->perPage())); ?>

                            </td>

                            <!-- ستون نام دسته بندی -->
                            <td class="text-start">
                                <?php echo e($category->name); ?>

                            </td>

                            <!-- ستون وضعیت -->
                            <td>
                                <?php if($category->description): ?>
                                    <?php echo e($category->description); ?>

                                <?php else: ?>
                                    <span class="text-muted">---</span>
                                <?php endif; ?>
                            </td>

                            <!-- ستون تعداد کالا -->
                            <td>
                                <?php if($category->products_count): ?>
                                    <span class="badge bg-primary">
                                        <?php echo e($category->products_count); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        0
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- ستون تاریخ ایجاد -->
                            <td>
                                <?php echo e($category->created_at); ?>

                            </td>

                            <!-- ستون وضعیت -->
                            <td>
                                <?php if($category->is_active): ?>
                                    <span class="badge bg-success">
                                        فعال
                                    </span>
                               <?php else: ?>
                                   <span class="badge bg-danger">
                                       غیرفعال
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- ستون عملیات -->
                            <td>
                                <div class="btn-group">

                                <!-- دکمه ویرایش دسته بندی -->
                                <button type="button"
                                       class="btn btn-sm btn-primary btn-edit"
                                       data-id="<?php echo e($category->id); ?>"
                                       data-name="<?php echo e($category->name); ?>"
                                       data-description="<?php echo e($category->description); ?>"
                                       data-active="<?php echo e($category->is_active); ?>"
                                       title="ویرایش این دسته بندی">

                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- دکمه حذف دسته بندی -->
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-delete"
                                        data-id="<?php echo e($category->id); ?>"
                                        data-name="<?php echo e($category->name); ?>"
                                        title="حذف کردن این دسته بندی">

                                    <i class="bi bi-trash"></i>

                                </button>
                                
                                </div>
                            </td>

                        </tr>

                        <!-- اگر اطلاعاتی در دیتابیس برای نمایش وجود نداشته باشد. اطلاعات زیر را نمایش میدهد -->
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-5">
                                <i class="bi bi-inbox fs-1 text-secondary"></i>
                                <div class="mt-3">
                                    هنوز هیچ دسته‌بندی ثبت نشده است.
                                </div>
                            </td>
                        </tr>

                    <?php endif; ?>
                    <!-- پایان حلقه -->

                    </tbody>

                </table>

            </div>

            <!--  -->
            <div class="mt-4 d-flex justify-content-center">

                <?php echo e($categories->links()); ?>


            </div>

        </div>

    </div>

</div>

<!-- include External Modals File -->
<?php echo $__env->make('categories.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('categories.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('categories.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php $__env->stopSection(); ?>


<!-- Start Scripts Section -->
<?php $__env->startSection('scripts'); ?>
<script>

    // Start Modals Script
    document.addEventListener('DOMContentLoaded', function () {

        // Start Create Modal
        const createModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
        document.getElementById('btnAddCategory').addEventListener('click', function () {

            document.getElementById('createCategoryForm').reset();
            createModal.show();

        });
        // End Create Modal


        // Start Edit Modal
        const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        document.querySelectorAll('.btn-edit').forEach(btn => {

            btn.addEventListener('click', function () {

                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('editCategoryForm').action = '/categories/' + this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_description').value = this.dataset.description ?? '';
                document.getElementById('edit_is_active').checked = this.dataset.active == 1;
                editModal.show();

            });
        });
        // End Edit Modal 


        // Start Delete Modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        document.querySelectorAll('.btn-delete').forEach(btn => {

            btn.addEventListener('click', function () {

                document.getElementById('delete_id').value = this.dataset.id;
                document.getElementById('deleteCategoryForm').action = '/categories/' + this.dataset.id;
                document.getElementById('delete_category_name').innerText = this.dataset.name;
                deleteModal.show();

            });

        });
        // End Delete Modal

    });
    // End Modals Script 

</script>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/categories/index.blade.php ENDPATH**/ ?>