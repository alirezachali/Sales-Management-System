<div class="modal fade" id="labelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upc-scan"></i>
                    چاپ لیبل کالا
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-center label-cart">
                    <div id="label-container" class="d-flex flex-wrap justify-content-center gap-2">

                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">
                        تعداد چاپ
                    </label>
                    <input type="number" id="label_quantity" class="form-control" value="1" min="1">
                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>

                <button class="btn btn-primary" id="print-label-btn">
                    <i class="bi bi-printer"></i>
                    چاپ
                </button>

            </div>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/products/modals/label.blade.php ENDPATH**/ ?>