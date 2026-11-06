@extends('layouts.app')
@section('title','User Permission')
@section('style')
    <style>
        ul{
            list-style: none;
        }
        label:not(.form-check-label):not(.custom-file-label) {
            font-weight: normal;
        }
        label.child-checkbox-label {
            font-size: 18px;
        }
        label.grandchild-checkbox-label {
            font-size: 16px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">User Permission Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('user.permission.update',['user'=>$user->id]) }}"
                      class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-left">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="check-all-checkbox" class="check-all-checkbox">
                                        <label style="font-size: 20px" for="check-all-checkbox">Check All</label>
                                    </div>

                                </td>
                            </tr>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td style="font-size: 20px;" class="text-left">
                                        <div class="icheck-success">
                                            <input {{ $user->can($permission->name) ? 'checked' : '' }} type="checkbox" id="for_label_{{ $permission->id }}" class="parent-checkbox" name="permission[]"
                                                   value="{{ $permission->name }}">
                                            <label class="parent-checkbox-label" for="for_label_{{ $permission->id }}"> {{ ucwords(str_replace('_',' ',$permission->name)) }}</label>
                                        </div>
                                        <ul>
                                            @foreach($permission->children->sortBy('sort') as $childrenItem)
                                                <li style="font-size: 19px;">
                                                    <div class="icheck-success">
                                                        <input {{ $user->can($childrenItem->name) ? 'checked' : '' }} type="checkbox" id="for_label_{{ $childrenItem->id }}" class="child-checkbox" name="permission[]"
                                                               value="{{ $childrenItem->name }}">
                                                        <label class="child-checkbox-label" for="for_label_{{ $childrenItem->id }}"> {{ ucwords(str_replace('_',' ',$childrenItem->name)) }}</label>
                                                    </div>
                                                    <ul>
                                                        @foreach($childrenItem->children->sortBy('sort') as $childrenItem2)
                                                            <li style="font-size: 18px;">
                                                                <div class="icheck-success">
                                                                    <input {{ $user->can($childrenItem2->name) ? 'checked' : '' }} type="checkbox" id="for_label_{{ $childrenItem2->id }}" class="grandchild-checkbox" name="permission[]"
                                                                           value="{{ $childrenItem2->name }}">
                                                                    <label class="grandchild-checkbox-label" for="for_label_{{ $childrenItem2->id }}"> {{ ucwords(str_replace('_',' ',$childrenItem2->name)) }}</label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary">Save</button>
                        <a href="{{ route('user.permission',['user'=>$user->id]) }}"
                           class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            function updateParentCheckboxes() {
                $('.parent-checkbox').each(function() {
                    var $this = $(this);
                    var $childCheckboxes = $this.closest('td').find('.child-checkbox, .grandchild-checkbox, .great-grandchild-checkbox');
                    var checkedChildCheckboxes = $childCheckboxes.filter(':checked');

                    if (checkedChildCheckboxes.length > 0) {
                        $this.prop('checked', true);
                    }
                });
            }

            $('.check-all-checkbox').change(function() {
                var isChecked = $(this).prop('checked');
                $('.parent-checkbox, .child-checkbox, .grandchild-checkbox, .great-grandchild-checkbox').prop('checked', isChecked);
                updateParentCheckboxes();
            });

            $('.child-checkbox, .grandchild-checkbox, .great-grandchild-checkbox').change(function() {
                updateParentCheckboxes();
            });

            $('.parent-checkbox').change(function() {
                $(this).closest('tr').find('.child-checkbox, .grandchild-checkbox, .great-grandchild-checkbox').prop('checked', this.checked);
            });

            $('.child-checkbox').change(function() {
                $(this).closest('li').find('.grandchild-checkbox, .great-grandchild-checkbox').prop('checked', this.checked);
            });

            updateParentCheckboxes();
        });
    </script>
@endsection
