<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>K8Supervisor</title>
    <!-- plugins:css -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.3/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ url('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ url('css/main/toastr.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('css/main/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/main/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ url('img/favicon.png') }}" />
</head>
<body class="sidebar-dark">
    <div class="container-scroller"> 
        <!-- TOPBAR -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row navbar-dark">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="icon-menu"></span>
                    </button>
                </div>
                <div>
                    <a class="navbar-brand brand-logo" href="{{ route ('Dashboard') }}">
                        <img src="{{url('img/logo.png')}}" style="height:28px" alt="logo"/>
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route ('Dashboard') }}">
                        <img src="{{url('img/favicon.png')}}" alt="logo"/>
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top"> 
                <ul class="navbar-nav">
                    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Hello, <span class="text-black fw-bold">{{isset(Auth::user()->name) ? Auth::user()->name : 'NONAME'}}</span></h1>
                        <h3 class="welcome-sub-text">{{session('clusterName') ? "Currently using: ".session('clusterName') : 'Currently not using any cluster' }} </h3>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>
        <!-- TOPBAR END -->
        <div class="container-fluid page-body-wrapper">
            <!-- SIDEBAR -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-category">Main pages</li>
                    <li class="nav-item {{ str_contains(Route::currentRouteName(),'Clusters.') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route ('Clusters.index') }}">
                            <i class="menu-icon mdi mdi-grid"></i>
                            <span class="menu-title">Clusters</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (Route::currentRouteName() == 'Users.editMe') ? 'active' : '' }}">
                        <a class="nav-link " href="{{route('Users.editMe')}}">
                            <i class="menu-icon mdi mdi-account-box"></i>
                            <span class="menu-title">My information</span>
                        </a>
                    </li>
                    @if (Auth::user()->role == "A")
                    <li class="nav-item {{ Route::currentRouteName() == 'Users.index' ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('Users.index')}}">
                            <i class="menu-icon mdi mdi-account-supervisor"></i>
                            <span class="menu-title">Users</span>
                        </a>
                    </li>
                    @endif
                    </li>
                    @if(session('clusterId'))
                    <li class="nav-item nav-category">Cluster Data & Resources</li>
                        <li class="nav-item {{ Route::currentRouteName() == 'Dashboard' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route ('Dashboard') }}">
                                <i class="menu-icon mdi mdi-view-dashboard"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::currentRouteName() == 'Nodes.index' ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Nodes.index")}}">
                                <i class="menu-icon mdi mdi-server"></i>
                                <span class="menu-title">Nodes</span>
                            </a>
                        </li>
                        <li class="nav-item {{ str_contains(Route::currentRouteName(),'Namespaces.') ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Namespaces.index")}}">
                                <i class="menu-icon mdi mdi-tournament"></i>
                                <span class="menu-title">Namespaces</span>
                            </a>
                        </li>
                        <li class="nav-item {{ str_contains(Route::currentRouteName(),'Pods.') ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Pods.index")}}">
                                <i class="menu-icon mdi mdi-apps"></i>
                                <span class="menu-title">Pods</span>
                            </a>
                        </li>
                        <li class="nav-item {{ str_contains(Route::currentRouteName(),'Deployments.') ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Deployments.index")}}">
                                <i class="menu-icon mdi mdi-apps-box"></i>
                                <span class="menu-title">Deployments</span>
                            </a>
                        </li>
                        <li class="nav-item {{ str_contains(Route::currentRouteName(),'Services.') ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Services.index")}}">
                                <i class="menu-icon mdi mdi-lan"></i>
                                <span class="menu-title">Services</span>
                            </a>
                        </li>
                        <li class="nav-item {{ str_contains(Route::currentRouteName(),'Ingresses.') ? 'active' : '' }}">
                            <a class="nav-link" href="{{route("Ingresses.index")}}">
                                <i class="menu-icon mdi mdi-sitemap"></i>
                                <span class="menu-title">Ingresses</span>
                            </a>
                        </li>
                    </li>
                    @endif
                    <li class="nav-item nav-category"></li>
                    <li class="nav-item">
                        <a class="nav-link" id="logout" href="#">
                            <i class="menu-icon mdi  mdi-logout"></i>
                            <span class="menu-title">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- SIDEBAR END -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="home-tab">
                            @yield('main-content')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END content-wrapper -->
                <!-- FOOTER -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">K8Supervisor - Simple K8S Control Panel</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2024. All rights reserved. IPL@EI</span>
                    </div>
                </footer>
                <!-- END FOOTER -->
            </div>
            <!-- END main-panel -->
        </div>
        <!-- END page-body-wrapper -->
    </div>

    <!-- plugins:js -->
    <script src="{{ url('vendors/js/vendor.bundle.base.js') }} "></script>
    <!-- inject:js -->
    <script src="{{ url('js/main/off-canvas.js') }} "></script>
    <script src="{{ url('js/main/hoverable-collapse.js') }}"></script>
    <script src="{{ url('js/main/template.js') }}"></script>
    <script src="{{ url('js/main/jquery.min.js') }}"></script>
    @if (str_contains(Route::currentRouteName(),'.index') || Route::currentRouteName() == 'Dashboard')
    <script src="{{ url('js/main/datatables.min.js') }}"></script>
    @endif
    <script src="{{ url('js/main/toastr.min.js') }}"></script>
    <script src="{{ url('js/main/sweetalert2@11.js') }}"></script>
    @if (Route::currentRouteName() == 'Dashboard')
    <script src="{{ url('js/main/chart.js') }}"></script>
    @endif

    @if (Route::currentRouteName() == 'Dashboard' || str_contains(Route::currentRouteName(),'.index'))
    <script>
        let table = new DataTable('#dt', {});
    </script>
    @endif

    @include('template/scripts/swal')

    @include('template/scripts/toastr')

    @if (Route::currentRouteName() == 'Dashboard')
        @include('template/scripts/charts')
    @endif

    @if (Route::currentRouteName() == 'Namespaces.create')
        @include('template/resource_creation/createNamespace')
    @endif

    @if (Route::currentRouteName() == 'Pods.create')
        @include('template/resource_creation/createPod')
    @endif

    @if (Route::currentRouteName() == 'Deployments.create')
        @include('template/resource_creation/createDeployment')
    @endif

    @if (Route::currentRouteName() == 'Services.create')
        @include('template/resource_creation/createService')
    @endif

    @if (Route::currentRouteName() == 'Ingresses.create')
        @include('template/resource_creation/createIngress')
    @endif

    @if (isset($json))
        @include('template/scripts/prettyJson')
    @endif
    <!-- endinject -->
</body>
</html>

