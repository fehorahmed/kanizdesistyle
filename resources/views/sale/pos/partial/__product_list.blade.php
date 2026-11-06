<div class="row" >
    @if(count($products) > 0)
    @foreach($products as $key => $product)
        <div data-id="{{ $product->id }}" data-quantity="{{ $product->inventory->quantity ?? 0 }}" data-name="{{ $product->name }}" data-price="{{ $product->inventory->selling_price ?? 0 }}" class="col-4 col-md-2 {{ ($product->inventory->quantity ?? 0) > 0 ? 'add-product' : '' }}  product-item-{{ $product->id }}"
             style="cursor: {{ ($product->inventory->quantity ?? 0) > 0 ? 'pointer' : 'not-allowed' }}">
            <div class="card" style="{{ ($product->inventory->quantity ?? 0) == 0 ? 'background: #ffaeb6;' : '' }}">
                <div class="card-header" style="padding: 4px;">
                    <div class="card-title">{{ substr($product->name,-11) }}</div>
                    <div class="card-title" style="float: right;"><b>{{ number_format($product->inventory->selling_price ?? 0 )}}</b></div>
                </div>
                <div class="card-body" style="padding: 4px;text-align: center;">
                    <img style="width: 118px;height: 70px;object-fit: fill;" src="{{ asset($product->thumbs ?? 'img/no-image.webp') }}" alt="">
                </div>
                <div class="card-footer text-center" style="padding: 4px;">
                    <b class="text-indigo">{{ $product->code }}</b>
                </div>
            </div>
        </div>
    @endforeach
    @else
        <div class="col text-center mt-4">
            <h5 class="text-danger">No available products !</h5>
        </div>
    @endif
</div>
