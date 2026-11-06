@extends('layouts.app')
@section('title','Banner')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-header">
                    <a href="{{ route('ecom_banner.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Banner
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $(document).on('click', '.zoom-gallery', function (e) {
                e.preventDefault();
                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    closeOnContentClick: false,
                    closeBtnInside: false,
                    mainClass: 'mfp-with-zoom mfp-img-mobile',
                    allowHTMLInTemplate: true,
                    image: {
                        verticalFit: true,
                        titleSrc: function(item) {
                            return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">image source</a>';
                        }
                    },
                    gallery: {
                        enabled: true
                    },
                    zoom: {
                        enabled: true,
                        duration: 300, // don't foget to change the duration also in CSS
                        opener: function(element) {
                            return element.find('img');
                        }
                    }
                }).magnificPopup('open');

            });

            
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('ecom_banner.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'image', name: 'image', searchable: false},
                    {data: 'type', name: 'type', searchable: true},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "columnDefs": [
                    {"className": "text-left", "targets": "5"}
                ],
                "responsive": false, "autoWidth": false,
            });

        })
    </script>
@endsection
