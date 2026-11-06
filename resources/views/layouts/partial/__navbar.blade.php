<nav class="main-header navbar navbar-expand  {{ auth()->user()->theme_mode == 1 ? 'navbar-white' : 'navbar-dark' }}  navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <h3 class="nav-link font-weight-bold active"
                style="color: #007bff;font-size: 22px;margin: 0">{{ config('app.name') }}
            </h3>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item">
            <a class="nav-link bg-gradient-info text-white rounded" style="color: #fff !important;" href="/" target="_blank">
                <i class="fas fa-desktop"></i> Website
            </a>
        </li>
        &nbsp;
        <li class="nav-item">
            @can('sales_receipt_offline')
                <a class="nav-link bg-gradient-info text-white rounded" style="color: #fff !important;" href="{{ route('pos') }}">
                    <i class="fas fa-shopping-basket"></i> POS
                </a>
            @endcan
        </li>
        
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell"></i><sup class="badge badge-danger navbar-badge">0</sup>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                {{-- <a href="{{ route('out_of_stock.all') }}" class="dropdown-item">
                    <i class="fas fa-shopping-bag mr-2"></i> Out Of Stock<sup class="bg-warning badge">{{ $out_of_stock }}</sup>
                </a>
                <a href="{{ route('alert_quantity_stock.all') }}" class="dropdown-item">
                    <i class="fas fa-shopping-bag mr-2"></i> Limited Stock<sup class="bg-warning badge">{{ $limited_stock }}</sup>
                </a>
                <a class="dropdown-item" href="{{ route('near_to_expire_stock.all') }}">
                    <i class="fa-solid fa-circle-half-stroke mr-2"></i>Near to Expire<sup class="bg-warning badge">{{ $near_to_expire_stock }}</sup>
                </a> --}}
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <img height="30" class="img-circle" src="{{ asset(file_exists(auth()->user()->profile_photo) ? auth()->user()->profile_photo : 'themes/backend/dist/img/avatar.png') }}"
                     alt=""> {{ auth()->user()->name ?? '' }} <i class="fa fa-angle-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user-cog mr-2"></i> Profile
                </a>
                <a href="{{ route('profile.password_change') }}" class="dropdown-item">
                    <i class="fas fa-lock mr-2"></i> Change Password
                </a>
                <a class="dropdown-item" href="{{ route('dark_mode_change') }}">
                    <i class="fa-solid fa-circle-half-stroke mr-2"></i> {{ auth()->user()->theme_mode == 1 ? 'Dark Mode' : 'Light Mode' }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
