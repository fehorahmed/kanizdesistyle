<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    @include('layouts.partial.__favicon')
    @include('layouts.partial.__styles')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @yield('style')
</head>
<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed {{ Route::currentRouteName() == 'pos' ? 'sidebar-collapse' : 'sidebar-mini' }} {{ auth()->user()->theme_mode == 1 ? ' ' : 'dark-mode' }}">
<div class="wrapper">
    

    <!-- Navbar -->
@include('layouts.partial.__navbar')
<!-- /.navbar -->
    <!-- Main Sidebar Container -->
@include('layouts.partial.__main_sidebar')
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper text-sm" style="position: relative;">
        <div id="preloader">
            <div id="loader"></div>
        </div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            @if(Route::currentRouteName() != 'pos')
                <div class="container-fluid">
                    <div class="row mb-2">
                        @if(Route::currentRouteName() == 'dashboard')
                            <div class="col-8 col-md-10">
                                <h1 class="m-0">@yield('title')</h1>
                            </div>

                            {{-- @if (auth()->user()->can('dashboard'))
                            <div class="col-4 col-md-2">
                                <form action="{{ route('dashboard') }}" id="dashboard_year_form" method="get">
                                    <div class="form-group mb-0">
                                        <select name="dashboard_year" class="form-control select2" id="dashboard_year">
                                            @for($i = 2023;$i <= date('Y');$i++)
                                                <option {{ request('dashboard_year',date('Y')) == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </form>
                            </div>
                            @endif--}}
                        @endif
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            @endif
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Main Footer -->
    @if(Route::currentRouteName() != 'pos')
        @include('layouts.partial.__footer')
    @endif
</div>
<!-- ./wrapper -->
@include('layouts.partial.__media_files')
<!-- REQUIRED SCRIPTS -->
@include('layouts.partial.__scripts')
@include('layouts.partial.__autocomplete')
@yield('script')
<!-- AdminLTE App -->
<script src="{{ asset('themes/backend/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
