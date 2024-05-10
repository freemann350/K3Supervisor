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
            <img src="{{url('img/ksl.png')}}" alt="logo"/>
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
          <li class="nav-item nav-category">My pages</li>
            <li class="nav-item {{ Route::currentRouteName() == 'Dashboard' ? 'active' : '' }}">
              <a class="nav-link" href="{{ route ('Dashboard') }}">
                <i class="menu-icon mdi mdi-grid"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ (Route::currentRouteName() == 'User.editMe' && Auth::user()->id == request()->route('User')) ? 'active' : '' }}" href="{{route('User.editMe')}}">
                <i class="menu-icon mdi mdi-account-box"></i>
                <span class="menu-title">My information</span>
              </a>
            </li>
            @if (Auth::user()->role == "A")
            <li class="nav-item">
              <a class="nav-link {{ Route::currentRouteName() == 'Users.index' ? 'active' : '' }}" href="{{route('Users.index')}}">
                <i class="menu-icon mdi mdi-account-supervisor"></i>
                <span class="menu-title">Users</span>
              </a>
            </li>
            @endif
          </li>
          <li class="nav-item nav-category">Resources</li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-server"></i>
                <span class="menu-title">Nodes</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-tournament"></i>
                <span class="menu-title">Namespaces</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-apps"></i>
                <span class="menu-title">Pods</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-apps-box"></i>
                <span class="menu-title">Deployments</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-lan"></i>
                <span class="menu-title">Services</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-sitemap"></i>
                <span class="menu-title">Ingresses</span>
              </a>
            </li>
          </li>
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
  <script src="https://cdn.datatables.net/v/bs5/dt-2.0.3/datatables.min.js"></script>
  <script src="{{ url('js/main/jquery.min.js') }}"></script>
  <script src="{{ url('js/main/toastr.min.js') }}"></script>
  <script src="{{ url('js/main/sweetalert2@11.js') }}"></script>
  <script>
    let table = new DataTable('#dt');
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Get the link element
      var logout = document.getElementById("logout");

      // Add click event listener
      logout.addEventListener("click", function(event) {
          // Prevent default link behavior
          event.preventDefault();

          // Execute SweetAlert code
          Swal.fire({
              title: "Are you sure you want to leave?",
              text: "There's more networking to be done",
              icon: "question",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              cancelButtonText: "No",
              confirmButtonText: "Yes"
          }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = '{{ route('Auth.logout') }}';
              }
          });
      });
    });

    function _delete(msg, route) {
        Swal.fire({
            title: "Delete",
            text: msg,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "No",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
              // Create a form element
              const form = document.createElement('form');
              form.method = 'POST';
              form.action = route;
              form.innerHTML = `
                  @csrf
                  @method('DELETE')
              `;
              // Append the form to the document body and submit it
              document.body.appendChild(form);
              form.submit();
            }
        });
    };

    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    @if (session('error-msg'))
      toastr.error('{{session('error-msg')['message']}} ({{session('error-msg')['error']}})<br>{{session('error-msg')['detail']}}', 'A problem has occurred')
    @endif

    @if (session('success-msg'))
      toastr.success('{{session('success-msg')}}','Success!')
    @endif
  <!-- endinject -->
  </script>
</body>

</html>

