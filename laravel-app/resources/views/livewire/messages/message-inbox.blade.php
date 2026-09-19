<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-6 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-primary">{{ $totalCount }}</div>
                    <div class="subheader">کل پیام‌ها</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-warning">{{ $unreadCount }}</div>
                    <div class="subheader">خوانده‌نشده</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-success">{{ $totalCount - $unreadCount }}</div>
                    <div class="subheader">خوانده‌شده</div>
                </div>
            </div>
        </div>
    </div>

    {{--=========== فیلترها ============--}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو در موضوع یا متن پیام...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="filterRead" class="form-select">
                        <option value="">همه پیام‌ها</option>
                        <option value="unread">فقط خوانده‌نشده‌ها</option>
                        <option value="read">فقط خوانده‌شده‌ها</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{--============================== صندوق پیام ==============================--}}
    <div class="card shadow-sm" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-inbox text-primary"></i>
                    پیام‌های من
                </h3>
                <small class="text-muted">پیام‌های دریافتی از مدیریت</small>
            </div>
            <div>
                @if ($unreadCount > 0)
                    <button type="button" class="btn btn-outline-success" wire:click="markAllAsRead"
                        title="علامت‌گذاری همه پیام‌ها به‌عنوان خوانده‌شده">
                        <i class="bi bi-check2-all"></i>
                        خواندن همه
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            @forelse ($inbox as $item)
                <div class="border rounded p-3 mb-2 {{ $item->isRead() ? '' : 'border-primary bg-primary bg-opacity-10' }}"
                    wire:key="inbox-{{ $item->id }}" role="button" wire:click="open({{ $item->id }})">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                        <div class="flex-grow-1">
                            <div class="fw-bold">
                                @if (! $item->isRead())
                                    <span class="badge bg-primary text-dark me-1">جدید</span>
                                @endif
                                {{ $item->message?->subject ?: '(بدون موضوع)' }}
                            </div>
                            <small class="text-muted d-block mt-1"
                                style="max-width: 640px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $item->message?->body }}
                            </small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">
                                <i class="bi bi-person"></i>
                                {{ $item->message?->sender?->name ?? 'مدیریت' }}
                            </small>
                            <small class="text-muted d-block" style="font-size:.72rem">
                                {{ relativeTimeFa($item->message?->created_at) }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-open" style="font-size:1.8rem"></i>
                    <div class="mt-2">پیامی برای نمایش وجود ندارد.</div>
                </div>
            @endforelse

            <div class="card-footer">
                {{ $inbox->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- ============================== مودال مشاهده پیام ============================== --}}
    @if ($showDetailsModal && $detailsRecipient?->message)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-envelope-open"></i>
                            {{ $detailsRecipient->message->subject ?: '(بدون موضوع)' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                            <small class="text-muted">
                                <i class="bi bi-person"></i>
                                فرستنده: {{ $detailsRecipient->message->sender?->name ?? 'مدیریت' }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i>
                                {{ jalaliDateTime($detailsRecipient->message->created_at) }}
                            </small>
                        </div>

                        <div class="alert alert-light border" style="white-space: pre-wrap">{{ $detailsRecipient->message->body }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
