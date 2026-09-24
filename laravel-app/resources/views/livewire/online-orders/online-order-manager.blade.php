<div>
    @include('partials.flash-messages')

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3"><div class="card-body">
                <div class="subheader">جدید</div>
                <div class="h1 mb-0">{{ $stats['received'] }}</div>
            </div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3"><div class="card-body">
                <div class="subheader">آماده‌سازی</div>
                <div class="h1 mb-0">{{ $stats['packing'] }}</div>
            </div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3"><div class="card-body">
                <div class="subheader">نزد پیک</div>
                <div class="h1 mb-0">{{ $stats['out_for_delivery'] }}</div>
            </div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3"><div class="card-body">
                <div class="subheader">تحویل‌شده</div>
                <div class="h1 mb-0 text-success">{{ $stats['delivered'] }}</div>
            </div></div>
        </div>
    </div>

    <div class="card mb-4 border-3">
        <div class="card-header">
            <div class="row g-2 align-items-center w-100">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="نام، موبایل یا کلید سفارش…">
                    </div>
                </div>
                <div class="col-auto">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="all">همه</option>
                        <option value="received">جدید</option>
                        <option value="packing">آماده‌سازی</option>
                        <option value="out_for_delivery">پیک</option>
                        <option value="delivered">تحویل</option>
                        <option value="rejected">رد شده</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-primary" wire:click="pullNow">
                        دریافت از فروشگاه
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-3">
        <div class="card-header">
            <h3 class="page-title mb-0">
                <i class="bi bi-bag-heart me-2 text-info"></i>
                سفارش‌های فروشگاه آنلاین
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>مشتری</th>
                        <th>آدرس</th>
                        <th>مبلغ</th>
                        <th>وضعیت</th>
                        <th>فاکتور</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $order->customer_name ?: '—' }}</div>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>
                                <small>{{ $order->city }} {{ $order->address }}</small>
                            </td>
                            <td>{{ number_format((float) $order->total) }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ $order->statusLabel() }}</span>
                                @if ($order->courier_name)
                                    <div class="small text-muted mt-1">پیک: {{ $order->courier_name }} — {{ $order->courier_phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($order->sale_id)
                                    <a href="{{ route('invoice', $order->sale_id) }}">#{{ $order->sale_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if (in_array($order->status, ['received', 'pending'], true))
                                    @can('online-orders.process')
                                        <button type="button" class="btn btn-sm btn-success" wire:click="fulfill({{ $order->id }})">پردازش</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="openReject({{ $order->id }})">رد</button>
                                    @endcan
                                @endif
                                @if ($order->status === 'packing')
                                    @can('online-orders.dispatch')
                                        <button type="button" class="btn btn-sm btn-primary" wire:click="openDispatch({{ $order->id }})">ارسال پیک</button>
                                    @endcan
                                @endif
                                @if ($order->status === 'out_for_delivery')
                                    @can('online-orders.dispatch')
                                        <button type="button" class="btn btn-sm btn-success" wire:click="deliver({{ $order->id }})">تحویل شد</button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                        @if ($items = data_get($order->payload, 'items', []))
                            <tr class="table-light">
                                <td colspan="6" class="small text-muted">
                                    @foreach ($items as $item)
                                        کالا {{ $item['source_product_id'] ?? '?' }} × {{ $item['quantity'] ?? 0 }}
                                        @if (! $loop->last) · @endif
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">سفارشی نیست.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $orders->links() }}</div>
    </div>

    @if ($dispatchingId)
        <div class="modal d-block" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">ارسال با پیک</h5></div>
                    <div class="modal-body">
                        <label class="form-label">نام پیک</label>
                        <input class="form-control mb-3" wire:model="courier_name">
                        @error('courier_name') <div class="text-danger small">{{ $message }}</div> @enderror
                        <label class="form-label">موبایل پیک</label>
                        <input class="form-control" wire:model="courier_phone">
                        @error('courier_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="$set('dispatchingId', null)">انصراف</button>
                        <button type="button" class="btn btn-primary" wire:click="dispatchOrder">ثبت</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($rejectingId)
        <div class="modal d-block" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">رد سفارش</h5></div>
                    <div class="modal-body">
                        <textarea class="form-control" rows="3" wire:model="reject_reason" placeholder="دلیل"></textarea>
                        @error('reject_reason') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="$set('rejectingId', null)">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="rejectOrder">رد کردن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
