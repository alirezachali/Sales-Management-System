<div wire:poll.{{ $pollingSeconds }}s>

    <div class="col-12">
        <div class="card dashboard-card border-3">
            <div class="card-header bg-success text-dark opacity-70 d-flex justify-content-between align-items-center">
                <strong>👤 کاربران</strong>
                <small class="text-dark">
                    <span class="badge bg-success">آنلاین: {{ $users->filter->isOnline()->count() }}</span>
                </small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>نام کاربری</th>
                            <th>نقش</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td>
                                    {{ $user->username }}
                                    @if ($user->id === auth()->id())
                                        <span class="badge bg-info text-dark">شما</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->role)
                                        <span class="badge bg-warning text-dark"
                                            style="background-color: {{ $user->role->color ?? '#6c757d' }}">
                                            {{ $user->role->display_name ?? $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->isOnline())
                                        <span class="badge bg-success"><i class="bi bi-circle-fill"></i> آنلاین</span>
                                    @elseif ($seen = $user->lastSeen())
                                        <span class="badge bg-secondary">{{ relativeTimeFa($seen) }}</span>
                                    @else
                                        <span class="text-muted">هنوز وارد نشده</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    کاربری یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
