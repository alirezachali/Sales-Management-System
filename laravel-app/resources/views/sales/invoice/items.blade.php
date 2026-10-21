<div class="section-title">کالاهای خریداری شده</div>

<table class="items-table">
    <thead>
        <tr>
            <th class="item-index">#</th>
            <th class="col-name">کالا</th>
            <th class="col-detail">تعداد × مبلغ</th>
            <th class="col-total">مبلغ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sale->items as $item)
            @php
                $qty = (float) $item->quantity;
                $qtyText = rtrim(rtrim(number_format($qty, 2, '.', ','), '0'), '.');
            @endphp
            <tr>
                <td class="item-index">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $item->product?->name ?? 'کالای حذف‌شده' }}</td>
                <td class="col-detail">{{ $qtyText }} × {{ number_format((float) $item->unit_price) }}</td>
                <td class="col-total">{{ number_format((float) $item->line_total) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
