@extends('layouts.app')
@section('title', 'Order Processing')
@section('style')
    <style>
        .table td,
        .table th {
            padding: 6px;
            vertical-align: middle;
            font-size: 12px;
        }
    </style>
@endsection
@section('content')

    <!-- form start -->
    <form enctype="multipart/form-data" id="order_confirm_form" action="{{ route('pre_order.confirm',['order'=>$order->id]) }}" class="form-horizontal" method="post">
        @csrf
        <div class="row">
            <!-- left column -->
            <div class="col-md-9">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Order Information </h3>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            {{-- <div class="col-md-4">
                                <div class="form-group {{ $errors->has('department_id') ? 'has-error' : '' }}">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="department_id" name="department_id" required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id',$order->id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}
                            <input type="hidden" name="sister_concern_id" value="{{ $order->sister_concern_id }}">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('sale_type') ? 'has-error' : '' }}">
                                    <label for="sale_type">Sale Type <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="sale_type" name="sale_type" required>
                                        <option value="1"{{ old('sale_type',$order->sale_type) == 1 ? 'selected' : '' }}>Offline</option>
                                    </select>
                                    @error('sale_type')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' : '' }}">
                                    <label for="customer">Customer <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="customer" name="customer" required>
                                        <option value="">Search Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer',$order->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}-{{ $customer->mobile_no }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mt-1">
                                    <button type="button" style="margin-top: 31px;" id="add_new_customer_form"
                                        class="btn btn-primary bg-gradient-primary btn-sm btn-block"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- <div class="card-header">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="select_product">Product <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="select_product" name="select_product">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option {{ old('product') == $product->id ? 'selected' : '' }}
                                                value="{{ $product->id }}">
                                                {{ $product->name }}-{{$product->code ?? ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_batch">Batch<span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="add_batch">
                                        <option value="">Select Batch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_stock">Stock <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_stock"
                                        placeholder="Stock" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_quantity">Qty <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_quantity"
                                        placeholder="Qty">
                                </div>
                                <input type="hidden" step="any" class="form-control" id="add_pack_size"
                                    placeholder="Qty">
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="add_discount">Dis(%)</label>
                                    <input type="number" step="any" class="form-control" id="add_discount"
                                        placeholder="Dis">
                                    <input type="hidden" id="add_buying_price">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_unit_price">Selling Price <span class="text-danger">*</span></label>
                                    <input type="number" step="any" readonly class="form-control" id="add_unit_price"
                                        placeholder="Unit Price">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button type="button" style="margin-top: 31px;" id="add_new_btn"
                                        class="btn btn-primary bg-gradient-primary btn-sm btn-block"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">S/L</th>
                                            <th class="text-center" width="20%">Item Details</th>
                                            <th class="text-center">Batch</th>
                                            <th class="text-center">Stock</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Dis(%)</th>
                                            <th class="text-center">Selling Price</th>
                                            <th class="text-center">Dis Amt</th>
                                            <th class="text-center" width="10%">Total Price</th>
                                            <th class="text-center" width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-container">
                                        @if (old('product') != null && sizeof(old('product')) > 0)
                                            @foreach (old('product') as $key => $item)
                                                <tr class="product-item">
                                                    <td class="text-center">
                                                        <span class="product_sl">{{ ++$key }}</span>
                                                    </td>
                                                    <td
                                                        class="text-left {{ $errors->has('product.' . $loop->index) ? 'bg-gradient-danger' : '' }}">
                                                        <span
                                                            class="product_name">{{ old('product_name_val.' . $loop->index) }}</span>
                                                        <input type="hidden" name="product_name_val[]"
                                                            value="{{ old('product_name_val.' . $loop->index) }}"
                                                            class="product_name_val">
                                                        <input type="hidden" name="product[]"
                                                            value="{{ old('product.' . $loop->index) }}" class="product">
                                                        <input type="hidden" name="serial[]"
                                                            value="{{ old('serial.' . $loop->index) }}"
                                                            class="product_code">
                                                    </td>
                                                    @php
                                                        $batches = \App\Models\PurchaseInventory::where('sister_concern_id',old('sister_concern_id'))->where('product_id',old('product.' . $loop->index))->where('quantity','>',0)->get();
                                                    @endphp
                                                    <td>
                                                        <div class="form-group mb-0 {{ $errors->has('batch.' . $loop->index) ? 'has-error' : '' }}">
                                                            <select class="form-control select2 d-none batch_select" name="batch[]">
                                                                <option value="">Select Batch</option>
                                                                @foreach ($batches as $batch)
                                                                    <option value="{{ $batch->batch_no }}" {{ old('batch.' . $loop->index) == $batch->batch_no ? 'selected' : '' }}>Batch-{{ $batch->batch_no }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('stock.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('stock.' . $loop->index) }}"
                                                                class="form-control text-right stock" name="stock[]"
                                                                readonly>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('quantity.' . $loop->index) }}"
                                                                class="form-control text-right quantity"
                                                                name="quantity[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('discount.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('discount.' . $loop->index) }}"
                                                                class="form-control text-right discount"
                                                                name="discount[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('unit_price.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('unit_price.' . $loop->index) }}"
                                                                class="form-control text-right unit_price"
                                                                name="unit_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('discount_amt.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('discount_amt.' . $loop->index) }}"
                                                                class="form-control text-right discount_amt"
                                                                name="discount_amt[]" readonly>
                                                            <input type="hidden" name="buying_price[]" value="{{ old('buying_price.' . $loop->index) }}" class="buying_price">
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-selling-cost"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($order->products as $orderProduct)
                                                @php
                                                    $stockItem = \App\Models\PurchaseInventory::where('sister_concern_id',$order->sister_concern_id)->where('product_id',$orderProduct->product_id)->where('batch_no',$orderProduct->batch_no)->first()->quantity ?? 0;
                                                    $batches = \App\Models\PurchaseInventory::where('sister_concern_id',$order->sister_concern_id)->where('product_id',$orderProduct->product_id)->where('quantity','>',0)->get();
                                                @endphp
                                                <tr class="product-item">
                                                    <td class="text-center">
                                                        <span class="product_sl"></span>
                                                    </td>
                                                    <td class="text-left">
                                                        <span class="product_name">{{ $orderProduct->product->name??'' }}</span>
                                                        <input type="hidden" name="product_name_val[]" value="{{ $orderProduct->product->name??'' }}" class="product_name_val">
                                                        <input type="hidden" name="product[]" value="{{ $orderProduct->product->id??'' }}" class="product">
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <select class="form-control select2 d-none batch_select" name="batch[]">
                                                                <option value="">Select Batch</option>
                                                                @foreach ($batches as $batch)
                                                                    <option value="{{ $batch->batch_no }}">Batch-{{ $batch->batch_no }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="any" value="{{ $stockItem }}" class="form-control text-right stock" name="stock[]" readonly>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="any" value="{{ $orderProduct->quantity }}" class="form-control text-right quantity"
                                                                name="quantity[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="any" value="{{ $orderProduct->discount_percentage }}" class="form-control text-right discount" name="discount[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="any" value="{{ $orderProduct->unit_price }}" class="form-control text-right unit_price" name="unit_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="any" value="{{ $orderProduct->discount_amt }}" class="form-control text-right discount_amt"
                                                                name="discount_amt[]" readonly>
                                                            <input type="hidden" name="buying_price[]" value="{{ $orderProduct->buy_price }}" class="buying_price">
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-selling-cost"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr style="{{ (old('product') != null && sizeof(old('product'))) > 0 ? 'display: revert' : 'display: none' }}"
                                            id="footer_area">
                                            <th class="text-right" colspan="4">Total</th>
                                            <th class="text-right" id="total_quantity"></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-right" id="total_dis_price"></th>
                                            <th class="text-right" id="total_selling_price"></th>
                                            <th></th>
                                            <input type="hidden" id="sub_total" name="sub_total">
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right">Discount Amount</th>
                                            <th class="text-right" id="total-dis-amount">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Previous Paid</th>
                                            <th colspan="3">৳{{ number_format($order->paid,2) }}</th>
                                            <th colspan="3" class="text-right">Total Payable Amt</th>
                                            <th class="text-right" id="total-amount">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="text-right">Shipping Charge</th>
                                            <th class="text-right">৳{{ number_format($order->shipping_charge,2) }}</th>
                                            <input type="hidden" name="shipping_charge" id="total_shipping_charge" value="{{ $order->shipping_charge }}">
                                        </tr>
                                        <tr>
                                            <th colspan="8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Payment Type</label>
                                                            <select class="form-control select2" id="modal_pay_type" name="payment_type">
                                                                <option value="">Select Option</option>
                                                                <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash
                                                                </option>
                                                                <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank(Card)
                                                                </option>
                                                                <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="modal_mobile_bank_info">
                                                            <div class="form-group {{ $errors->has('mobile_banking') ? 'has-error' : '' }}">
                                                                <label>Mobile Banking Account</label>
                                                                <select class="form-control select2" id="modal_mobile_banking" name="mobile_banking">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($mobileBankings as $mobileBanking)
                                                                        <option value="{{ $mobileBanking->id }}">
                                                                            {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="modal_bank_info">
                                                            <div class="form-group {{ $errors->has('account') ? 'has-error' : '' }}">
                                                                <label> Bank Account</label>
                                                                <select class="form-control select2" id="modal_account" name="account">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($accounts as $account)
                                                                        <option value="{{ $account->id }}">
                                                                            {{ $account->account_no }} -
                                                                            {{ $account->branch->name ?? '' }} -
                                                                            {{ $account->bank->name ?? '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </th>
                                            <td>
                                                <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                                                    <label>Paid</label>
                                                    <input type="text" class="form-control text-right" name="paid"
                                                        id="paid"
                                                        value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid', 0) }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Payment Type (Additional)</label>
                                                            <select class="form-control select2" id="modal_pay_type_two" name="payment_type_two">
                                                                <option value="">Select Option</option>
                                                                <option value="1" {{ old('payment_type_two') == '1' ? 'selected' : '' }}>Cash
                                                                </option>
                                                                <option value="2" {{ old('payment_type_two') == '2' ? 'selected' : '' }}>Bank(Card)
                                                                </option>
                                                                <option value="3" {{ old('payment_type_two') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="modal_mobile_bank_info_two">
                                                            <div class="form-group {{ $errors->has('mobile_banking_two') ? 'has-error' : '' }}">
                                                                <label>Mobile Banking Account</label>
                                                                <select class="form-control select2" id="modal_mobile_banking_two" name="mobile_banking_two">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($mobileBankings as $mobileBanking)
                                                                        <option value="{{ $mobileBanking->id }}">
                                                                            {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="modal_bank_info_two">
                                                            <div class="form-group {{ $errors->has('account_two') ? 'has-error' : '' }}">
                                                                <label> Bank Account</label>
                                                                <select class="form-control select2" id="modal_account_two" name="account_two">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($accounts as $account)
                                                                        <option value="{{ $account->id }}">
                                                                            {{ $account->account_no }} -
                                                                            {{ $account->branch->name ?? '' }} -
                                                                            {{ $account->bank->name ?? '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </th>
                                            <td>
                                                <div class="form-group {{ $errors->has('paid_two') ? 'has-error' : '' }}">
                                                    <label>Paid</label>
                                                    <input type="text" class="form-control text-right" name="paid_two"
                                                        id="paid_two"
                                                        value="{{ empty(old('paid_two')) ? ($errors->has('paid_two') ? '' : '0') : old('paid_two', 0) }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Payment Type (Additional)</label>
                                                            <select class="form-control select2" id="modal_pay_type_three" name="payment_type_three">
                                                                <option value="">Select Option</option>
                                                                <option value="1" {{ old('payment_type_three') == '1' ? 'selected' : '' }}>Cash
                                                                </option>
                                                                <option value="2" {{ old('payment_type_three') == '2' ? 'selected' : '' }}>Bank(Card)
                                                                </option>
                                                                <option value="3" {{ old('payment_type_three') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="modal_mobile_bank_info_three">
                                                            <div class="form-group {{ $errors->has('mobile_banking_three') ? 'has-error' : '' }}">
                                                                <label>Mobile Banking Account</label>
                                                                <select class="form-control select2" id="modal_mobile_banking_three" name="mobile_banking_three">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($mobileBankings as $mobileBanking)
                                                                        <option value="{{ $mobileBanking->id }}">
                                                                            {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="modal_bank_info_three">
                                                            <div class="form-group {{ $errors->has('account_three') ? 'has-error' : '' }}">
                                                                <label> Bank Account</label>
                                                                <select class="form-control select2" id="modal_account_three" name="account_three">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($accounts as $account)
                                                                        <option value="{{ $account->id }}">
                                                                            {{ $account->account_no }} -
                                                                            {{ $account->branch->name ?? '' }} -
                                                                            {{ $account->bank->name ?? '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </th>
                                            <td>
                                                <div class="form-group {{ $errors->has('paid_three') ? 'has-error' : '' }}">
                                                    <label>Paid</label>
                                                    <input type="text" class="form-control text-right" name="paid_three"
                                                        id="paid_three"
                                                        value="{{ empty(old('paid_three')) ? ($errors->has('paid_three') ? '' : '0') : old('paid_three', 0) }}">
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="8" class="text-right">Due</th>
                                            <th class="text-right" id="due-amount">৳0.00</th>
                                            <input type="hidden" name="total" id="total">
                                            <input type="hidden" name="due_total" id="due_total">
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="button" id="order_confirm_submit" class="btn btn-primary bg-gradient-primary btn-sm">Process</button>
                        <a href="" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->

                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-3">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Other Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('order_no') ? 'has-error' : '' }}">
                                    <label for="date">Order No <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" value="{{ old('order_no', $order->order_no) }}"
                                        name="order_no" id="order_no" class="form-control"
                                        placeholder="Enter Order No" readonly>
                                    @error('order_no')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" value="{{ old('date', date('d-m-Y',strtotime($order->date))) }}"
                                        name="date" id="date" class="form-control date-picker"
                                        placeholder="Enter date">
                                    @error('date')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('received_by') ? 'has-error' : '' }}">
                                    <label for="received_by">Received By</label>
                                    <input type="text" value="{{ old('received_by',auth()->user()->name) }}" name="received_by"
                                        id="received_by" class="form-control" placeholder="">
                                    @error('received_by')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                                    <label for="notes">Notes</label>
                                    <input type="text" value="{{ old('notes',$order->note) }}" name="notes" id="notes"
                                        class="form-control" placeholder="Enter Notes">
                                    @error('notes')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer"></div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
            <!--/.col (left) -->
        </div>
    </form>

    <template id="template-product">
        <tr class="product-item">
            <td class="text-center">
                <span class="product_sl"></span>
            </td>
            <td class="text-left">
                <span class="product_name"></span>
                <input type="hidden" name="product_name_val[]" class="product_name_val">
                <input type="hidden" name="product[]" class="product">
            </td>
            <td class="text-center">
                <div class="form-group mb-0">
                    <input type="text" class="form-control text-right batch" name="batch[]" readonly>
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right stock" name="stock[]" readonly>
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right quantity"
                        name="quantity[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right discount" name="discount[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right discount_amt"
                        name="discount_amt[]" readonly>
                    <input type="hidden" name="buying_price[]" class="buying_price">
                </div>
            </td>
            <td class="text-right total-selling-cost"></td>
            <td class="text-center"><button type="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                        class="fa fa-trash-alt"></i></button></td>
        </tr>
    </template>

    <!-- Modal for new Customer-->
    <div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="newCustomerModalLabel">Customer Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="newCustomerForm">
                        <div class="container">
                            <div class="row">
                                <input type="hidden" name="department" value="1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Customer Name</label>
                                        <div class="input-group name">
                                            <input type="text"
                                                class="form-control new_customer {{ $errors->has('name') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="name" value="{{ old('name') }}" placeholder="Name">
                                        </div>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_no">Mobile No</label>
                                        <div class="input-group mobile_no">
                                            <input type="text"
                                                class="form-control {{ $errors->has('mobile_no') ? 'is-invalid' : 'is-valid-border' }}"
                                                placeholder="Enter Mobile No." name="mobile_no"
                                                value="{{ old('mobile_no') }}">
                                        </div>
                                        @error('mobile_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opening_due">Email</label>
                                        <div class="input-group email">
                                            <input type="text"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="email" placeholder="Email">
                                        </div>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date Of Birth</label>
                                        <div class="input-group date_of_birth">
                                            <input type="text"
                                                class="form-control date-picker {{ $errors->has('date_of_birth') ? 'is-invalid' : 'is-valid-border' }}"
                                                id="date_of_birth" name="date_of_birth"
                                                value="{{ old('date_of_birth') }}" autocomplete="off">
                                        </div>
                                        @error('date_of_birth')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <div class="input-group address">
                                            <textarea class="form-control is-valid-border" rows="3" name="address"></textarea>
                                        </div>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">SEX</label>
                                        <div class="col-sm-10">
                                            <div class="icheck-success d-inline">
                                                <input checked type="radio" id="active" name="sex"
                                                    value="Male" {{ old('sex') == 'Male' ? 'checked' : '' }}>
                                                <label for="active">
                                                    Male
                                                </label>
                                            </div>

                                            <div class="icheck-danger d-inline">
                                                <input type="radio" id="inactive" name="sex" value="Female"
                                                    {{ old('sex') == 'Female' ? 'checked' : '' }}>
                                                <label for="inactive">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                        @error('sex')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-danger bg-gradient-danger btn-sm float-right"
                                data-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(function() {
            $('body').on('click', '#order_confirm_submit', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You want to processing this order!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#order_confirm_form').submit();
                    }
                });
            });

            $('.select2').select2();
            $('#add_new_customer_form').click(function() {
                $('#newCustomerModal').modal('show');
            });
            $('#newCustomerForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#newCustomerForm')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('customer.add_ajax_customer') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#newCustomerModal').modal('hide');
                        $("#customer").append('<option value="' + response.customer.id +
                            '" selected>' + response.customer.name + '</option>');
                        //$('.customer').trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            });
            // Initialize datepickers
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });

            // Toggle cheque payment section
            $('#cheque_status').change(function() {
                if ($(this).val() == '1') {
                    $('#cheque_payment').show();
                } else {
                    $('#cheque_payment').hide();
                }
            });

            // Toggle modal bank info section
            $('#modal_pay_type').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info').hide();
                    $('#modal_mobile_bank_info').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info').show();
                    }
                } else if($(this).val() == '2'){
                    $('#modal_bank_info').show();
                    $('#modal_mobile_bank_info').hide();
                }else{
                    $('#modal_bank_info').hide();
                    $('#modal_mobile_bank_info').hide();
                }
            }).trigger('change');

            // Toggle modal bank info section
            $('#modal_pay_type_two').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info_two').hide();
                    $('#modal_mobile_bank_info_two').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info_two').show();
                    }
                } else if($(this).val() == '2'){
                    $('#modal_bank_info_two').show();
                    $('#modal_mobile_bank_info_two').hide();
                }else{
                    $('#modal_bank_info_two').hide();
                    $('#modal_mobile_bank_info_two').hide();
                }
            }).trigger('change');

            // Toggle modal bank info section
            $('#modal_pay_type_three').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info_three').hide();
                    $('#modal_mobile_bank_info_three').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info_three').show();
                    }
                } else if($(this).val() == '2'){
                    $('#modal_bank_info_three').show();
                    $('#modal_mobile_bank_info_three').hide();
                }else{
                    $('#modal_bank_info_three').hide();
                    $('#modal_mobile_bank_info_three').hide();
                }
            }).trigger('change');

            // Reset product-related fields on department change
            $('body').on('change', '#department_id', function() {
                $('#add_stock, #add_unit_price').val('');
                $('#select_product').val(null).trigger('change');
            });

            calculate();

            // Keep track of selected products
            let productIds = [];
            $(".product").each(function() {
                if ($(this).val() != '') {
                    productIds.push($(this).val());
                }
            });

            // Prevent default action for Enter key on numeric inputs
            $('body').on('keypress', '#add_quantity, #add_discount, #add_unit_price', function(
                e) {
                if (e.keyCode == 13) return false;
            });

            // Handle product selection
            $('body').on('change', '#select_product', function() {
                let departmentId = $('#department_id').val();

                if (!departmentId) {
                    alert('Please select a Department first!');
                    return;
                }

                let productId = $(this).val();
                $('#add_unit_price,#add_stock').val('');
                $('#add_batch').empty().append('<option value="">Search Batch</option>');

                if (productId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory') }}",
                        data: {
                            id: productId,
                            department_id: departmentId
                        },
                    }).done(function(response) {
                        if (response) {
                            $('#add_stock').val(response.quantity || 0);
                            $('#add_unit_price').val(response.selling_price || 0);

                            if (response.batches.length > 0) {
                                response.batches.forEach(batch => {
                                    $('#add_batch').append(
                                        `<option value="${batch}">Batch-${batch}</option>`
                                    );
                                });
                            }
                        } else {
                            $('#add_stock').val(0);
                        }
                    });
                }
            });

            // Handle batch selection
            $('body').on('change', '#add_batch', function() {
                let departmentId = $('#department_id').val();
                let productId = $('#select_product').val();

                if (!departmentId || !productId) {
                    alert('Department or product not found!');
                    return;
                }

                let batchId = $(this).val();
                $('#add_unit_price,#add_expiry_date,#add_stock').val('');

                if (batchId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory_batch_wise') }}",
                        data: {
                            id: batchId,
                            product_id: productId,
                            department_id: departmentId
                        },
                    }).done(function(response) {
                        if (response) {
                            $('#add_stock').val(response.quantity || 0);
                            $('#add_buying_price').val(response.buying_price || 0);
                            $('#add_unit_price').val(response.selling_price || 0);
                        } else {
                            $('#add_stock').val(0);
                        }
                    });
                }
            });

            // Handle batch selection
            $('body').on('change', '.batch_select', function() {
                let currentItem = $(this).closest('.product-item');
                currentItem.find('.stock').val(0);
                currentItem.find('.buying_price').val(0);
                let productId = currentItem.find('.product').val();

                if (!productId) {
                    alert('Product not found!');
                    return;
                }
                
                let batchId = $(this).val();

                if (batchId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory_batch_wise') }}",
                        data: {
                            id: batchId,
                            product_id: productId,
                            sister_concern_id: 1
                        },
                    }).done(function(response) {
                        if (response) {
                            console.log(response);
                            currentItem.find('.stock').val(response.quantity || 0);
                            currentItem.find('.buying_price').val(response.buying_price || 0);
                        } else {
                            currentItem.find('.stock').val(0);
                            currentItem.find('.buying_price').val(0);
                        }
                    });
                }
            });



            // Add new product row
            $('body').on('click', '#add_new_btn', function() {
                let selectProduct = $('#select_product').val();
                let selectProductName = $("#select_product option:selected").text();
                let addBatch = $('#add_batch').val();
                let addStock = parseFloat($('#add_stock').val()) || 0; // Stock quantity
                let addQuantity = parseFloat($('#add_quantity').val()) || 0; // Entered quantity
                let addDiscount = parseFloat($('#add_discount').val()) || 0; // Discount quantity
                let addUnitPrice = $('#add_unit_price').val();
                let addBuyingPrice = $('#add_buying_price').val();

                if (!validateInputs(selectProduct, addQuantity, addUnitPrice, addBatch)) {
                    return false;
                }
                if(addDiscount > 0){
                    let addDiscountPrice = addUnitPrice * (addDiscount/100);
                    if(addDiscountPrice > addBuyingPrice){
                        Swal.fire({
                            icon: 'error',
                            title: 'Discount Exceeded',
                            text: `The entered Discount exceeds the product buying price! Please decrease discount.`
                        });
                        let notificationSound = document.getElementById('notification-error-audio');
                        notificationSound?.play();
                        return false;
                    }
                }
                
                // Check if entered quantity exceeds stock
                if (addQuantity > addStock) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock Exceeded',
                        text: `The entered quantity (${addQuantity}) exceeds the available stock (${addStock})! Please select another batch.`
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }

                // Check for duplicates
                let isDuplicate = false;
                $("#product-container .product-item").each(function() {
                    let existingProduct = $(this).find('.product').val();
                    let existingBatch = $(this).find('.batch').val().replace('Batch-',
                        ''); // Remove "Batch-" prefix

                    if (existingProduct === selectProduct && existingBatch === addBatch) {
                        isDuplicate = true;
                        return false; // Exit loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Entry',
                        text: 'This product and batch are already added!'
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }

                let addMoreSound = document.getElementById("add_more_sound");
                addMoreSound.play();

                let html = $('#template-product').html();
                let itemHtml = $(html);
                $('#product-container').prepend(itemHtml);

                let item = $('.product-item').first();
                populateProductRow(item, selectProductName, selectProduct, addBatch,
                    addStock, addQuantity, addDiscount,addBuyingPrice, addUnitPrice);

                productIds.push(selectProduct);
                resetProductFields();
                calculate();
            });


            // Remove product row
            $('body').on('click', '.btn-remove', function() {
                let product = $(this).closest('tr').find('.product').val();

                let removeSound = document.getElementById("remove_sound");
                removeSound.play();

                $(this).closest('.product-item').remove();
                productIds = productIds.filter(id => id != product);
                calculate();
            });

            // Update calculations on input change
            $('body').on('keyup change', 'input[type="number"], #discount_percentage, #discount',
                function() {
                    calculate();
                });
            // Update calculations on input change
            $('body').on('keyup change', '#paid',
                function() {
                    calculatePaid();
                });
            // Update calculations on input change
            $('body').on('keyup change', '#paid_two',
                function() {
                    calculatePaidTwo();
                });
            $('body').on('focus','#paid_two',function(){
                var total = $('#total').val() || 0;
                let paid = $('#paid').val() || 0;
                let paid_two = $('#paid_two').val() || 0;
                let paid_three = $('#paid_three').val() || 0;
                let dueAmount = total - paid - paid_two - paid_three;

                $('#paid_two').val(dueAmount);

                paid = $('#paid').val() || 0;
                paid_two = $('#paid_two').val() || 0;
                paid_three = $('#paid_three').val() || 0;
                dueAmount = total - paid - paid_two - paid_three;
                
                $('#due_total').val(dueAmount.toFixed(2));
                $('#due-amount').html('৳' + dueAmount.toFixed(2));
            });    
            // Update calculations on input change
            $('body').on('keyup change', '#paid_three',
                function() {
                    calculatePaidThree();
                });

            $('body').on('focus','#paid_three',function(){
                var total = $('#total').val() || 0;
                let paid = $('#paid').val() || 0;
                let paid_two = $('#paid_two').val() || 0;
                let paid_three = $('#paid_three').val() || 0;
                let dueAmount = total - paid - paid_two - paid_three;

                $('#paid_three').val(dueAmount);

                paid = $('#paid').val() || 0;
                paid_two = $('#paid_two').val() || 0;
                paid_three = $('#paid_three').val() || 0;
                dueAmount = total - paid - paid_two - paid_three;

                $('#due_total').val(dueAmount.toFixed(2));
                $('#due-amount').html('৳' + dueAmount.toFixed(2));
            });  
        });

        function validateInputs(selectProduct, addQuantity, addUnitPrice, addBatch) {
            let errors = [];

            if (!selectProduct) errors.push('Please, select product!');
            if (!addQuantity) errors.push('Please, type product quantity!');
            if (!addUnitPrice) errors.push('Please, type product unit price!');
            if (!addBatch) errors.push('Please, select batch!');

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errors[0]
                });
                let notificationSound = document.getElementById('notification-error-audio');
                notificationSound?.play();
                return false;
            }

            return true;
        }

        function populateProductRow(item, name, id, batch, stock, quantity, discount,buying_price,
            unitPrice) {
            item.hide();
            item.find('.product_name').text(name);
            item.find('.product_name_val').val(name);
            item.find('.product').val(id);
            item.find('.batch').val('Batch-' + batch);
            item.find('.stock').val(stock);
            item.find('.quantity').val(quantity);
            item.find('.discount').val(discount);
            item.find('.buying_price').val(buying_price);
            item.find('.unit_price').val(unitPrice);
            item.show();
        }

        function resetProductFields() {
            $('#select_product').val(null).trigger('change');
            $('#add_batch, #add_stock,#add_quantity,#add_discount, #add_unit_price')
                .val('');
        }

        function calculate() {
            let productSl = 1;
            let totalQuantity = 0;
            let totalDisPrice = 0;
            let totalSellingPrice = 0;
            let discount = parseFloat($('.discount').val()) || 0;
            

            $("#product-container .product-item").each(function() {
                $(this).find('.product_sl').text(productSl++);

                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let buyingPrice = parseFloat($(this).find('.buying_price').val()) || 0;
                let productDiscount = parseFloat($(this).find('.discount').val()) || 0; // per product discount
                // if(productDiscount > 0){
                //     let discountAmount = unitPrice * (productDiscount / 100);
                //     if(discountAmount > buyingPrice){
                //         Swal.fire({
                //             icon: 'error',
                //             title: 'Discount Exceeded',
                //             text: `The entered Discount exceeds the product buying price! Please decrease discount.`
                //         });
                //         let notificationSound = document.getElementById('notification-error-audio');
                //         notificationSound?.play();
                //         $(this).find('.discount').val(0);
                //         calculate();
                //         return false;
                //     }
                // }
                $(this).find('.total-selling-cost').text((quantity * unitPrice).toFixed(2));

                // Use the individual product discount if present
                let discountAmt = productDiscount > 0 ?
                    (quantity * (productDiscount * unitPrice) / 100) :
                    0;

                $(this).find('.discount_amt').val(discountAmt.toFixed(2));

                totalQuantity += quantity;
                totalSellingPrice += quantity * unitPrice;
                totalDisPrice += discountAmt;
            });

            $('#total_quantity').text(totalQuantity);
            $('#total_dis_price').text(Math.floor(totalDisPrice));
            $('#total_selling_price').text(Math.ceil(totalSellingPrice));
            $('#sub_total').val(Math.ceil(totalSellingPrice));

            // let total_shipping_charge = $('#total_shipping_charge').val();
            let coupon_discount = $('#coupon_discount').val();
            let total = Math.ceil(totalSellingPrice - Math.floor(totalDisPrice) - coupon_discount);
            $('#total-dis-amount').html('৳' + Math.floor(totalDisPrice));
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));

            let oldPaid = '{{ old('paid') }}';

            if (!oldPaid){
                $('#paid').val(0);
            }
            let paid = parseFloat($('#paid').val()) || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;

            let dueAmount = Math.ceil(total - paid - paid_two - paid_three);
            $('#due-amount').html('৳' + dueAmount.toFixed(2));
            $('#due_total').val(dueAmount.toFixed(2));

            $('#tr-next-payment').toggle(dueAmount > 0);
            $("#footer_area").toggle($("#product-container .product-item").length > 0);
        }
        function calculatePaid(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;
            
            $('#due_total').val(dueAmount.toFixed(2));
            $('#due-amount').html('৳' + dueAmount.toFixed(2));
        }
        function calculatePaidTwo(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;
            
            $('#due_total').val(dueAmount.toFixed(2));
            $('#due-amount').html('৳' + dueAmount.toFixed(2));
        }
        function calculatePaidThree(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;
            
            $('#due_total').val(dueAmount.toFixed(2));
            $('#due-amount').html('৳' + dueAmount.toFixed(2));
        }
    </script>

@endsection
