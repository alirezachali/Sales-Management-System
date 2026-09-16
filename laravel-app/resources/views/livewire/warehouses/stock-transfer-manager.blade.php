<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter','all')">همه</button>
            <button type="button" class="btn btn-sm {{ $filter === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter','pending')">در انتظار</button>
            <button type="button" class="btn btn-sm {{ $filter === 'received' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter','received')">تحویل شده</button>
        </div>

        @can('transfers.create')
            <button class="btn btn-primary glow-btn" wire:click="openCreateModal">
                <i class="bi bi-arrow-left-right"></i> انتقال جدید
            </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header">
            <h3 class="fw-bold mb-1"><i class="bi bi-arrow-left-right text-primary"></i> انتقالات بین انبار</h3>
            <small class="text-muted">کالا ابتدا از انبار مبدأ کسر و پس از تأیید دریافت به انبار مقصد اضافه می‌شود.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>شماره</th>
                            <th>از</th>
                            <th>به</th>
                            <th>اقلام</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th width="180" class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr wire:key="tr-{{ $transfer->id }}">
                                <td><code>{{ $transfer->reference }}</code></td>
                                <td>{{ $transfer->fromWarehouse?->name }}</td>
                                <td>{{ $transfer->toWarehouse?->name }}</td>
                                <td>{{ $transfer->items_count ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $transfer->statusColor() }}-subtle text-{{ $transfer->statusColor() }}-emphasis">
                                        {{ $transfer->statusText() }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ jalaliDateTime($transfer->created_at) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info" wire:click="showDetail({{ $transfer->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if ($transfer->status === 'pending')
                                        @can('transfers.receive')
                                            <button class="btn btn-sm btn-outline-success" wire:click="confirmReceive({{ $transfer->id }})"
                                                wire:confirm="تأیید دریافت و انتقال موجودی به انبار مقصد؟">
                                                <i class="bi bi-box-arrow-in-down"></i> دریافت
                                            </button>
                                        @endcan
                                        @can('transfers.create')
                                            <button class="btn btn-sm btn-outline-danger" wire:click="cancel({{ $transfer->id }})"
                                                wire:confirm="لغو این انتقال؟">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">انتقالی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $transfers->links() }}</div>
        </div>
    </div>

    {{-- مودال ایجاد --}}
    @if ($showCreateModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="tr-create-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="create">
                        <div class="modal-header">
                            <h5 class="modal-title">انتقال بین انبار</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">از انبار <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model.live="from_warehouse_id">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('from_warehouse_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">به انبار <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="to_warehouse_id">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('to_warehouse_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <label class="form-label">افزودن کالا</label>
                            <div class="position-relative mb-2">
                                <input type="text" class="form-control" wire:model.live.debounce.300ms="productSearch"
                                    placeholder="نام یا بارکد کالا را تایپ کنید..." autocomplete="off"
                                    @if(blank($from_warehouse_id)) disabled @endif>
                                @if(!blank($from_warehouse_id))
                                    <div class="list-group position-absolute w-100 shadow" style="z-index:20">
                                        @foreach ($searchResults as $p)
                                            <button type="button" class="list-group-item list-group-item-action" wire:click="addItem({{ $p->id }})">
                                                {{ $p->name }} <small class="text-muted">({{ $p->barcode }})</small>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @if(blank($from_warehouse_id))
                                <div class="text-muted small mb-2"><i class="bi bi-info-circle"></i> برای جستجوی کالا، ابتدا انبار مبدأ را انتخاب کنید.</div>
                            @endif

                            @if (!empty($items))
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr><th>کالا</th><th>موجود در مبدأ</th><th width="120">مقدار</th><th width="50"></th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr wire:key="tri-{{ $item['id'] }}">
                                                    <td class="fw-semibold small">{{ $item['name'] }}</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ number_format($item['available'], 1) }}</span></td>
                                                    <td>
                                                        <input type="number" step="any" min="0" class="form-control form-control-sm"
                                                            wire:model.live="items.{{ $item['id'] }}.quantity">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $item['id'] }})">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="mt-3">
                                <label class="form-label">یادداشت</label>
                                <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="create">ثبت انتقال</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال جزئیات --}}
    @if ($showDetailModal && $detail)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="tr-detail-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">جزئیات انتقال {{ $detail->reference }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3 small">
                            <div class="col-6"><strong>از:</strong> {{ $detail->fromWarehouse?->name }}</div>
                            <div class="col-6"><strong>به:</strong> {{ $detail->toWarehouse?->name }}</div>
                            <div class="col-6"><strong>وضعیت:</strong> <span class="badge bg-{{ $detail->statusColor() }}-subtle text-{{ $detail->statusColor() }}-emphasis">{{ $detail->statusText() }}</span></div>
                            <div class="col-6"><strong>ثبت‌کننده:</strong> {{ $detail->creator?->name ?? '—' }}</div>
                            @if ($detail->notes)
                                <div class="col-12"><strong>یادداشت:</strong> {{ $detail->notes }}</div>
                            @endif
                        </div>
                        <table class="table table-sm">
                            <thead><tr><th>کالا</th><th>بارکد</th><th>مقدار</th></tr></thead>
                            <tbody>
                                @foreach ($detail->items as $item)
                                    <tr>
                                        <td>{{ $item->product?->name }}</td>
                                        <td><code>{{ $item->product?->barcode }}</code></td>
                                        <td>{{ number_format((float) $item->quantity, 1) }} {{ $item->product?->unit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
