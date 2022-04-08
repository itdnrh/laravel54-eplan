		<header class="main-header">
			<!-- Logo -->
			<a href="{{ url('/home') }}" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>L</b>MS</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>E-Plan</b> MS</span>
			</a>

			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top">

				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>

				<!-- Navbar Right Menu -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">

						<!-- Messages: style can be found in dropdown.less-->
						@include('layouts._messages')
						<!-- End messages menu-->
						
						<!-- Notifications: style can be found in dropdown.less -->
						@include('layouts._notifications')
						<!-- End notifications menu -->

						<!-- Tasks: style can be found in dropdown.less -->
						<!-- <li class="dropdown tasks-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-flag-o"></i>
								<span class="label label-danger">9</span>
							</a>
							<ul class="dropdown-menu">
								<li class="header">You have 9 tasks</li>
								<li>
									<ul class="menu">
										<li>
											<a href="#">
												<h3>
													Design some buttons
													<small class="pull-right">20%</small>
												</h3>
												<div class="progress xs">
													<div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
													aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
														<span class="sr-only">20% Complete</span>
													</div>
												</div>
											</a>
										</li>
										<li>
											<a href="#">
												<h3>
													Create a nice theme
													<small class="pull-right">40%</small>
												</h3>
												<div class="progress xs">
													<div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar"
													aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
														<span class="sr-only">40% Complete</span>
													</div>
												</div>
											</a>
										</li>
										<li>
											<a href="#">
												<h3>
													Some task I need to do
													<small class="pull-right">60%</small>
												</h3>
												<div class="progress xs">
													<div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar"
													aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
														<span class="sr-only">60% Complete</span>
													</div>
												</div>
											</a>
										</li>
										<li>
											<a href="#">
												<h3>
													Make beautiful transitions
													<small class="pull-right">80%</small>
												</h3>
												<div class="progress xs">
													<div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar"
													aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
														<span class="sr-only">80% Complete</span>
													</div>
												</div>
											</a>
										</li>
									</ul>
								</li>
								<li class="footer">
									<a href="#">View all tasks</a>
								</li>
							</ul>
						</li> --><!-- End tasks menu -->

						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">

							@if (Auth::guest())

							@else

								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<?php $userAvatarUrl = (Auth::user()->person_photo != '') ? "http://192.168.20.4:3839/ps/PhotoPersonal/" .Auth::user()->person_photo : asset('img/user2-160x160.jpg'); ?>
									<img
										src="{{ $userAvatarUrl }}"
										class="user-image"
										alt="User Image"
									/>
									<span class="hidden-xs">{{ Auth::user()->person_firstname }} {{ Auth::user()->person_lastname }}</span>
								</a>
								<ul class="dropdown-menu">
									<!-- User image -->
									<li class="user-header">
										<img src="{{ $userAvatarUrl }}" class="img-circle" alt="User Image">

										<p>
											{{ Auth::user()->person_firstname }} {{ Auth::user()->person_lastname }}
											<small>
												<span>{{ Auth::user()->typeposition_id == 1 ? 'วันที่บรรจุ' : 'วันที่เริ่มงาน' }}</span> 
												{{ convDbDateToThDate(Auth::user()->person_singin) }}
											</small>
										</p>
									</li>
									
									<!-- Menu Body -->
									<!-- <li class="user-body">
										<div class="row">
											<div class="col-xs-4 text-center">
												<a href="#">Followers</a>
											</div>
											<div class="col-xs-4 text-center">
												<a href="#">Sales</a>
											</div>
											<div class="col-xs-4 text-center">
												<a href="#">Friends</a>
											</div>
										</div>
									</li> -->
									
									<!-- Menu Footer-->
									<li class="user-footer">
										<div class="pull-left">
											<a 	href="#"
												class="btn btn-default btn-flat"
												ng-click="redirectTo($event, 'histories/profile/' + {{ Auth::user()->person_id }})"
											>
												ข้อมูลส่วนตัว
											</a>
										</div>
										<div class="pull-right">
											<a 	href="{{ route('logout') }}" 
												onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
												class="btn btn-default btn-flat">
												ลงชื่อออก
											</a>

											<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
												{{ csrf_field() }}
											</form>
										</div>
									</li>
								</ul>

							@endif

						</li><!-- End user account menu -->

						<!-- Control Sidebar Toggle Button -->
						<!-- <li>
							<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
						</li> -->
						<!-- Control Sidebar Toggle Button -->

					</ul><!-- /.nav navbar-nav -->
				</div>

			</nav>
		</header>