		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
						<?php $userAvatarUrl = (Auth::user()->person_photo != '') ? "http://192.168.20.4:3839/ps/PhotoPersonal/" .Auth::user()->person_photo : asset('img/user2-160x160.jpg'); ?>
						<img
							src="{{ $userAvatarUrl }}"
							class="img-circle"
							alt="User Image"
						/>
					</div>
					<div class="pull-left info">
						<p>

							@if (!Auth::guest())
								{{ Auth::user()->person_firstname }} {{ Auth::user()->person_lastname }}
							@endif

						</p>
						<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
					</div>
				</div>
				<!-- search form -->
				<form action="#" method="get" class="sidebar-form">
					<div class="input-group">
						<input type="text" name="q" class="form-control" placeholder="Search...">
						<span class="input-group-btn">
							<button type="submit" name="search" id="search-btn" class="btn btn-flat">
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</form>
				<!-- /.search form -->
				<!-- sidebar menu: style can be found in sidebar.less -->
				<ul class="sidebar-menu" data-widget="tree">
					<li class="header">MAIN NAVIGATION</li>

					<li ng-class="{ 'active': menu == 'home' }">
						<a href="{{ url('/home') }}">
							<i class="fa fa-dashboard"></i> <span>Dashboard</span>
						</a>
					</li>

					<li class="treeview" ng-class="{ 'menu-open active': ['plans','projects'].includes(menu) }">
						<a href="#">
							<i class="fa fa-calendar"></i>
							<span>คำขอประจำปี</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu" ng-style="{ 'display': ['plans'].includes(menu) ? 'block' : 'none' }">
							<li ng-class="{ 'active': ['assets','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/assets') }}">
									<i class="fa fa-circle-o"></i> ครุภัณฑ์
								</a>
							</li>
							<li ng-class="{ 'active': ['materials','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/materials') }}">
									<i class="fa fa-circle-o"></i> วัสดุ (นอกคลัง)
								</a>
							</li>
							<li ng-class="{ 'active': ['services','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/services') }}">
									<i class="fa fa-circle-o"></i> จ้างบริการ
								</a>
							</li>
							<li ng-class="{ 'active': ['constructs','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/constructs') }}">
									<i class="fa fa-circle-o"></i> ก่อสร้าง
								</a>
							</li>
							<li ng-class="{ 'active': ['list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/projects/list') }}">
									<i class="fa fa-circle-o"></i> โครงการ
								</a>
							</li>
						</ul>
					</li>

					<!-- <li class="treeview" ng-class="{ 'menu-open active': ['utilities'].includes(menu) }">
						<a href="#">
							<i class="fa fa-bolt"></i>
							<span>ค่าสาธารณูปโภค</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu" ng-style="{ 'display': ['utilities'].includes(menu) ? 'block' : 'none' }">
							<li ng-class="{ 'active': ['assets','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/utilities/electricity') }}">
									<i class="fa fa-circle-o"></i> ค่าไฟฟ้า
								</a>
							</li>
							<li ng-class="{ 'active': ['assets','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/utilities/plumbing') }}">
									<i class="fa fa-circle-o"></i> ค่าน้ำประปา
								</a>
							</li>
							<li ng-class="{ 'active': ['assets','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/utilities/telephone') }}">
									<i class="fa fa-circle-o"></i> ค่าโทรศัพท์
								</a>
							</li>
						</ul>
					</li> -->
					<!-- // Authorize เฉพาะหัวหน้ากลุ่มภารกิจ/ธุรการหรือเลขาฯกลุ่มภารกิจ/หัวหน้ากลุ่มงาน -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->person_id == '1309900322504' ||
						Auth::user()->memberOf->duty_id == 1 ||
						Auth::user()->memberOf->duty_id == 2 ||
						count(Auth::user()->delegations) > 0
					)
						<!-- <li class="treeview" ng-class="{ 'menu-open active': ['approvals'].includes(menu) }">
							<a href="#">
								<i class="fa fa-server"></i>
								<span>รายการย่อย</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu" ng-style="{ 'display': ['approvals'].includes(menu) ? 'block' : 'none' }">
								<li ng-class="{ 'active': ['reparations','list','add','edit','detail'].includes(submenu)}">
									<a href="{{ url('/plans/reparations') }}">
										<i class="fa fa-circle-o"></i> จ้างซ่อมบำรุง
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'receive' }">
									<a href="{{ url('materials/list') }}">
										<i class="fa fa-circle-o"></i> วัสดุ (ในคลัง)
									</a>
								</li>
							</ul>
						</li> -->
					@endif

					<!-- // Authorize เฉพาะหัวหน้ากลุ่มภารกิจ/ธุรการหรือเลขาฯกลุ่มภารกิจ/หัวหน้ากลุ่มงาน -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 4 ||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': ['approvals'].includes(menu) }">
							<a href="#">
								<i class="fa fa-check-square-o"></i>
								<span>การอนุมัติ</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu" ng-style="{ 'display': ['approvals'].includes(menu) ? 'block' : 'none' }">
								<li ng-class="{ 'active': submenu == 'assets' }">
									<a href="{{ url('approvals/assets') }}">
										<i class="fa fa-circle-o"></i> ครุภัณฑ์
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'materials' }">
									<a href="{{ url('approvals/materials') }}">
										<i class="fa fa-circle-o"></i> วัสดุ (นอกคลัง)
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'services' }">
									<a href="{{ url('approvals/services') }}">
										<i class="fa fa-circle-o"></i> จ้างบริการ
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'constructs' }">
									<a href="{{ url('approvals/constructs') }}">
										<i class="fa fa-circle-o"></i> ก่อสร้าง
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'projects' }">
									<a href="{{ url('approvals/projects') }}">
										<i class="fa fa-circle-o"></i> โครงการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<li class="treeview" ng-class="{ 'menu-open active': menu == 'supports' }">
						<a href="#">
							<i class="fa fa-handshake-o"></i>
							<span>ขอสนับสนุน</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li ng-class="{ 'active': submenu == 'list' }">
								<a href="{{ url('supports/list') }}">
									<i class="fa fa-circle-o"></i> บันทึกขอสนับสนุน
								</a>
							</li>
							<li ng-class="{ 'active': submenu == 'timeline' }">
								<a href="{{ url('supports/timeline') }}">
									<i class="fa fa-circle-o"></i> ติดตามพัสดุ
								</a>
							</li>
						</ul>
					</li>

					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 2 ||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': menu == 'orders' }">
							<a href="#">
								<i class="fa fa-laptop"></i>
								<span>จัดซื้อจัดจ้าง</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'received' }">
									<a href="{{ url('orders/received') }}">
										<i class="fa fa-circle-o"></i> รับใบขอสนับสนุน
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('orders/list') }}">
										<i class="fa fa-circle-o"></i> ใบสั่งซื้อ (P/O)
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'inspect' }">
									<a href="{{ url('orders/inspect') }}">
										<i class="fa fa-circle-o"></i> ตรวจรับพัสดุ
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'withdraw' }">
									<a href="{{ url('orders/withdraw') }}">
										<i class="fa fa-circle-o"></i> ส่งเบิกเงิน
									</a>
								</li>
								<!-- <li ng-class="{ 'active': submenu == 'inventory' }">
									<a href="{{ url('orders/inventory') }}">
										<i class="fa fa-circle-o"></i> ส่งคลังพัสดุ
									</a>
								</li> -->
							</ul>
						</li>
					@endif

					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->person_id == '1309900322504' ||
						Auth::user()->memberOf->duty_id == 1 ||
						Auth::user()->memberOf->duty_id == 2 ||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': menu == 'monthly' }">
							<a href="#">
								<i class="fa fa-line-chart" aria-hidden="true"></i>
								<span>ควบคุมกำกับติดตาม</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('monthly/list') }}">
										<i class="fa fa-circle-o"></i> สรุปผลงาน
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'inspect' }">
									<a href="{{ url('monthly/add') }}">
										<i class="fa fa-circle-o"></i> บันทึกรายการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- // Authorize เฉพาะหัวหน้ากลุ่มภารกิจ/ธุรการหรือเลขาฯกลุ่มภารกิจ/หัวหน้ากลุ่มงาน -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->person_id == '1309900322504' ||
						Auth::user()->memberOf->duty_id == 1 ||
						Auth::user()->memberOf->duty_id == 2
					)
						<!-- <li class="treeview" ng-class="{ 'menu-open active': menu == 'reports' }">
							<a href="#">
								<i class="fa fa-pie-chart"></i>
								<span>รายงาน</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'daily' }">
									<a href="{{ url('reports/daily') }}">
										<i class="fa fa-circle-o"></i> สรุปผู้ลาประจำวัน
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'summary' }">
									<a href="{{ url('reports/summary') }}">
										<i class="fa fa-circle-o"></i> สรุปการลา
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'remain' }">
									<a href="{{ url('reports/remain') }}">
										<i class="fa fa-circle-o"></i> สรุปวันลาคงเหลือ
									</a>
								</li>
							</ul>
						</li> -->
					@endif

					@if (Auth::user()->person_id == '1300200009261')
						<li class="treeview" ng-class="{ 'menu-open active': menu == 'system' }">
							<a href="#">
								<i class="fa fa-gear"></i> <span>ข้อมูลระบบ</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'suppliers' }">
									<a href="{{ url('/system/suppliers') }}">
										<i class="fa fa-circle-o"></i> ข้อมูลเจ้าหนี้
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'categories' }">
									<a href="{{ url('/system/categories') }}">
										<i class="fa fa-circle-o"></i> ประเภทครุภัณฑ์
									</a>
								</li>
							</ul>
						</li>
					@endif

				</ul>
			</section><!-- /.sidebar -->

		</aside>
