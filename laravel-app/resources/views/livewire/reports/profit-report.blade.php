<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="btn-group gap-2" role="group">
            <button class="btn btn-outline-primary" wire:click="changeMonth('prev')" title="ماه قبلی">
                <i class="bi bi-chevron-right"></i>
            </button>

            <button class="btn btn-info text-dark fw-bold">ماه {{ $monthTitle }}</button>

            <button class="btn btn-outline-primary" wire:click="changeMonth('next')" title="ماه بعدی">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>
        <button class="btn btn-outline-primary" onclick="window.print()" title="پرینت گرفتن از گزارش جاری">
            <i class="bi bi-printer"></i>
             چــاپ گــزارش
        </button>
    </div>

    {{-- کارت‌های اصلی P/L --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(59,130,246,.15); --icon-color:#3b82f6">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="subheader">درآمــد فــروش</div>
                    <div class="h3 mb-1 fw-bold">{{ number_format($revenue) }}</div>
                    <div class="small text-muted">{{ number_format($invoiceCount) }} فـاکـتـور مـوفـق</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(245,158,11,.15); --icon-color:#f59e0b">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div class="subheader">ســود نــاخــالــص</div>
                    <div class="h3 mb-1 fw-bold {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($grossProfit) }}</div>
                    <div class="small text-muted">پـس از کـسر قـیـمــت تـمام‌شـده کـالا</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(239,68,68,.15); --icon-color:#ef4444">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                    <div class="subheader">کــل هــزیــنــه‌هــا</div>
                    <div class="h3 mb-1 fw-bold text-danger">{{ number_format($operatingExpenses + $payrollCost) }}</div>
                    <div class="small text-muted">هـزیـنـه‌هـا {{ number_format($operatingExpenses) }} + حـقـوق {{ number_format($payrollCost) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100 {{ $netProfit >= 0 ? 'card-glow-green' : 'card-glow-red' }}">
                <div class="card-body">
                    <div class="subheader">ســود (زیــان) خــالــص</div>
                    <div class="h2 mb-1 fw-bolder {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($netProfit) }}
                    </div>
                    <div>
                        <span class="badge {{ $margin >= 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                            حـاشـیـه سـود: {{ $margin }}٪
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- صورت سود و زیان --}}
    <div class="row row-cards">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h3 class="fw-bold mb-0">
                        <i class="bi bi-journal-arrow-down text-primary"></i>
                         صــورت ســود و زیــان
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0 pl-table">
                        <tbody>
                            <tr><td>فــروش کــل</td><td class="text-start">{{ number_format($revenue) }}</td></tr>
                            <tr class="text-muted small"><td class="ps-4">تــخــفــیــفــات</td><td class="text-start">({{ number_format($discounts) }})</td></tr>
                            <tr class="table-active"><td class="fw-semibold">بـهـای تـمـام‌شـده کـالای فـروش‌رفـتـه</td><td class="text-start">({{ number_format($cogs) }})</td></tr>
                            <tr class="border-top-2"><td class="fw-bold text-success">سود ناخالص</td><td class="text-start fw-bold text-success">{{ number_format($grossProfit) }}</td></tr>
                            <tr><td>هــزیــنــه‌هــای عــمــلــیــاتــی</td><td class="text-start">({{ number_format($operatingExpenses) }})</td></tr>
                            <tr><td>حــقــوق و دســتــمــزد</td><td class="text-start">({{ number_format($payrollCost) }})</td></tr>
                            <tr class="table-primary border-top-2">
                                <td class="fw-bold fs-6">ســود خــالــص</td>
                                <td class="text-start fw-bold fs-6 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netProfit) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer small text-muted">
                    <i class="bi bi-info-circle"></i>
                    کالای فروش‌رفته بر اساس قیمت تمام‌شده ثبت‌شده در هر فاکتور محاسبه می‌شود — نه قیمت خرید فعلی کالاها.
                </div>
            </div>
        </div>

        {{-- نمودار روند ۱۲ ماهه --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h3 class="fw-bold mb-0">
                        <i class="bi bi-bar-chart-fill text-info"></i>
                         رونــد ۱۲ مــاه اخــیــر
                    </h3>
                    <small class="text-muted">
                        <span class="dot" style="background:#3b82f6"></span> درآمـد
                        <span class="dot ms-2" style="background:#ef4444"></span> هـزیـنـه
                        <span class="dot ms-2" style="background:#22c55e"></span> سـود
                    </small>
                </div>
                <div class="card-body">
                    @php $max = collect($trend)->max(fn ($t) => max($t['revenue'], $t['cost'])) ?: 1; @endphp
                    <div class="trend-chart">
                        @foreach ($trend as $point)
                            <div class="trend-col" title="{{ $point['label'] }}: درآمد {{ number_format($point['revenue']) }}، سود {{ number_format($point['profit']) }}">
                                <div class="trend-bars">
                                    <div class="bar bar-revenue" style="height: {{ max(2, round($point['revenue'] / $max * 100)) }}%"></div>
                                    <div class="bar bar-cost" style="height: {{ max(2, round($point['cost'] / $max * 100)) }}%"></div>
                                    <div class="bar bar-profit {{ $point['profit'] < 0 ? 'bar-negative' : '' }}"
                                        style="height: {{ max(2, round(abs($point['profit']) / $max * 100)) }}%"></div>
                                </div>

                                <div class="trend-label">{{ \Hekmatinasser\Verta\Verta::parse(str_replace('/', '-', $point['label']).'-01')->format('n/y') }}</div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- پرفروش‌ترین از نظر سود --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <h3 class="fw-bold mb-0">
                        <i class="bi bi-star-fill text-warning"></i>
                         پــرســودتــریــن کــالاهــای مــاه
                    </h3>
                </div>
                <div class="card-body">
                    @forelse ($topProducts as $p)
                        @php $width = $topProducts[0]['profit'] > 0 ? max(3, round($p['profit'] / $topProducts[0]['profit'] * 100)) : 3; @endphp
                        <div class="mb-2 top-product-row">
                            <div class="d-flex justify-content-between small">
                                <span class="fw-semibold">{{ $p['name'] }}</span>
                                <span class="{{ $p['profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($p['profit']) }} تومان</span>
                            </div>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar {{ $p['profit'] >= 0 ? 'bg-success' : 'bg-danger' }}" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">فـروشـی در ایـن مـاه ثـبـت نـشـده اسـت.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- کارت‌های کمکی --}}
        <div class="col-lg-5">
            <div class="row row-cards">
                <div class="col-6">
                    <div class="card stat-card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="h2 fw-bold mb-0">{{ number_format($revenue / max(1, $invoiceCount)) }}</div>
                            <div class="subheader mt-1">مـیـانـگـیـن سـبـد هـر فـاکـتـور</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat-card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="h2 fw-bold mb-0 {{ $grossProfit > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0 }}٪
                            </div>
                            <div class="subheader mt-1">حـاشـیـه سـود نـاخـالـص</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat-card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="h2 fw-bold mb-0 text-warning">{{ number_format($discounts) }}</div>
                            <div class="subheader mt-1">تـخـفـیـفـات اعـمـال‌شـده</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat-card border-0 h-100">
                        <div class="card-body text-center">
                            <div class="h2 fw-bold mb-0 text-info">{{ number_format($operatingExpenses + $payrollCost > 0 ? $grossProfit / max(1, $operatingExpenses + $payrollCost) * 100 : 0) }}٪</div>
                            <div class="subheader mt-1">پوشش هزینه‌ها با سود ناخالص</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
