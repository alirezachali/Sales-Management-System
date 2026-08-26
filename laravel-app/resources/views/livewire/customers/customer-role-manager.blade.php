<div dir="rtl">

    {{-- Success/Error Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-award-fill text-primary"></i>
                    رده‌های باشگاه مشتریان
                </h3>
                <small class="text-muted">مدیریت رده های باشگاه مشتریان و مدیریت درصد تخفیف برای هر رده</small>
            </div>

            <div class="d-flex gap-3">

                <a href="{{ route('customers.index') }}">
                    <button class="btn btn-info text-dark" title="بازگشت به لیست مشتریان">
                        <i class="bi bi-arrow-right"></i>
                        بازگشت
                    </button>
                </a>

                <button type="button" class="btn btn-primary" wire:click="openCreateModal"
                    title="افزودن رده جدید به باشگاه مشتریان">
                    <i class="bi bi-plus-circle"></i>
                    رده جدید
                </button>

            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ترتیب</th>
                        <th>عنوان رده</th>
                        <th>حداقل تعداد خرید</th>
                        <th>حداقل مبلغ کل خرید</th>
                        <th width="80">درصد تخفیف</th>
                        <th width="80">تعداد مشتریان</th>
                        <th width="80">وضعیت</th>
                        <th width="100">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr wire:key="role-{{ $role->id }}">
                            <td>{{ $role->sort_order }}</td>
                            <td>
                                <span class="badge bg-{{ $role->color }} text-dark">
                                    <i class="bi {{ $role->icon }}"></i>
                                    {{ $role->name }}
                                </span>
                                @if ($role->is_default)
                                    <span class="badge bg-light text-dark border">پیش‌فرض</span>
                                @endif
                            </td>
                            <td>{{ number_format($role->min_purchase_count) }} خرید</td>
                            <td>{{ number_format($role->min_purchase_amount) }} {{ setting('currency', '') }}</td>
                            <td>{{ $role->discount_percent }}٪</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $role->customers_count }} نفر</span>
                            </td>
                            <td>
                                @if ($role->is_active)
                                    <span class="badge bg-success text-dark">فعال</span>
                                @else
                                    <span class="badge bg-danger text-dark">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $role->id }})" title="ویرایش رده">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-dark"
                                    wire:click="confirmDelete({{ $role->id }})" title="حذف رده">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                هیچ رده‌ای برای باشگاه مشتریان ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش رده ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="role-form-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش رده باشگاه مشتریان' : 'رده جدید باشگاه مشتریان' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">عنوان رده</label>
                                    <input type="text" wire:model="name" placeholder="مثلاً: طلایی"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">آیکن (Bootstrap Icons)</label>
                                    <input type="text" wire:model="icon" class="form-control" placeholder="bi-award">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">رنگ نشان</label>
                                    <select wire:model="color" class="form-select">
                                        <option value="secondary">خاکستری</option>
                                        <option value="success">سبز</option>
                                        <option value="primary">آبی</option>
                                        <option value="info">آبی روشن</option>
                                        <option value="warning">زرد</option>
                                        <option value="danger">قرمز</option>
                                        <option value="dark">مشکی</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ترتیب نمایش (رتبه)</label>
                                    <input type="number" wire:model="sort_order" class="form-control">
                                    <div class="form-text">عدد بزرگ‌تر یعنی رتبه‌ی بالاتر.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">درصد تخفیف خودکار</label>
                                    <input type="number" step="0.01" wire:model="discount_percent"
                                        class="form-control @error('discount_percent') is-invalid @enderror">
                                    @error('discount_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="role-is-active">
                                        <label class="form-check-label" for="role-is-active">فعال</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">حداقل تعداد خرید برای رسیدن به این رده</label>
                                    <input type="number" wire:model="min_purchase_count" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">حداقل مبلغ کل خرید برای رسیدن به این رده</label>
                                    <input type="number" wire:model="min_purchase_amount" class="form-control">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">توضیحات</label>
                                    <textarea wire:model="description" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" wire:model="is_default" class="form-check-input"
                                            id="role-is-default">
                                        <label class="form-check-label" for="role-is-default">
                                            رده‌ی پیش‌فرض برای مشتریان جدید
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                ذخیره
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="role-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف رده</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این رده مطمئن هستید؟ مشتریانی که در این رده هستند، بدون رده باقی می‌مانند و در
                        بازمحاسبه‌ی بعدی، دوباره رده‌بندی می‌شوند.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
