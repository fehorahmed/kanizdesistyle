@foreach ($orderPayments as $payment)
    <tr class="return_order_payment_row">
        <th class="text-right">
            @if ($payment->transaction_method == 1)
                Cash
                <input type="hidden" name="payment_method_name[]" value="Cash"/>
                <input type="hidden" name="payment_method_desc[]" value=""/>
            @elseif ($payment->transaction_method == 2)
                Bank<br/>
                {{ $payment->bank->name??'' }}, {{ $payment->branch->name??'' }}, {{ $payment->account->account_no??'' }}
                <input type="hidden" name="payment_method_name[]" value="Bank"/>
                <input type="hidden" name="payment_method_desc[]" value="{{ $payment->bank->name??'' }}, {{ $payment->branch->name??'' }}, {{ $payment->account->account_no??'' }}"/>
            @elseif ($payment->transaction_method == 3)
                Mobile Banking<br/>
                {{ $payment->mobileBanking->account_name??'' }}
                <input type="hidden" name="payment_method_name[]" value="Mobile Banking"/>
                <input type="hidden" name="payment_method_desc[]" value="{{ $payment->mobileBanking->account_name??'' }}"/>
            @endif
            <input type="hidden" name="payment_id[]" value="{{ $payment->id }}"/>
        </th>
        <th class="text-right">
            <div class="form-group">
                <input type="text" class="form-control payment_amount text-right" name="payment_amount[]" value="{{ $payment->amount }}">
            </div>
        </th>
    </tr>
@endforeach
