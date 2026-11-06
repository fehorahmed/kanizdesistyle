@extends('layouts.app')
@section('title','Cash Information')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class=" row {{ $errors->has('excel_file') ? 'has-error' :'' }}">
                        <label  class="col-sm-2">Cash Information</label>
                        <div class="col-sm-10 text-right">
                            @if($cash->opening_balance <= 0)
                            <a class="btn btn-danger btn-sm btn-add" role="button" data-type="opening" ><i class="fa-solid fa-money-check-dollar"></i> Add Opening</a>
                            @endif
                            <a class="btn btn-success btn-sm btn-add" role="button" data-type="investment"><i class="fa-solid fa-money-check-dollar"></i> Add Investment</a>
                            <a class="btn btn-primary btn-sm btn-add" role="button" data-type="withdraw"><i class="fa-solid fa-money-check-dollar"></i> Withdraw</a>
                        </div>
                    </div>
                </div>
                <hr style="margin-top: -10px !important;margin-bottom: -10px !important">            <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Hand Cash</td>
                                    <td>৳{{number_format($cash->amount,2) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Opening Balance</td>
                                    <td>৳{{number_format($cash->opening_balance,2) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Investment Amount</td>
                                    <td>৳{{number_format($cash->investment_amt,2) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Withdraw Amount</td>
                                    <td>৳{{number_format($cash->withdraw_amt,2) ?? 0}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center"><h4>Invenstment Information</h4></th>
                                <th class="text-center"><h4>Withdraw Information</h4></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 50%;">
                                        <table id="table2" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="text-center">S/L</th>
                                                <th class="text-center">User</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Note</th>
                                                <th class="text-center">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($cashLogs as $cashLog)
                                                @if($cashLog->type==2)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">{{ $cashLog->user->name ?? '' }}</td>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($cashLog->date)->format('d-m-Y') }}</td>
                                                        <td class="text-center">{{ $cashLog->note }}</td>
                                                        <td class="text-center">{{ number_format($cashLog->amount, 2) }}</td>
                                                    </tr>
                                                    @endif
                                                @empty
                                                  <tr>
                                                    <td>No Data found!</td>
                                                  </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width: 50%;">
                                        <table id="table3" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="text-center">S/L</th>
                                                <th class="text-center">User</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Note</th>
                                                <th class="text-center">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($cashLogs as $cashLog)
                                                @if($cashLog->type==3)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="text-center">{{ $cashLog->user->name ?? '' }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($cashLog->date)->format('d-m-Y') }}</td>
                                                    <td class="text-center">{{ $cashLog->note }}</td>
                                                    <td class="text-center">{{ number_format($cashLog->amount, 2) }}</td>
                                                </tr>
                                                @endif
                                            @empty
                                              <tr>
                                                <td>No Data found!</td>
                                              </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Amount/Withdraw Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <input type="hidden" id="cash_type" name="type">
                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" id="amount" placeholder="Enter Amount">
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-save">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            $('#table').DataTable();

            $('body').on('click', '.btn-add', function () {
                var type = $(this).data('type');
                $('#cash_type').val(type);
                if(type === 'opening'){
                    $('.modal-title').text('Add Opening Blance');
                }else if(type == 'investment'){
                    $('.modal-title').text('Add Investment Amount');
                }else if(type == 'withdraw'){
                    $('.modal-title').text('Withdraw Amount');
                }
                $('#modal-pay').modal('show');
            });

            $('#modal-btn-save').click(function () {
                var formData = new FormData($('#modal-form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('cash_in_out') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Save!',
                                response.message,
                                'success'
                            ).then((result) => {
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        })
    </script>
@endsection
