<ol>
    @foreach ($sub_categories as $index => $sub_category)
        <li>
            {{ $sub_category->name }}
            @if ($sub_category->products)
                <ul style="list-style: none;">
                    @foreach ($sub_category->products as $product)
                        <li><i class="fas fa-long-arrow-right"></i> <a href="{{ route('batch_wise_inventory_fm.all',['productId'=>$product->id]) }}">{{ $product->pack_size->name??'' }} &nbsp; | &nbsp; {{ $product->name }} &nbsp; | &nbsp; Stock: {{ $product->stock }}</a></li>
                    @endforeach
                </ul>
            @endif
        </li> 
    @endforeach
</ol>