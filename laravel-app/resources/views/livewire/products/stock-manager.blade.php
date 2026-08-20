<div dir="rtl">

    {{-- پیام موفقیت --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="card-title">
                گردش کالا:
                {{ $product->name }}
            </h3>
            <a href="{{ route('products.index') }}" class="btn btn-secondary" title="بازگشت به صفحه لیست محصولات">
                بازگشت
            </a>
        </div>

        <div class="card-body">
            <div class="card-header d-flex justify-content-between align-items-center">
                {{-- نمایش پیغام موجودی فعلی --}}
                <div class="alert alert-info mb-0">
                    موجودی فعلی:
                    <strong>{{ $product->formatted_stock }}</strong>
                    {{ $product->unit }}
                </div>

                {{-- دکمه‌های ورود و خروج کالا از انبار --}}
                <div class="d-flex gap-2">
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
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-vcenter">

                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>نوع عملیات</th>
                            <th>مقدار</th>
                            <th>توضیحات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($movements as $movement)
                            <tr wire:key="movement-{{ $movement->id }}">
                                <td>{{ $movement->created_at }}</td>
                                <td>
                                    @switch($movement->type)
                                        @case('initial')
                                            <span class="badge bg-info">موجودی اولیه</span>
                                        @break

                                        @case('purchase')
                                            <span class="badge bg-success">خرید</span>
                                        @break

                                        @case('sale')
                                            <span class="badge bg-danger">فروش</span>
                                        @break

                                        @case('adjust')
                                            <span class="badge bg-warning">اصلاح</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>{{ $movement->quantity }}</td>
                                <td>{{ $movement->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    هیچ گردشی برای این کالا ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="mt-3">{{ $movements->links() }}</div>

        </div>
    </div>

    {{-- ============================ مودال ورود/خروج کالا ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
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

                            <div class="alert {{ $formType === 'purchase' ? 'alert-info' : 'alert-warning' }}">
                                کالا:
                                <strong>{{ $product->name }}</strong>
                                <br>
                                موجودی فعلی:
                                <strong>{{ $product->formatted_stock }}</strong>
                                {{ $product->unit }}
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
                                class="btn {{ $formType === 'purchase' ? 'btn-primary' : 'btn-danger' }}"
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
