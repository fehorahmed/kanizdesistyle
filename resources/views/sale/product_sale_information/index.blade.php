@extends('layouts.app')

@section('title')
    Product Sale Information
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" name="serial" id="serial" placeholder="Serial No.">
                        </div>

                        <div class="col-md-2">
                            <a role="button" class="btn btn-primary" id="btnSearch">Search</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(function () {
            $('#btnSearch').click(function () {
                var serial = $('#serial').val();

                if (serial == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Enter serial no.',
                    });

                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "{{ route('sale_information.post') }}",
                    data: { serial: serial }
                }).done(function( response ) {
                    if (response.success) {
                        var win = window.open(response.redirect_url, '_blank');
                        if (win) {
                            //Browser has allowed it to be opened
                            win.focus();
                        } else {
                            //Browser has blocked it
                            alert('Please allow popups for this website');
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            });

            $('#serial').keypress(function (e) {
                if (e.keyCode == 13) {
                    $('#btnSearch').trigger('click');
                }
            });
        });
    </script>
@endsection
