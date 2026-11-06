@extends('layouts.app')
@section('title',' Balance Sheet')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Bank Details</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Bank Name</th>
                                        <th>Branch Name</th>
                                        <th>Account No.</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($bankAccounts as $account)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->bank->name }}</td>
                                            <td>{{ $account->branch->name }}</td>
                                            <td>{{ $account->account_no }}</td>
                                            <td class="text-right">{{ number_format($account->balance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="3"></th>
                                            <th class="text-right">Total</th>
                                            <th class="text-right">{{ number_format($bankAccounts->sum('balance'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Cash & Mobile Banking</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Cash</td>
                                        <td class="text-right">{{ number_format($cash->amount, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>Mobile Banking</td>
                                        <td class="text-right">{{ number_format($mobileBanking->amount, 2) }}</td>
                                    </tr>
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">{{ number_format($cash->amount+$mobileBanking->amount, 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Customer Payment</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="customer-payment-table">
                                    <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Address</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th class="text-right">Total</th>
                                            <th class="text-right">{{ number_format($customerTotalPaid, 2) }}</th>
                                            <th class="text-right">{{ number_format($customerTotalDue, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Supplier Payment</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="supplier-payment-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($suppliers as $supplier)
                                            <tr>
                                                <td>{{ $supplier->name }}</td>
                                                <td>{{ $supplier->mobile }}</td>
                                                <td>{{ number_format($supplier->total, 2) }}</td>
                                                <td>{{ number_format($supplier->paid, 2) }}</td>
                                                <td>{{ number_format($supplier->due, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">{{ number_format($suppliers->sum('paid'), 2) }}</th>
                                        <th class="text-right">{{ number_format($suppliers->sum('due'), 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Stock Summary</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventory-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Total Price</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th class="text-right">Total</th>
                                            <th class="text-right">{{ number_format($totalInventory[0]->total, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Summary</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventory-table">
                                    <thead>
                                    <tr>
                                        <th>Total Capital: {{ number_format($bankAccounts->sum('balance') + $cash->amount + $mobileBanking->amount + $customerTotalPaid + $customerTotalDue + $totalInventory[0]->total - $supplierTotalDue, 2) }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(function () {
            $('.table').DataTable();
            $('.select2').select2()
        });
    </script>
@endsection
