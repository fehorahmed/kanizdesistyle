@extends('layouts.app')
@section('title', 'Purchase Create')
@section('style')
    <style>
        .table td,
        .table th {
            padding: 6px;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <!-- form start -->
    <form enctype="multipart/form-data" action="{{ route('purchase_order.create') }}" class="form-horizontal" method="post">
        @csrf
        @php
            $submissionToken = (string) \Illuminate\Support\Str::uuid();
            session(['submission_token' => $submissionToken]); // store as string
        @endphp
        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Product Information </h3>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('supplier') ? 'has-error' : '' }}">
                                    <label for="supplier">
                                        Supplier <span class="text-danger">*</span>
                                        <button type="button" id="add_new_supplier" class="btn btn-primary bg-gradient-indigo btn-sm"><i class="fa fa-plus"></i></button>
                                    </label>
                                    <select class="form-control select2" id="supplier" name="supplier" required>
                                        <option value="">Search Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('supplier') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="select_product">Product <span class="text-danger">*</span>
                                        {{-- <button type="button" id="add_new_product" class="btn btn-primary bg-gradient-indigo btn-sm"><i class="fa fa-plus"></i></button> --}}
                                    </label>
                                    <select class="form-control select2" id="select_product" name="select_product">
                                        <option value="">Search Name of Product</option>
                                        @foreach ($products as $product)
                                            <option {{ old('product') == $product->id ? 'selected' : '' }}
                                                value="{{ $product->id }}">
                                                {{ $product->name }}-({{ $product->code }}) - ({{ $product->category->name??'' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_quantity">Qty <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_quantity"
                                        placeholder="Qty">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_unit_price">Cost Price <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_unit_price"
                                        placeholder="0">
                                    <small>Hint: <span id="unit_price_hint"></span></small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_selling_price">Selling MRP <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_selling_price"
                                        placeholder="0">
                                    <small>Hint: <span id="selling_price_hint"></span></small>
                                </div>
                            </div>
                            <div class="" style="max-width: 4%;">
                                <div class="form-group">
                                    <button type="button" style="margin-top: 31px;" id="add_new_btn"
                                        class="btn btn-primary bg-gradient-primary btn-sm btn-block"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">S/L</th>
                                            <th class="text-center">Item Details</th>
                                            <th class="text-center" width="10%">Qty</th>
                                            <th class="text-center" width="10%">Cost Price</th>
                                            <th class="text-center" width="10%">Selling MRP</th>
                                            <th class="text-center" width="10%">Tot Cost Price</th>
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
                                                    </td>
                                                    
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('quantity.' . $loop->index) }}"
                                                                class="form-control text-right quantity" name="quantity[]"
                                                                readonly>
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
                                                            class="form-group mb-0  {{ $errors->has('selling_price.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('selling_price.' . $loop->index) }}"
                                                                class="form-control text-right selling_price"
                                                                name="selling_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-purchase-cost"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr style="{{ (old('product') != null && sizeof(old('product'))) > 0 ? 'display: revert' : 'display: none' }}"
                                            id="footer_area">
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="text-right" id="total_quantity"></th>
                                            <th class="text-right" id="total_unit_price"></th>
                                            <th class="text-right" id="total_selling_unit_price"></th>
                                            <th class="text-right" id="total_purchase_price"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div
                                                            class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                                                            <label for="date">Date <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" autocomplete="off"
                                                                value="{{ old('date', date('d-m-Y')) }}" name="date"
                                                                id="date" class="form-control date-picker"
                                                                placeholder="Enter date">
                                                            @error('date')
                                                                <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Payment Type</label>
                                                            <select class="form-control select2" id="modal-pay-type"
                                                                name="payment_type">
                                                                <option value="">Select Option</option>
                                                                <option value="1"
                                                                    {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash
                                                                </option>
                                                                <option value="2"
                                                                    {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank
                                                                </option>
                                                                <option value="3"
                                                                    {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div id="mobile-bank-info">
                                                            <div class="form-group">
                                                                <label class="">Mobile Banking Account</label>
                                                                <select class="form-control select2" id="mobile_banking" name="mobile_banking">
                                                                    <option value="">Select Account</option>
                                                                    @foreach ($mobileBankings as $mobileBanking)
                                                                        <option value="{{ $mobileBanking->id }}">
                                                                            {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="modal-bank-info">
                                                            <div
                                                                class="form-group {{ $errors->has('account') ? 'has-error' : '' }}">
                                                                <label> Bank Account</label>
                                                                <select class="form-control select2" id="modal-account"
                                                                    name="account">
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
                                                            <div id="cheque_payment">
                                                                <div class="form-group">
                                                                    <label>Cheque No.</label>
                                                                    <input class="form-control" type="text"
                                                                        name="cheque_no" placeholder="Enter Cheque No."
                                                                        value="{{ old('cheque_no') }}">
                                                                </div>

                                                                <div
                                                                    class="form-group {{ $errors->has('cheque_image') ? 'has-error' : '' }}">
                                                                    <label>Cheque Image</label>
                                                                    <input class="form-control" name="cheque_image"
                                                                        type="file">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div
                                                            class="form-group {{ $errors->has('received_by') ? 'has-error' : '' }}">
                                                            <label for="received_by">Received By</label>
                                                            <input type="text" value="{{ old('received_by',auth()->user()->name) }}"
                                                                name="received_by" id="received_by" class="form-control"
                                                                placeholder="">
                                                            @error('received_by')
                                                                <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div
                                                            class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                                                            <label for="notes">Notes</label>
                                                            <input type="text" value="{{ old('notes') }}"
                                                                name="notes" id="notes" class="form-control"
                                                                placeholder="Enter Notes">
                                                            @error('notes')
                                                                <span class="help-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        {{-- <tr>
                                                            <th class="text-right">Discount(%) </th>
                                                            <td>
                                                                <div
                                                                    class="form-group {{ $errors->has('discount_percentage') ? 'has-error' : '' }}">
                                                                    <input type="text" class="form-control" name="discount_percentage"
                                                                        id="discount_percentage"
                                                                        value="{{ old('discount_percentage', 0) }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Discount Amt</th>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                                                                    <input type="text" class="form-control" name="discount"
                                                                        id="discount"
                                                                        value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount') }}">
                                                                </div>
                                                            </td>
                                                        </tr> --}}
                                                        <tr>
                                                            <th class="text-right">Total Amount</th>
                                                            <th id="total-amount">৳0.00</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Paid</th>
                                                            <td>
                                                                <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                                                                    <input type="text" class="form-control" name="paid"
                                                                        id="paid"
                                                                        value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid', 0) }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right">Due</th>
                                                            <th id="due-amount">৳0.00</th>
                                                            <input type="hidden" name="total" id="total">
                                                            <input type="hidden" name="due_total" id="due_total">
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.pack-body -->
                                    <div class="card-footer">
                                        <button type="submit"
                                            class="btn btn-primary bg-gradient-primary btn-sm float-right">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </form>
    <div class="modal fade" id="new_supplier_modal" tabindex="-1" aria-labelledby="new_supplier_modal_label" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="new_supplier_modal_label">Supplier Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="new_supplier_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Supplier Name</label>
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
                                        <label for="address">Address</label>
                                        <div class="input-group address">
                                            <textarea class="form-control is-valid-border" name="address"></textarea>
                                        </div>
                                        @error('address')
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
    <div class="modal fade" id="new_brand_modal" tabindex="-1" aria-labelledby="new_brand_modal_label" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="new_brand_modal_label">Brand Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="new_brand_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <div class="input-group name">
                                            <input type="text"
                                                class="form-control new_brand {{ $errors->has('name') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="name" value="{{ old('name') }}" placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <div class="input-group">
                                            <div class="icheck-success d-inline">
                                                <input checked type="radio" id="active" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                                <label for="active">
                                                    Active
                                                </label>
                                            </div>
            
                                            <div class="icheck-danger d-inline">
                                                <input type="radio" id="inactive" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                                <label for="inactive">
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
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
    <div class="modal fade" id="new_product_modal" tabindex="-1" aria-labelledby="new_product_modal_label" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="new_product_modal_label">Product Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="new_product_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Product Name <span class="text-danger">*</span></label>
                                        <div class="input-group name">
                                            <input type="text"
                                                class="form-control {{ $errors->has('name') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="name" value="{{ old('name') }}" placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand">Brand </label>
                                        <div class="input-group brand">
                                            <select name="brand" class="form-control select2">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option {{ old('brand') == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category </label>
                                        <div class="input-group category">
                                            <select name="category" id="category" class="form-control select2">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option {{ old('category') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sub_category">SubCategory </label>
                                        <div class="input-group sub_category">
                                            <select name="sub_category" id="sub_category" class="form-control select2">
                                                <option value="">Select SubCategory</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit">Unit <span class="text-danger">*</span></label>
                                        <div class="input-group unit">
                                            <select name="unit" class="form-control select2">
                                                <option value="">Select Unit</option>
                                                @foreach($units as $unit)
                                                    <option {{ old('unit') == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opening_due">Alert quantity</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Enter alert_quantity" name="alert_quantity" value="{{ old('alert_quantity', 0) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Enter Description" name="description" value="{{ old('description') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image">Product Image</label>
                                        <div class="input-group">
                                            <input type="file" name="image" class="form-control" id="image">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <div class="input-group">
                                            <div class="icheck-success d-inline">
                                                <input checked type="radio" id="active" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                                <label for="active">
                                                    Active
                                                </label>
                                            </div>
            
                                            <div class="icheck-danger d-inline">
                                                <input type="radio" id="inactive" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                                <label for="inactive">
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
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
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right quantity" name="quantity[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right selling_price"
                        name="selling_price[]">
                </div>
            </td>
            <td class="text-right total-purchase-cost"></td>
            <td class="text-center"><button type="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                        class="fa fa-trash-alt"></i></button></td>
        </tr>
    </template>

@endsection
@section('script')
    <script>
        $(function() {
            let selected_sub_category = '{{ old('sub_category',$product->sub_category_id) }}';
            $(document).on('change','#category',function(){
                let category = $(this).val();
                $('#sub_category').html('<option value="">Select SubCategory</option>');
                if(category != ''){
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_product_sub_category') }}',
                        data: {category: category}
                    }).done(function(response){
                        if(response.status){
                            $.each(response.sub_categories,function(i,item){
                                if(item.id == selected_sub_category){
                                    $('#sub_category').append('<option value="'+ item.id +'" selected>'+ item.name +'</option>');
                                }else{
                                    $('#sub_category').append('<option value="'+ item.id +'">'+ item.name +'</option>');
                                }
                            });
                        }
                    });
                }
            });
            $('#category').trigger('change');

            $('#add_new_supplier').click(function() {
                $('#new_supplier_modal').modal('show');
            });
            $('#new_supplier_form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#new_supplier_form')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('supplier.instant_add') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#new_supplier_modal').modal('hide');
                        $("#supplier").append('<option value="' + response.supplier.id +
                            '" selected>' + response.supplier.name + '</option>');
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
            $('#add_new_brand').click(function() {
                $('#new_brand_modal').modal('show');
            });
            $('#new_brand_form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#new_brand_form')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('brand.instant_add') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#new_brand_modal').modal('hide');
                        $("#brand").append('<option value="' + response.brand.id +
                            '" selected>' + response.brand.name + '</option>');
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
            $('#add_new_product').click(function() {
                $('#new_product_modal').modal('show');
            });
            $('#new_product_form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#new_product_form')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('product.instant_add') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#new_product_modal').modal('hide');
                        $("#select_product").append('<option value="' + response.product.id +
                            '" selected>' + response.product.name + '-('+ response.product.code + ')' + '</option>');
                        //$('.customer').trigger('change');
                        $('#select_pack_size').val(response.product.pack_size_id);
                        $('#select_pack_size').trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            });


            //Date picker
            $(".expiry_date").datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
            });
            $('#modal-pay-type').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#modal-bank-info').show();
                    $('#mobile-bank-info').hide();
                }else {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#modal-pay-type').trigger('change');

            $('#department').on('change', function() {
                // Get the selected Department ID
                var departmentId = $(this).val();

                // Construct the new URL
                var currentUrl = window.location.href.split('?')[0]; // Get the base URL without query parameters
                var newUrl = currentUrl + '?department=' + departmentId;

                // Update the browser's URL and reload the page
                window.location.href = newUrl;
            });
            $('#select_product').change(function(){
                let productId = $(this).val();
                $('#unit_price_hint').text('');
                $('#selling_price_hint').text('');
                if(productId){
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_and_inventory_info') }}",
                        data: {
                            id: productId
                        },
                    }).done(function(response) {
                        if (response.inventory) {
                            console.log(response);
                            if(response.inventory){
                                $('#unit_price_hint').text(response.inventory.unit_price);
                                $('#selling_price_hint').text(response.inventory.selling_price);
                            }
                        } else {
                            $('#unit_price_hint').text('');
                            $('#selling_price_hint').text('');
                        }
                    });
                }
            });
            // Handle batch selection

            calculate();
            var productIds = [];

            $(".product").each(function(index) {
                if ($(this).val() != '') {
                    productIds.push($(this).val());
                }
            });
            $('body').on('keypress',
                '#add_quantity,#add_unit_price,#add_selling_price,add_expiry_date,#add_pack_quantity',
                function(e) {
                    if (e.keyCode == 13) {
                        return false; // prevent the button click from happening
                    }
                });
            $('body').on('change', '#select_product', function(e) {
                let selectProductId = $(this).val();
                $('#add_unit_price').val(' ');
                if (selectProductId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_details') }}",
                        data: {
                            id: selectProductId
                        }
                    }).done(function(response) {
                        $('#add_unit_price').val(response.purchase_price);
                        $('#add_selling_price').val(response.selling_price);
                    });
                }
            });

            $('body').on('click', '#add_new_btn', function(e) {
                let selectProduct = $('#select_product').val();
                let selectProductName = $("#select_product option:selected").text();
                let addQuantity = $('#add_quantity').val()
                let addUnitPrice = $('#add_unit_price').val();
                let addSellingUnitPrice = $('#add_selling_price').val();

                if (selectProduct == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, select product !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addQuantity == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product quantity !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }

                if (addUnitPrice == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product unit price !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addSellingUnitPrice == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product selling unit price !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }

                if ($.inArray(selectProduct, productIds) != -1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: selectProductName + ' already exist in list.',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }

                if (selectProduct != '' && addQuantity != '' && addUnitPrice != '' && 
                    addSellingUnitPrice != '') {
                    var addMoreSound = document.getElementById("add_more_sound");
                    addMoreSound.play();
                    var html = $('#template-product').html();
                    var itemHtml = $(html);
                    $('#product-container').prepend(itemHtml);
                    var item = $('.product-item').first();
                    item.hide();
                    item.closest('tr').find('.product_name').text($("#select_product option:selected")
                        .text());
                    item.closest('tr').find('.product_name_val').val($("#select_product option:selected")
                        .text());
                    item.closest('tr').find('.product').val($("#select_product option:selected").val());
                    item.closest('tr').find('.quantity').val(addQuantity);
                    item.closest('tr').find('.unit_price').val(addUnitPrice);
                    item.closest('tr').find('.selling_price').val(addSellingUnitPrice);
                    productIds.push(selectProduct);
                    item.show();
                    calculate();
                    $('#select_product').val(null).trigger('change');
                    $('#add_quantity').val('');
                    $('#add_unit_price').val('');
                    $('#add_selling_price').val('');
                }
                return false; // prevent the button click from happening
            });

            $('body').on('click', '.btn-remove', function() {
                var product = $(this).closest('tr').find('.product').val();

                var removeItem = document.getElementById("remove_sound");
                removeItem.play();

                $(this).closest('.product-item').remove();
                calculate();
                productIds = $.grep(productIds, function(value) {
                    return value != product;
                });
            });
            $('body').on('keyup', 'input[type="number"],#discount_percentage,#discount,#paid',
                function() {
                    calculate();
                });
            $('body').on('change', 'input[type="number"]', function() {
                calculate();
            });
        })

        function calculate() {
            // Assuming you want to start the product_sl value from 1
            var productSl = 1;

            // Select all the table rows with the class .product-item
            var rows = $("#product-container .product-item");
            // Iterate over each row and update the product_sl value
            rows.each(function() {
                // Find the .product_sl element within the current row
                var productSlElement = $(this).find('.product_sl');
                // Update the text of the .product_sl element with the product_sl value
                productSlElement.text(productSl);
                // Increment the product_sl value for the next iteration
                productSl++;
            });
            var total_quantity = 0;
            var total_pack_size = 0;
            var total_selling_price = 0;
            var total_purchase_price = 0;
            var discount = parseFloat($('#discount').val() || 0);
            var paid = parseFloat($('#paid').val() || 0);
            $('.product-item').each(function(i, obj) {

                let quantity = parseFloat($('.quantity:eq(' + i + ')').val());
                quantity = (isNaN(quantity) || quantity < 0) ? 0 : quantity;

                let unit_price = parseFloat($('.unit_price:eq(' + i + ')').val());
                unit_price = (isNaN(unit_price) || unit_price < 0) ? 0 : unit_price;

                let selling_price = parseFloat($('.selling_price:eq(' + i + ')').val());
                selling_price = (isNaN(selling_price) || selling_price < 0) ? 0 : selling_price;


                $('.total-purchase-cost:eq(' + i + ')').text((quantity * unit_price).toFixed(2));
               
                total_quantity += quantity;
                total_selling_price += quantity * selling_price;
                total_purchase_price += quantity * unit_price;
            });

            $('#total_quantity').text(total_quantity);
            $('#total_selling_unit_price').text(total_selling_price.toFixed(2));
            $('#total_purchase_price').text(total_purchase_price.toFixed(2));

            if ($('#discount_percentage').val() >= 0) {
                discount = ($('#discount_percentage').val() * total_purchase_price) / 100;
                $('#discount').val(discount);
            } else {
                $('#discount_percentage').val(0);
            }

            var total = parseFloat(total_purchase_price) - discount;
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));
            var due_amount = parseFloat(total) - parseFloat(paid);
            $('#due-amount').html('৳' + due_amount.toFixed(2));
            $('#due_total').val(due_amount.toFixed(2));


            if (rows.length > 0) {
                $("#footer_area").show();
            } else {
                $("#footer_area").hide();
            }
        }
    </script>
@endsection
