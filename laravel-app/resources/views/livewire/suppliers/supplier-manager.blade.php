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
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد تامین کنندگان</div>
                    <div class="h1 mb-0">
                        {{ $suppliers->total() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تامین کنندگان فعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}1
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تامین کنندگان غیرفعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تامین کنندگان بدون برند</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Search Card --}}
    <div class="card glass-card mb-4 border-3">
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


    {{-- جدول تامین کنندگان --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-bus-front-fill text-primary"></i>
                    مدیریت تامین کنندگان
                </h3>
                <small class="text-muted">مدیریت اطلاعات تامین‌کنندگان </small>
            </div>

            <button class="btn btn-primary" wire:click="openCreateModal" title="افزودن تامین کننده جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن تامین کننده
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="55">ردیف</th>
                            <th>کد</th>
                            <th>نام</th>
                            <th width="100">موبایل</th>
                            <th>شهر</th>
                            <th width="70">نوع</th>
                            <th width="80">وضعیت</th>
                            <th width="180">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr wire:key="supplier-{{ $supplier->id }}">
                                <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                                <td>{{ $supplier->code }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->mobile }}</td>
                                <td>{{ $supplier->city ?? '-' }}</td>
                                <td>{{ $supplier->type === 'company' ? 'حقوقی' : 'حقیقی' }}</td>
                                <td>
                                    @if ($supplier->is_active)
                                        <span class="badge bg-success text-dark">فعال</span>
                                    @else
                                        <span class="badge bg-secondary text-dark">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary text-dark"
                                        wire:click="openEditModal({{ $supplier->id }})">
                                        <i class="bi bi-pencil-fill"></i> ویرایش
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger text-dark"
                                        wire:click="confirmDelete({{ $supplier->id }})">
                                        <i class="bi bi-trash-fill"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">هیچ تامین‌کننده‌ای ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $suppliers->links() }}</div>

        </div>
    </div>

    {{-- مودال ساخت / ویرایش (یک مودال مشترک، بدون وابستگی به Bootstrap JS) --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="form-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $supplierId ? 'ویرایش تامین‌کننده' : 'افزودن تامین‌کننده جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نام شرکت</label>
                                    <input type="text" class="form-control" wire:model="company_name">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نام شخص رابط</label>
                                    <input type="text" class="form-control" wire:model="contact_person">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نوع <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="type">
                                        <option value="individual">حقیقی</option>
                                        <option value="company">حقوقی</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">موبایل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        wire:model="mobile">
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تلفن ثابت</label>
                                    <input type="text" class="form-control" wire:model="phone">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ایمیل</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">استان</label>
                                    <input type="text" class="form-control" wire:model="province">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">شهر</label>
                                    <input type="text" class="form-control" wire:model="city">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">آدرس</label>
                                    <textarea class="form-control" rows="2" wire:model="address"></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">کد پستی</label>
                                    <input type="text" class="form-control" wire:model="postal_code">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">کد ملی</label>
                                    <input type="text" class="form-control" wire:model="national_id">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">کد اقتصادی</label>
                                    <input type="text" class="form-control" wire:model="economic_code">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">سقف اعتباری (ریال)</label>
                                    <input type="number" step="0.01" class="form-control"
                                        wire:model="credit_limit">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">مونده اولیه حساب (ریال)</label>
                                    <input type="number" step="0.01" class="form-control"
                                        wire:model="opening_balance">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">شماره شبا</label>
                                    <input type="text" class="form-control" wire:model="iban">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">شرایط پرداخت</label>
                                    <input type="text" class="form-control" wire:model="payment_terms"
                                        placeholder="مثلاً: اعتباری ۳۰ روزه">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">امتیاز (۱ تا ۵)</label>
                                    <input type="number" min="1" max="5" class="form-control"
                                        wire:model="rating">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">یادداشت</label>
                                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                </div>

                                <div class="col-12 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active"
                                        wire:model="is_active">
                                    <label class="form-check-label" for="is_active">فعال</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $supplierId ? 'ذخیره تغییرات' : 'ثبت تامین‌کننده' }}
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
            wire:key="delete-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف تامین‌کننده</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این تامین‌کننده مطمئن هستید؟
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
