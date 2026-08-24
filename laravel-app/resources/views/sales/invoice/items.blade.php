<div class="bold center">
    کالاهای خریداری شده
</div>
<div class="line"></div>

@foreach ($sale->items as $item)
    <div class="item">

        <div class="bold">
            {{ $item->product->name }}
        </div>

        <div class="row">

            <span>
                {{ number_format($item->quantity, 0) }}
                ×
                {{ number_format($item->unit_price) }}
            </span>

            <span>
                {{ number_format($item->line_total) }}
            </span>

        </div>

    </div>
    
    <div class="line"></div>
@endforeach
