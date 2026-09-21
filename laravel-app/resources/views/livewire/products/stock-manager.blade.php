<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="card-title">
                گردش کالا:
                {{ $product->name }}
            </h3>
            <a href="{{ route('products.index') }}" class="btn btn-primary" title="بازگشت به صفحه لیست محصولات">
                بازگشت به صفحه محصولات
            </a>
        </div>

        <div class="card-body">
            <div class="card-header d-flex justify-content-between align-items-center">
            {{--======= نمایش پیغام موجودی فعلی =======--}}
                <div class="alert alert-info mb-3 w-25 justify-content-between">
                    موجودی فعلی 👈
                    <strong>{{ $product->formatted_stock }} {{ $product->unit }}</strong>

                </div>

            {{--====== دکمه‌های ورود، خروج و خروجی گزارش ======--}}
                {{-- <div class="d-flex align-items-center gap-2">

                    <button type="button" class="btn btn-sm btn-outline-success" wire:click="openAddStockModal"
                        title="ورود این کالا به انبار">
                        <i class="bi bi-plus-lg"></i>
                        ورود کالا
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="openRemoveStockModal"
                        title="خروج این کالا از انبار">
                        <i class="bi bi-dash-lg"></i>
                        خروج کالا
                    </button>

                    <div class="vr mx-1 d-none d-sm-block" style="opacity:.15;"></div> --}}

                {{--====== دکمه‌های خروجی گزارش (اکسل / CSV) ======--}}
                    <div class="d-flex align-items-center gap-2">

                        <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                            wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel"
                            title="دریافت خروجی Excel">
                            <span wire:loading.remove wire:target="exportExcel">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                            </span>
                            <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm"></span>
                            خروجی Excel
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                            wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv"
                            title="دریافت خروجی CSV">
                            <span wire:loading.remove wire:target="exportCsv">
                                <i class="bi bi-filetype-csv"></i>
                            </span>
                            <span wire:loading wire:target="exportCsv" class="spinner-border spinner-border-sm"></span>
                            خروجی CSV
                        </button>

                    </div>

                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th width="180">تاریخ</th>
                            <th width="110">نوع عملیات</th>
                            <th width="110">مقدار</th>
                            <th>توضیحات</th>
                            <th width="130">انبار</th>
                            <th width="130">ثبت توسط</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($movements as $movement)
                            <tr wire:key="movement-{{ $movement->id }}">
                                <td>{{ jalaliDateTime($movement->created_at) }}</td>
                                <td>
                                    @switch($movement->type)
                                        @case('initial')
                                            <span class="badge bg-info-subtle">موجودی اولیه</span>
                                        @break

                                        @case('purchase')
                                            <span class="badge bg-success-subtle">خرید</span>
                                        @break

                                        @case('sale')
                                            <span class="badge bg-danger-subtle">فروش</span>
                                        @break

                                        @case('adjust')
                                            <span class="badge bg-warning-subtle">اصلاح</span>
                                        @break

                                        @case('transfer')
                                            <span class="badge bg-primary-subtle">انتقال</span>
                                        @break

                                        @case('return')
                                            <span class="badge bg-secondary-subtle">مرجوعی</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    {{ number_format($movement->quantity, 0) }}
                                    <span>{{ $product->unit }}</span>
                                </td>
                                <td>{{ $movement->description }}</td>
                                <td>
                                    <span class="badge bg-info-subtle">
                                        {{ $movement->warehouse?->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle">
                                        {{ $movement->user?->name ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    هیچ گردشی برای این کالا ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="card-footer">
                {{ $movements->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

{{-- ================================== مودال ورود/خروج کالا ================================== --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="stock-form-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form wire:submit="save">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                @if ($formType === 'purchase')
                                    <i class="bi bi-plus-lg text-success"></i>
                                    ورود کالا به انبار
                                @else
                                    <i class="bi bi-dash-lg text-danger"></i>
                                    خروج کالا از انبار
                                @endif
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">

                            <div class="alert {{ $formType === 'purchase' ? 'alert-success' : 'alert-danger' }}">
                                کالا:
                                <strong class="text-info">{{ $product->name }}</strong>
                                    <span>|</span>
                                موجودی فعلی:
                                <strong class="text-info">{{ $product->formatted_stock }}{{ $product->unit }}</strong>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">انبار</label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror"
                                    wire:model="warehouse_id">
                                    @foreach ($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (count($warehouseStocks) > 0)
                                    <div class="form-text">
                                        توزیع موجودی:
                                        @foreach ($warehouseStocks as $ws)
                                            <span class="badge bg-info-subtle text-info-emphasis mt-1">
                                                {{ $ws->warehouse?->name }}: {{ rtrim(rtrim(number_format((float) $ws->quantity, 3, '.', ''), '0'), '.') }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تعداد {{ $formType === 'purchase' ? 'ورودی' : 'خروجی' }}</label>
                                <input type="number" step="0.001" wire:model="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror" autofocus>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">توضیحات</label>
                                <textarea wire:model="description" class="form-control"></textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals" title="انصراف">
                                انصراف
                            </button>
                            <button type="submit"
                                class="btn {{ $formType === 'purchase' ? 'btn-success' : 'btn-danger' }}"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $formType === 'purchase' ? 'ثبت ورود کالا' : 'ثبت خروج کالا' }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif

</div>
