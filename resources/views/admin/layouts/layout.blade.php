<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />
	<title>@yield('admin_page_title')</title>
	<link href="{{asset('admin_asset/css/app.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="index.html">
          <span class="align-middle">Admin</span>
        </a>
				<ul class="sidebar-nav">
					<li class="sidebar-item{{request()->routeIs()?'active':''}}">
						<a class="sidebar-link" href="{{route('dashboard')}}">
              <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
            </a>
					</li>
                    <ul class="sidebar-nav">
                        <li class="sidebar-item{{request()->routeIs('admin.history.manage')?'active':''}}">
                            <a class="sidebar-link" href="{{route('admin.history.manage')}}">
                  <i class="align-middle" data-feather="compass"></i> <span class="align-middle">Manage Parking History</span>
                </a>
                <ul class="sidebar-nav">
					<li class="sidebar-item{{request()->routeIs('admin.payment.manage')?'active':''}}">
						<a class="sidebar-link" href="{{route('admin.payment.manage')}}">
              <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Manage payments</span>
            </a>
					</li>
                        </li>
                        <ul class="sidebar-nav">
                            <li class="sidebar-header">
                                Users
                            </li>


                            <li class="sidebar-item {{ request()->routeIs('admin.user.manage') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ route('admin.user.manage') }}">
                                    <i class="align-middle" data-feather="list"></i> <span class="align-middle">Manage</span>
                                </a>
                            </li>
                        </ul>
                            </li>




				</div>
		</nav>
@yield('admin.layout')
		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
          <i class="hamburger align-self-center"></i>
        </a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
								<div class="position-relative">
									<i class="align-middle" data-feather="bell"></i>
									<span class="indicator">0</span>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
								<div class="dropdown-menu-header">
									1 New Notifications
								</div>
								<div class="list-group">
									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-danger" data-feather="alert-circle"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">Update completed</div>
												<div class="text-muted small mt-1">Restart server</div>
												<div class="text-muted small mt-1">30m ago</div>
											</div>
										</div>
									</a>

									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-primary" data-feather="home"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">Login from 192.186.1.8</div>
												<div class="text-muted small mt-1">5h ago</div>
											</div>
										</div>
									</a>

								</div>
								<div class="dropdown-menu-footer">
									<a href="#" class="text-muted">Show all notifications</a>
								</div>
							</div>
						</li>
						<li class="nav-item">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger" style="margin-top: 8px;">
            <i class="align-middle me-1" data-feather="log-out"></i> Logout
        </button>
    </form>
</li>

						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                <i class="align-middle" data-feather="settings"></i>
              </a>

						<li class="nav-item dropdown">

              </a>

							<div class="dropdown-menu dropdown-menu-end">


								<form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn text-white" style="background-color: #1e1e3d;">Logout</button>
                                </form>

							</div>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content">
				<div class="container-fluid p-0">
               @yield('admin_layout')
				</div>
			</main>


		</div>
	</div>

	<script src="{{asset('admin_asset/js/app.js')}}"></script>

</body>

</html>

