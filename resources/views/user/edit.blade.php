@extends('layouts.app')
@section('title','User Edit')
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
                    <h3 class="card-title">User Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="{{ route('user.update',['user'=>$user->id]) }}"
                      class="form-horizontal" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('role_id') ? 'has-error' :'' }}">
                            <label for="role_id" class="col-sm-2 col-form-label">Role <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="role_id" id="role_id" class="form-control select2">
                                    <option value="">Select Role</option>
                                    <option {{ old('role_id',$user->role_id) ==1  ? 'selected' : ''}} value="1">Super Admin</option>
                                    <option {{ old('role_id',$user->role_id) ==2  ? 'selected' : ''}} value="2">Admin</option>
                                    <option {{ old('role_id',$user->role_id) ==3  ? 'selected' : ''}} value="3">Employee</option>
                                </select>
                                @error('role_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label for="name" class="col-sm-2 col-form-label">Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('name',$user->name) }}" name="name"
                                       class="form-control" id="name" placeholder="Enter Name">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('username') ? 'has-error' :'' }}">
                            <label for="username" class="col-sm-2 col-form-label">Username <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('username',$user->username) }}" name="username"
                                       class="form-control" id="username" placeholder="Enter Username">
                                @error('username')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" value="{{ old('email',$user->email) }}" name="email"
                                       class="form-control" id="email" placeholder="Enter Email">
                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label for="mobile_no" class="col-sm-2 col-form-label">Mobile No.</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('mobile_no',$user->mobile_no) }}" name="mobile_no"
                                       class="form-control" id="mobile_no" placeholder="Enter Mobile No.">
                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label for="password" class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" autocomplete="new-password" name="password" class="form-control"
                                       id="password" placeholder="Enter Password">
                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('password_confirmation') ? 'has-error' :'' }}">
                            <label for="password_confirmation" class="col-sm-2 col-form-label">Password
                                Confirmation</label>
                            <div class="col-sm-10">
                                <input type="password" name="password_confirmation" class="form-control"
                                       id="password_confirmation" placeholder="Enter Password Confirmation">
                                @error('password_confirmation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>

                            <div class="col-sm-10">

                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="active" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($user->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($user->status == '0' ? 'checked' : '')) :
                                            (old('status') == '0' ? 'checked' : '') }}>
                                    <label for="inactive">
                                        Inactive
                                    </label>
                                </div>

                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <table class="table table-bordered">
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
                                            @foreach($permission->children as $childrenItem)
                                                <li style="font-size: 19px;">
                                                    <div class="icheck-success">
                                                        <input {{ $user->can($childrenItem->name) ? 'checked' : '' }} type="checkbox" id="for_label_{{ $childrenItem->id }}" class="child-checkbox" name="permission[]"
                                                               value="{{ $childrenItem->name }}">
                                                        <label class="child-checkbox-label" for="for_label_{{ $childrenItem->id }}"> {{ ucwords(str_replace('_',' ',$childrenItem->name)) }}</label>
                                                    </div>
                                                    <ul>
                                                        @foreach($childrenItem->children as $childrenItem2)
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
                        </table> --}}
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary">Save</button>
                        <a href="{{ route('user.index') }}"
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
                var $this = $(this);
                $this.siblings('ul').find('.child-checkbox, .grandchild-checkbox, .great-grandchild-checkbox').prop('checked', this.checked);
            });

            updateParentCheckboxes();
        });
    </script>
@endsection
