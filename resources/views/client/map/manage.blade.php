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
	<title>@yield('client_page_title')</title>
	<link href="{{asset('admin_asset/css/app.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">

                <a class="sidebar-brand" href="">
          <span class="align-middle">Parking System</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-item{{request()->routeIs()?'active':''}}">
                <a class="sidebar-link" href="{{route('record')}}">
      <i class="align-middle" data-feather="home"></i> <span class="align-middle">Home page</span>
    </a>
            </li>
				<ul class="sidebar-nav">
					<li class="sidebar-item{{request()->routeIs()?'active':''}}">
						<a class="sidebar-link" href="{{route('record')}}">
              <i class="align-middle" data-feather="user"></i> <span class="align-middle">Profile</span>
            </a>
					</li>
                    <ul class="sidebar-nav">
                        <li class="sidebar-item{{request()->routeIs()?'active':''}}">
                            <a class="sidebar-link" href="{{route('record')}}">
                  <i class="align-middle" data-feather="map"></i> <span class="align-middle">Map</span>
                </a>
                        </li>
                    <ul class="sidebar-nav">
                        <li class="sidebar-header">
                            Booking
                        </li>

                        <li class="sidebar-item{{request()->routeIs()?'active':''}}">
                            <a class="sidebar-link" href="">
                  <i class="align-middle" data-feather="truck"></i> <span class="align-middle">Car Parking</span>
                </a>
                        </li>
                        <li class="sidebar-item{{request()->routeIs()?'active':''}}">
                            <a class="sidebar-link" href="">
                  <i class="align-middle" data-feather="list"></i> <span class="align-middle">Booking History</span>
                </a>
                        </li>

				</div>
		</nav>

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
									<span class="indicator">2</span>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
								<div class="dropdown-menu-header">
									2 New Notifications
								</div>
								<div class="list-group">

									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-warning" data-feather="bell"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">car booked </div>
												<div class="text-muted small mt-1">payment made successfully</div>
												<div class="text-muted small mt-1">2m ago</div>
											</div>
										</div>
									</a>
									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-primary" data-feather="home"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">parked successfully</div>
												<div class="text-muted small mt-1">1m ago</div>
											</div>
										</div>
									</a>

								</div>
								<div class="dropdown-menu-footer">
									<a href="#" class="text-muted">Show all notifications</a>
								</div>
							</div>
						</li>

						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                <i class="align-middle" data-feather="settings"></i>
              </a>

							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                <img src="img/avatars/avatar.jpg" class="avatar img-fluid rounded me-1" alt="" /> <span class="text-dark">Paulo Dybala</span>
              </a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1" data-feather="user"></i> Profile</a>

								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.html"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
								<div class="dropdown-divider"></div>
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

					<div class="mb-3">
						<h1 class="h3 d-inline align-middle">Google Maps</h1>

					</div>

						<div class="col-12 col-lg-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Hybrid Map of Haramaya University</h5>
									<h6 class="card-subtitle text-muted">Displays the normal and satellite views of haramaya university.</h6>
								</div>
								<div class="card-body">
									<div class="content" id="hybrid_map" style="height: 300px; width: 100%;"></div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</main>

            <script src="{{asset('admin_asset/js/app.js')}}"></script>

            <script>
                function initMaps() {
                    var hybridMap = {
                        zoom: 14,
                        center: {
                            lat: 9.4230,
                            lng: 42.0373
                        },
                        mapTypeId: google.maps.MapTypeId.HYBRID
                    };
                    new google.maps.Map(document.getElementById("hybrid_map"), hybridMap);
                }
            </script>

            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-aWrwgr64q4b3TEZwQ0lkHI4lZK-moM4&callback=initMaps" async defer></script>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
							</p>
						</div>
											</div>
				</div>
			</footer>
		</div>
	</div>
		</div>
	</div>


</body>

</html>
