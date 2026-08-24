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

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">تعداد برندها</div>
                    <div class="h1 mb-0">
                        {{ $brands->total() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">برندهای فعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}1
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">برندهای غیرفعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">برندهای بدون تامین کننده</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- کارت جستجو --}}
    <div class="card glass-card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                            placeholder="جستجو بر اساس نام برند">
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- جدول برندها --}}
    <div class="card shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-tags-fill text-primary"></i>
                    مدیریت برندها
                </h3>
                <small class="text-muted">مدیریت اطلاعات برندها و تامین‌کنندگان مرتبط با هرکدام</small>
            </div>

            <button class="btn btn-primary" wire:click="openCreateModal" title="افزودن برند جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن برند
            </button>
        </div>


        {{-- جدول --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="55">ردیف</th>
                            <th>نام برند</th>
                            <th>توضیحات</th>
                            <th >تامین‌کنندگان</th>
                            <th width="80">وضعیت</th>
                            <th width="170">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr wire:key="brand-{{ $brand->id }}">
                                <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                <td class="fw-semibold">{{ $brand->name }}</td>
                                <td class="text-muted small">
                                    {{ $brand->description ? \Illuminate\Support\Str::limit($brand->description, 50) : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        {{ $brand->suppliers_count }} تامین‌کننده
                                    </span>
                                </td>
                                <td>
                                    @if ($brand->is_active)
                                        <span class="badge bg-success text-dark">فعال</span>
                                    @else
                                        <span class="badge bg-secondary text-dark">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary text-dark"
                                        wire:click="openEditModal({{ $brand->id }})">
                                        <i class="bi bi-pencil-fill"></i> ویرایش
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger text-dark"
                                        wire:click="confirmDelete({{ $brand->id }})">
                                        <i class="bi bi-trash-fill"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    @if ($search)
                                        نتیجه‌ای برای «{{ $search }}» پیدا نشد.
                                    @else
                                        هیچ برندی ثبت نشده است.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $brands->links() }}</div>
        </div>
    </div>

    {{-- مودال ساخت / ویرایش برند --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="brand-form-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $brandId ? 'ویرایش برند' : 'افزودن برند جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام برند <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">مسیر / لینک لوگو</label>
                                    <input type="text" class="form-control" wire:model="logo"
                                        placeholder="مثلاً: logos/brand.png">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea class="form-control" rows="2" wire:model="description"></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">تامین‌کنندگان این برند</label>
                                    <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                        @forelse ($allSuppliers as $supplier)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="supplier_{{ $supplier->id }}" wire:model="selectedSuppliers"
                                                    value="{{ $supplier->id }}">
                                                <label class="form-check-label" for="supplier_{{ $supplier->id }}">
                                                    {{ $supplier->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">هیچ تامین‌کننده فعالی ثبت نشده است.
                                            </p>
                                        @endforelse
                                    </div>
                                    <small class="text-muted">می‌تونی چند تامین‌کننده رو همزمان انتخاب کنی.</small>
                                </div>

                                <div class="col-12 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="brand_is_active"
                                        wire:model="is_active">
                                    <label class="form-check-label" for="brand_is_active">فعال</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $brandId ? 'ذخیره تغییرات' : 'ثبت برند' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال تایید حذف --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="brand-delete-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف برند</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این برند مطمئن هستید؟ اگه این برند به تامین‌کننده یا محصول دیگه‌ای متصل باشه،
                        ممکنه
                        حذف با خطا مواجه بشه.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
