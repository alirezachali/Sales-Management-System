<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-primary">{{ $counts['messages'] }}</div>
                    <div class="subheader">پیام‌های ارسال‌شده</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-info">{{ $counts['delivered'] }}</div>
                    <div class="subheader">کل دریافت‌کنندگان</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-success">{{ $counts['read'] }}</div>
                    <div class="subheader">خوانده‌شده</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-warning">{{ $counts['unread'] }}</div>
                    <div class="subheader">خوانده‌نشده</div>
                </div>
            </div>
        </div>
    </div>

    {{--=========== فیلترها ============--}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو در موضوع یا متن پیام...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterAudience" class="form-select">
                        <option value="">همه انواع ارسال</option>
                        <option value="single">شخصی</option>
                        <option value="all">گروهی (همه کاربران)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterRead" class="form-select">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="unread">خوانده‌نشده</option>
                        <option value="read">خوانده‌شده</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{--============================== جدول پیام‌ها ==============================--}}
    <div class="card shadow-sm" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-envelope-paper text-primary"></i>
                    پیام‌های ارسال‌شده
                </h3>
                <small class="text-muted">مدیریت پیام‌های ارسالی و پیگیری خوانده‌شدن آن‌ها</small>
            </div>
            <div>
                @can('messages.create')
                    <button type="button" class="btn btn-primary" wire:click="openCreateModal"
                        title="ارسال پیام جدید">
                        <i class="bi bi-send-plus"></i>
                        ارسال پیام جدید
                    </button>
                @endcan
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="40">ردیف</th>
                        <th>موضوع</th>
                        <th width="140">نوع ارسال</th>
                        <th width="150">گیرندگان</th>
                        <th width="180">وضعیت خوانده‌شدن</th>
                        <th width="150">تاریخ ارسال</th>
                        <th width="130">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr wire:key="message-{{ $message->id }}">
                            <td>{{ $loop->iteration + ($messages->currentPage() - 1) * $messages->perPage() }}</td>
                            <td>
                                <div class="fw-bold">{{ $message->subject ?: '(بدون موضوع)' }}</div>
                                <small class="text-muted d-block"
                                    style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $message->body }}
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $message->audience === 'all' ? 'bg-info' : 'bg-secondary' }} text-dark">
                                    {{ $message->audience_label }}
                                </span>
                            </td>
                            <td>
                                @if ($message->recipients_count === 1)
                                    {{ $message->recipients->first()?->user?->name ?? 'کاربر حذف‌شده' }}
                                @else
                                    <span class="badge bg-dark">{{ $message->recipients_count }} کاربر</span>
                                @endif
                            </td>
                            <td>
                                @if ($message->is_fully_read)
                                    <span class="badge bg-success text-dark">
                                        <i class="bi bi-check2-all"></i>
                                        همه خواندند ({{ $message->read_count }})
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i>
                                        {{ $message->read_count }} از {{ $message->recipients_count }} خوانده‌شده
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ jalaliDate($message->created_at) }}</div>
                                <small class="text-muted" style="font-size:.72rem">
                                    {{ relativeTimeFa($message->created_at) }}
                                </small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info"
                                    wire:click="openDetails({{ $message->id }})"
                                    title="مشاهده گیرندگان و وضعیت خوانده‌شدن">
                                    <i class="bi bi-people"></i>
                                </button>
                                @can('messages.delete')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        wire:click="confirmDelete({{ $message->id }})" title="حذف پیام">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                هنوز پیامی ارسال نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $messages->links() }}</div>
        </div>
    </div>

    {{-- ================================ مودال ارسال پیام ================================ --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-send"></i>
                                ارسال پیام جدید
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-12">
                                    <label class="form-label">گیرندگان پیام</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="single"
                                                id="audience-single" wire:model.live="audience">
                                            <label class="form-check-label" for="audience-single">
                                                یک کاربر خاص
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="all"
                                                id="audience-all" wire:model.live="audience">
                                            <label class="form-check-label" for="audience-all">
                                                همه کاربران
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @if ($audience === 'single')
                                    <div class="col-md-12">
                                        <label class="form-label">کاربر گیرنده</label>
                                        <select wire:model="recipient_id"
                                            class="form-select @error('recipient_id') is-invalid @enderror">
                                            <option value="">— انتخاب کاربر —</option>
                                            @foreach ($recipients as $recipient)
                                                <option value="{{ $recipient->id }}">
                                                    {{ $recipient->name }} ({{ $recipient->username }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('recipient_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="alert alert-info mb-0 py-2">
                                            <i class="bi bi-info-circle"></i>
                                            این پیام برای همه کاربران فعال سیستم (به‌جز خودتان) ارسال می‌شود؛
                                            در حال حاضر {{ $recipients->count() }} کاربر.
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-12">
                                    <label class="form-label">موضوع (اختیاری)</label>
                                    <input type="text" wire:model="subject"
                                        class="form-control @error('subject') is-invalid @enderror"
                                        placeholder="موضوع پیام...">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">متن پیام</label>
                                    <textarea wire:model="body" rows="5"
                                        class="form-control @error('body') is-invalid @enderror"
                                        placeholder="متن پیام را بنویسید..."></textarea>
                                    @error('body')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                <i class="bi bi-send"></i>
                                ارسال پیام
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال گیرندگان و وضعیت خوانده‌شدن ============================ --}}
    @if ($showDetailsModal && $detailsMessage)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-people"></i>
                            گیرندگان پیام
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">

                        <table class="table table-bordered table-sm mb-3">
                            <tbody>
                                <tr>
                                    <th width="120">موضوع</th>
                                    <td>{{ $detailsMessage->subject ?: '(بدون موضوع)' }}</td>
                                </tr>
                                <tr>
                                    <th>متن پیام</th>
                                    <td style="white-space: pre-wrap">{{ $detailsMessage->body }}</td>
                                </tr>
                                <tr>
                                    <th>فرستنده</th>
                                    <td>{{ $detailsMessage->sender?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>تاریخ ارسال</th>
                                    <td>{{ jalaliDateTime($detailsMessage->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>وضعیت</th>
                                    <td>
                                        @if ($detailsMessage->is_fully_read)
                                            <span class="badge bg-success text-dark">همه خوانده‌اند</span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                {{ $detailsMessage->read_count }} از
                                                {{ $detailsMessage->recipients_count }} خوانده‌شده
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="fw-bold mb-2">فهرست گیرندگان</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm">
                                <thead>
                                    <tr>
                                        <th width="40">ردیف</th>
                                        <th>کاربر</th>
                                        <th width="130">وضعیت</th>
                                        <th width="200">تاریخ و ساعت خواندن</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($detailsMessage->recipients as $recipient)
                                        <tr wire:key="recipient-{{ $recipient->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $recipient->user?->name ?? 'کاربر حذف‌شده' }}
                                                @if ($recipient->user?->username)
                                                    <small class="text-muted">({{ $recipient->user->username }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($recipient->isRead())
                                                    <span class="badge bg-success text-dark">
                                                        <i class="bi bi-check2"></i> خوانده‌شده
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary text-dark">
                                                        <i class="bi bi-clock"></i> خوانده‌نشده
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($recipient->read_at)
                                                    {{ jalaliDateTime($recipient->read_at) }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                گیرنده‌ای ثبت نشده است.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================== مودال تایید حذف ============================== --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-dark">
                        <h5 class="modal-title">حذف پیام</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این پیام مطمئن هستید؟ پیام از صندوق همه گیرندگان هم حذف می‌شود و
                        این عملیات قابل بازگشت نیست.
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
