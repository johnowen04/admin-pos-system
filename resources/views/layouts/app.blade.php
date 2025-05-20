<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Kaiadmin - Bootstrap 5 Admin Dashboard')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
</head>

<body>
    @guest
        <div class="container d-flex justify-content-center align-items-center flex-column min-vh-100">
            @yield('content')
        </div>
    @else
        @if (Route::is('pos.receipt'))
            <div class="container d-flex justify-content-center align-items-center flex-column min-vh-100">
                @yield('content')
            </div>
        @else
            <div class="wrapper {{ Route::is('pos.*') ? 'sidebar_minimize' : '' }}">
                <!-- Sidebar -->
                <div class="sidebar sidebar-style-2" data-background-color="dark">
                    <x-sidebar-logo logo="assets/img/kaiadmin/logo_light.svg" backgroundColor="dark" />

                    {{-- Move this code to role access controller later --}}
                    <?php $menuItems = [
                        [
                            'name' => 'Dashboard',
                            'link' => 'dashboard',
                            'icon' => 'fas fa-home',
                            'route' => 'dashboard',
                            'active' => request()->is('/'),
                        ],
                        [
                            'name' => 'Role & Permission',
                            'link' => 'role',
                            'icon' => 'fas fa-user-shield',
                            'route' => 'role.index',
                            'active' => request()->is('role*') || request()->is('feature*') || request()->is('operation*') || request()->is('permission*') || request()->is('position*') || request()->is('acl*'),
                            'children' => [
                                [
                                    'name' => 'Role',
                                    'route' => 'role.index',
                                    'active' => request()->is('role'),
                                ],
                                [
                                    'name' => 'Feature',
                                    'route' => 'feature.index',
                                    'active' => request()->is('feature'),
                                ],
                                [
                                    'name' => 'Operation',
                                    'route' => 'operation.index',
                                    'active' => request()->is('operation'),
                                ],
                                [
                                    'name' => 'Permission',
                                    'route' => 'permission.index',
                                    'active' => request()->is('permission'),
                                ],
                                [
                                    'name' => 'Position',
                                    'route' => 'position.index',
                                    'active' => request()->is('position'),
                                ],
                                [
                                    'name' => 'ACL',
                                    'route' => 'acl.index',
                                    'active' => request()->is('acl'),
                                ],
                            ],
                        ],
                        [
                            'name' => 'Employee',
                            'link' => 'employee',
                            'icon' => 'fas fa-user',
                            'route' => 'employee.index',
                            'active' => request()->is('employee*'),
                        ],
                        [
                            'name' => 'Outlet',
                            'link' => 'outlet',
                            'icon' => 'fas fa-building',
                            'route' => 'outlet.index',
                            'active' => request()->is('outlet*'),
                        ],
                        [
                            'name' => 'Unit',
                            'link' => 'unit',
                            'icon' => 'fas fa-ruler-horizontal',
                            'route' => 'unit.index',
                            'active' => request()->is('unit*') || request()->is('baseunit*'),
                            'children' => [
                                [
                                    'name' => 'Base Unit',
                                    'route' => 'baseunit.index',
                                    'active' => request()->is('baseunit'),
                                ],
                                [
                                    'name' => 'Unit',
                                    'route' => 'unit.index',
                                    'active' => request()->is('unit'),
                                ],
                            ],
                        ],
                        [
                            'name' => 'Product',
                            'link' => 'product',
                            'icon' => 'fas fa-box-open',
                            'route' => 'product.index',
                            'active' => request()->is('product*') || request()->is('category*') || request()->is('department*'),
                            'children' => [
                                [
                                    'name' => 'Department',
                                    'route' => 'department.index',
                                    'active' => request()->is('department'),
                                ],
                                [
                                    'name' => 'Category',
                                    'route' => 'category.index',
                                    'active' => request()->is('category'),
                                ],
                                [
                                    'name' => 'Product',
                                    'route' => 'product.index',
                                    'active' => request()->is('product'),
                                ],
                            ],
                        ],
                        [
                            'name' => 'Inventory',
                            'link' => 'inventory',
                            'icon' => 'fas fa-boxes',
                            'route' => 'inventory.index',
                            'active' => request()->is('inventory*'),
                        ],
                        [
                            'name' => 'Purchase',
                            'link' => 'purchase',
                            'icon' => 'fas fa-shopping-basket',
                            'route' => 'purchase.index',
                            'active' => request()->is('purchase*'),
                        ],
                        [
                            'name' => 'Sales',
                            'link' => 'sales',
                            'icon' => 'fas fa-tags',
                            'route' => 'sales.index',
                            'active' => request()->is('sales*'),
                        ],
                        [
                            'name' => 'POS',
                            'link' => 'pos',
                            'route' => 'pos.index',
                            'icon' => 'fas fa-shopping-cart',
                            'active' => request()->is('pointofsale*'), // redo later
                        ],
                    ]; ?>

                    <x-sidebar :menuItems="$menuItems" />
                </div>
                <!-- End Sidebar -->

                <div class="main-panel">
                    <div class="main-header">
                        <div class="main-header-logo">
                            <!-- Logo Header -->
                            <x-sidebar-logo logo="assets/img/kaiadmin/logo_light.svg" backgroundColor="dark" />
                            <!-- End Logo Header -->
                        </div>
                        <!-- Navbar Header -->
                        <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                            <div class="container-fluid">

                                @if (Route::is('pos.*') && Auth::user()->employee->outlets->isNotEmpty())
                                    <!-- Additional Feature: Add dropdown outlets for user that has more than 1 outlet assigned -->
                                    <!-- Active Outlet Section -->
                                    <span class="profile-outlet">
                                        <span class="fw-bold">Active Outlet:
                                            {{ Auth::user()->employee->outlets[0]->name }}</span>
                                    </span>
                                @endif

                                <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                                    <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                                            role="button" aria-expanded="false" aria-haspopup="true">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-search animated fadeIn">
                                            <form class="navbar-left navbar-form nav-search">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Search ..." class="form-control" />
                                                </div>
                                            </form>
                                        </ul>
                                    </li>

                                    <li class="nav-item topbar-user dropdown hidden-caret">
                                        <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                            aria-expanded="false">
                                            <div class="avatar-sm">
                                                <img src="{{ asset('assets/img/profile.jpg') }}" alt="..."
                                                    class="avatar-img rounded-circle" />
                                            </div>
                                            <span class="profile-username">
                                                <span class="op-7">Hi,</span>
                                                <span class="fw-bold">{{ Auth::user()->name }}</span>
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-user animated fadeIn">
                                            <div class="dropdown-user-scroll scrollbar-outer">
                                                <li>
                                                    <div class="user-box">
                                                        <div class="avatar-lg">
                                                            <img src="{{ asset('assets/img/profile.jpg') }}"
                                                                alt="image profile" class="avatar-img rounded" />
                                                        </div>
                                                        <div class="u-text">
                                                            <h4>{{ Auth::user()->name }}</h4>
                                                            <p class="text-muted">{{ Auth::user()->email }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="dropdown-divider"></div>
                                                    {{-- Logout link --}}
                                                    <a class="dropdown-item" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        Logout
                                                    </a>

                                                    {{-- Hidden form --}}
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        style="display: none;">
                                                        @csrf
                                                    </form>
                                                </li>
                                            </div>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                        <!-- End Navbar -->
                    </div>

                    <div class="container">
                        @yield('content')
                    </div>

                    <footer class="footer">
                        <div class="container-fluid d-flex justify-content-between">
                            <nav class="pull-left">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="http://www.themekita.com">
                                            ThemeKita
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#"> Help </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#"> Licenses </a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="copyright">
                                2024, made with <i class="fa fa-heart heart text-danger"></i> by
                                <a href="http://www.themekita.com">ThemeKita</a>
                            </div>
                            <div>
                                Distributed by
                                <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
                            </div>
                        </div>
                    </footer>
                </div>

                <!-- Custom template | don't include it in your project! -->
                <div class="custom-template">
                    <div class="title">Settings</div>
                    <div class="custom-content">
                        <div class="switcher">
                            <div class="switch-block">
                                <h4>Logo Header</h4>
                                <div class="btnSwitch">
                                    <button type="button" class="selected changeLogoHeaderColor"
                                        data-color="dark"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="blue"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                                    <button type="button" class="changeLogoHeaderColor"
                                        data-color="light-blue"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="green"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="orange"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="red"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="white"></button>
                                    <br />
                                    <button type="button" class="changeLogoHeaderColor" data-color="dark2"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="blue2"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="purple2"></button>
                                    <button type="button" class="changeLogoHeaderColor"
                                        data-color="light-blue2"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="green2"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="orange2"></button>
                                    <button type="button" class="changeLogoHeaderColor" data-color="red2"></button>
                                </div>
                            </div>
                            <div class="switch-block">
                                <h4>Navbar Header</h4>
                                <div class="btnSwitch">
                                    <button type="button" class="changeTopBarColor" data-color="dark"></button>
                                    <button type="button" class="changeTopBarColor" data-color="blue"></button>
                                    <button type="button" class="changeTopBarColor" data-color="purple"></button>
                                    <button type="button" class="changeTopBarColor" data-color="light-blue"></button>
                                    <button type="button" class="changeTopBarColor" data-color="green"></button>
                                    <button type="button" class="changeTopBarColor" data-color="orange"></button>
                                    <button type="button" class="changeTopBarColor" data-color="red"></button>
                                    <button type="button" class="selected changeTopBarColor"
                                        data-color="white"></button>
                                    <br />
                                    <button type="button" class="changeTopBarColor" data-color="dark2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="blue2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="purple2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="light-blue2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="green2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="orange2"></button>
                                    <button type="button" class="changeTopBarColor" data-color="red2"></button>
                                </div>
                            </div>
                            <div class="switch-block">
                                <h4>Sidebar</h4>
                                <div class="btnSwitch">
                                    <button type="button" class="changeSideBarColor" data-color="white"></button>
                                    <button type="button" class="selected changeSideBarColor"
                                        data-color="dark"></button>
                                    <button type="button" class="changeSideBarColor" data-color="dark2"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-toggle">
                        <i class="icon-settings"></i>
                    </div>
                </div>
                <!-- End Custom template -->
            </div>
        @endif
    @endguest

    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>

    <script>
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            Í
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    </script>

    @stack('scripts')
</body>

</html>
