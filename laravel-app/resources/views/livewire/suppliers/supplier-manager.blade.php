<div dir="rtl">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">تامین‌کنندگان</h4>
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> افزودن تامین‌کننده جدید
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>موبایل</th>
                        <th>شهر</th>
                        <th>نوع</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr wire:key="supplier-{{ $supplier->id }}">
                            <td>{{ $supplier->code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->mobile }}</td>
                            <td>{{ $supplier->city ?? '-' }}</td>
                            <td>{{ $supplier->type === 'company' ? 'حقوقی' : 'حقیقی' }}</td>
                            <td>
                                @if ($supplier->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="openEditModal({{ $supplier->id }})">
                                    <i class="bi bi-pencil"></i> ویرایش
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    wire:click="confirmDelete({{ $supplier->id }})">
                                    <i class="bi bi-trash"></i> حذف
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">هیچ تامین‌کننده‌ای ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $suppliers->links() }}
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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror" wire:model="mobile">
                                    @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تلفن ثابت</label>
                                    <input type="text" class="form-control" wire:model="phone">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ایمیل</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <input type="number" step="0.01" class="form-control" wire:model="credit_limit">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">مونده اولیه حساب (ریال)</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="opening_balance">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">شماره شبا</label>
                                    <input type="text" class="form-control" wire:model="iban">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">شرایط پرداخت</label>
                                    <input type="text" class="form-control" wire:model="payment_terms" placeholder="مثلاً: اعتباری ۳۰ روزه">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">امتیاز (۱ تا ۵)</label>
                                    <input type="number" min="1" max="5" class="form-control" wire:model="rating">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">یادداشت</label>
                                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                </div>

                                <div class="col-12 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" wire:model="is_active">
                                    <label class="form-check-label" for="is_active">فعال</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
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
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:key="delete-modal">
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
                        <button type="button" class="btn btn-danger" wire:click="delete" wire:loading.attr="disabled">
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
