<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    <div class="d-flex justify-content-end mb-3">
        @can('counts.create')
            <button class="btn btn-primary glow-btn" wire:click="openCreateModal">
                <i class="bi bi-clipboard-check"></i> شروع انبارگردانی جدید
            </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-check text-primary"></i>
                    برگه‌های انبارگردانی
                </h3>
                <small class="text-muted">
                    شمارش فیزیکی موجودی با بارکدخوان، محاسبه خودکار مغایرت و اعمال آن روی سیستم.
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>شماره برگه</th>
                            <th>انبار</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th class="text-center" width="240">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($counts as $count)
                            <tr wire:key="cn-{{ $count->id }}">
                                <td><code>{{ $count->reference }}</code></td>
                                <td>{{ $count->warehouse?->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $count->statusColor() }}-subtle text-{{ $count->statusColor() }}-emphasis">
                                        {{ $count->statusText() }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ jalaliDateTime($count->created_at) }}</td>
                                <td class="text-center">
                                    @if ($count->status === 'draft')
                                        <button class="btn btn-sm btn-outline-primary" wire:click="startCount({{ $count->id }})">
                                            <i class="bi bi-pencil-square"></i> ادامه شمارش
                                        </button>
                                        @can('counts.finalize')
                                            <button class="btn btn-sm btn-outline-success" wire:click="startCount({{ $count->id }})" title="برای نهایی‌سازی وارد صفحه شمارش شوید">
                                                <i class="bi bi-check2-all"></i>
                                            </button>
                                        @endcan
                                    @endif
                                    @if ($count->status === 'draft')
                                        @can('counts.edit')
                                            <button class="btn btn-sm btn-outline-warning" wire:click="cancel({{ $count->id }})" wire:confirm="لغو این برگه انبارگردانی؟">
                                                <i class="bi bi-x"></i> لغو
                                            </button>
                                        @endcan
                                    @endif
                                    @can('counts.edit')
                                        <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $count->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">برگه انبارگردانی وجود ندارد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $counts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- مودال ساخت --}}
    @if ($showCreateModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="cn-create-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="create">
                        <div class="modal-header">
                            <h5 class="modal-title">انبارگردانی جدید</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">انبار <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="warehouse_id">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach ($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="includeEmpty" wire:model="includeEmpty">
                                <label class="form-check-label" for="includeEmpty">شامل کالاهای با موجودی صفر هم باشد</label>
                            </div>
                            <div>
                                <label class="form-label">یادداشت</label>
                                <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary">ساخت و شروع شمارش</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- صفحه شمارش --}}
    @if ($showCountModal && $active)
        <div class="card border-0 shadow-sm mt-4 count-page" wire:key="count-{{ $active->id }}">
            <div class="card-header flex-wrap gap-2">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-clipboard-check text-success"></i> شمارش — {{ $active->reference }}</h3>
                    <small class="text-muted">انبار: {{ $active->warehouse?->name }} | با بارکدخوان اسکن کنید یا مقدار را دستی وارد کنید.</small>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <button class="btn btn-sm btn-secondary" wire:click="closeCount"><i class="bi bi-x-lg"></i> بستن</button>
                    <button class="btn btn-sm btn-outline-primary" wire:click="saveCounts" wire:loading.attr="disabled">
                        <i class="bi bi-save"></i> ذخیره مقادیر
                    </button>
                    @can('counts.finalize')
                        <button class="btn btn-sm btn-success glow-btn" wire:click="finalize" wire:confirm="نهایی‌سازی، مغایرت‌ها را روی موجودی انبار اعمال می‌کند. ادامه دهیم؟" wire:loading.attr="disabled">
                            <i class="bi bi-check2-all"></i> نهایی‌سازی و اعمال مغایرت
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                @if (session('count_error'))
                    <div class="alert alert-warning py-2">{{ session('count_error') }}</div>
                @endif

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control" wire:model="scanBarcode" wire:enter="scan"
                                placeholder="بارکد را اسکن یا تایپ کنید..." autocomplete="off" autofocus>
                            <button class="btn btn-primary" type="button" wire:click="scan"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" wire:model.live.debounce.400ms="search" placeholder="جستجوی کالا...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>کالا</th>
                                <th>بارکد</th>
                                <th>موجود سیستم</th>
                                <th width="140">شمارش واقعی</th>
                                <th>مغایرت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                @php
                                    $countedQty = (float) ($counted[$item->product_id] ?? $item->counted_quantity);
                                    $diff = $countedQty - (float) $item->system_quantity;
                                @endphp
                                <tr wire:key="ci-{{ $item->id }}" class="{{ abs($diff) > 0.001 ? 'table-warning' : '' }}">
                                    <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                                    <td class="fw-semibold small">{{ $item->product?->name }}</td>
                                    <td><code class="small">{{ $item->product?->barcode }}</code></td>
                                    <td>{{ number_format((float) $item->system_quantity, 1) }}</td>
                                    <td>
                                        <input type="number" step="any" min="0" class="form-control form-control-sm"
                                            wire:model.live="counted.{{ $item->product_id }}" value="{{ $countedQty }}">
                                    </td>
                                    <td>
                                        @if (abs($diff) > 0.001)
                                            <span class="badge {{ $diff > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 1) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">موردی برای شمارش نیست.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $items->links() }}</div>
            </div>
        </div>
    @endif

    {{-- مودال حذف --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="cn-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">حذف برگه انبارگردانی</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">این برگه و همه اقلام شمارش آن حذف شوند؟</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
