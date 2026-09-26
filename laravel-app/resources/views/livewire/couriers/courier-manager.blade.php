<div>
    @include('partials.flash-messages')

    <div class="card mb-4 border-3">
        <div class="card-header">
            <div class="row g-2 align-items-center w-100">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="نام، موبایل یا پلاک پیک…">
                    </div>
                </div>
                <div class="col-auto">
                    <select wire:model.live="filterActive" class="form-select">
                        <option value="all">همه</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <div class="col-auto">
                    @can('couriers.create')
                        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                            <i class="bi bi-plus-circle"></i> پیک جدید
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-3">
        <div class="card-header">
            <h3 class="page-title mb-0">
                <i class="bi bi-box-seam me-2 text-info"></i>
                مدیریت پیک‌ها
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>موبایل</th>
                        <th>نوع وسیله</th>
                        <th>پلاک</th>
                        <th>سفارش‌ها</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($couriers as $courier)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $courier->name }}</div>
                            </td>
                            <td>{{ $courier->phone ?: '—' }}</td>
                            <td>
                                <span class="badge text-bg-light">{{ $vehicleTypes[$courier->vehicle_type] ?? $courier->vehicle_type }}</span>
                            </td>
                            <td>
                                <small dir="ltr" class="text-muted">{{ $courier->plate_number ?: '—' }}</small>
                            </td>
                            <td>
                                <span class="badge text-bg-info">{{ $courier->online_orders_count }}</span>
                            </td>
                            <td>
                                @if ($courier->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @can('couriers.edit')
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $courier->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                @endcan
                                @can('couriers.delete')
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $courier->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                        @if ($courier->notes)
                            <tr class="table-light">
                                <td colspan="7" class="small text-muted">
                                    <i class="bi bi-info-circle"></i> {{ $courier->notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">پیکی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $couriers->links() }}</div>
    </div>

    @if ($showFormModal)
        <div class="modal d-block" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $courierId ? 'ویرایش پیک' : 'پیک جدید' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">نام پیک <span class="text-danger">*</span></label>
                            <input class="form-control" wire:model="name" placeholder="مثال: علی محمدی">
                            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">موبایل</label>
                            <input class="form-control" wire:model="phone" placeholder="09123456789" dir="ltr">
                            @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نوع وسیله نقلیه</label>
                            <select class="form-select" wire:model="vehicle_type">
                                @foreach ($vehicleTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_type') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">پلاک</label>
                            <input class="form-control" wire:model="plate_number" placeholder="۱۲-۴۵۶۷۱ ۴۴" dir="ltr">
                            @error('plate_number') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">توضیحات</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                            <label class="form-check-label" for="isActiveSwitch">پیک فعال است</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-primary" wire:click="save">ثبت</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="modal d-block" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف پیک</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <p>آیا از حذف پیک «<strong>{{ $deletingName }}</strong>» مطمئن هستید؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>