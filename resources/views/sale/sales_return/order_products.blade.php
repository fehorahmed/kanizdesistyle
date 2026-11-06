@foreach ($orderProducts as $index => $orderItem)
    <tr class="previous-product-item">
        <td class="text-center">
            <span class="product_sl">{{ $index+1 }}</span>
        </td>
        <td class="text-left">
            <span class="product_name">{{ $orderItem->name }}</span>
            <input type="hidden" value="{{ $orderItem->name }}" name="product_name_val[]" class="product_name_val">
            <input type="hidden" value="{{ $orderItem->product_id }}" name="product[]" class="product">
            <input type="hidden" value="{{ $orderItem->id }}" name="product_id[]" class="product_id">
            <input type="hidden" value="{{ $orderItem->batch_no }}" name="batch[]" class="batch">
            <input type="hidden" value="{{ $orderItem->pack_size }}" name="pack_size[]" class="pack_size">
            <input type="hidden" value="{{ $orderItem->expiry_date }}" name="expiry_date[]" class="expiry_date">
        </td>
        {{-- <td class="text-center">
            <div class="form-group mb-0">
                <input type="text" value="{{ $orderItem->batch_no }}" class="form-control text-right order_batch" name="order_batch[]" readonly>
            </div>
        </td> --}}
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" readonly value="{{ $orderItem->pack_quantity }}" step="any" class="form-control text-right pack_quantity"
                    name="pack_quantity[]">
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" value="0" min="0" max="{{ $orderItem->pack_quantity }}" step="any" class="form-control text-right return_quantity"
                    name="return_quantity[]">
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" value="{{ $orderItem->discount_percentage }}" step="any" class="form-control text-right discount_percentage"
                    name="discount_percentage[]" readonly>
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" value="{{ ($orderItem->discount_percentage + 5) }}" step="any" class="form-control text-right deduct_percent"
                    name="deduct_percent[]" readonly>
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" value="0" step="any" class="form-control text-right deduct_amt"
                    name="deduct_amt[]" readonly>
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" readonly step="any" value="{{ $orderItem->unit_price }}" class="form-control text-right unit_price" name="unit_price[]">
            </div>
        </td>
        <td class="text-right">
            <div class="form-group mb-0">
                <input type="number" value="{{ $orderItem->discount_amt }}" step="any" class="form-control text-right discount_amt"
                    name="discount_amt[]" readonly>
                <input type="hidden" name="return_discount_amt[]" class="return_discount_amt" value="0"/> 
            </div>
        </td>
        <td class="text-right total-return-cost">0</td>
        <td class="text-right total_selling_cost">{{ $orderItem->total }}</td> 
    </tr>
@endforeach
