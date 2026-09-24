<div>
    @include('partials.flash-messages')

    <div class="card mb-4 border-3">
        <div class="card-header">
            <div class="row g-2 align-items-center w-100">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="نام یا موبایل…">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-primary" wire:click="pullNow">همگام‌سازی از فروشگاه</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-3">
        <div class="card-header">
            <h3 class="page-title mb-0">
                <i class="bi bi-people me-2 text-info"></i>
                مشتریان فروشگاه آنلاین
            </h3>
            <small class="text-muted">همان پرونده باشگاه مشتریان؛ با موبایل به حساب آنلاین وصل شده‌اند.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>موبایل</th>
                        <th>شهر / آدرس</th>
                        <th>ثبت آنلاین</th>
                        <th>باشگاه</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->full_name }}</td>
                            <td dir="ltr">{{ $customer->mobile }}</td>
                            <td><small>{{ $customer->city }} {{ $customer->address }}</small></td>
                            <td>{{ $customer->registered_online_at?->format('Y/m/d') }}</td>
                            <td>
                                <a href="{{ route('customers.index') }}">پرونده مشتری</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">هنوز مشتری آنلاینی همگام نشده.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $customers->links() }}</div>
    </div>
</div>
