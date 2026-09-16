<div class="section-title">کالاهای خریداری شده</div>

<table class="items-table">
    <thead>
        <tr>
            <th class="item-index">#</th>
            <th class="col-name">کالا</th>
            <th class="col-detail">تعداد × مبلغ</th>
            <th class="col-total">کل</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sale->items as $item)
            <tr>
                <td class="item-index">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $item->product->name }}</td>
                <td class="col-detail">
                    {{ number_format($item->quantity, 0) }} × {{ number_format($item->unit_price) }}
                </td>
                <td class="col-total">{{ number_format($item->line_total) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
