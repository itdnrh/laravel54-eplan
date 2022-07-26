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

					<!-- Dashboard -->
					<li ng-class="{ 'active': menu == 'home' }">
						<a href="{{ url('/home') }}">
							<i class="fa fa-dashboard"></i> <span>Dashboard</span>
						</a>
					</li>

					<!-- คำขอประจำปี -->
					<li class="treeview" ng-class="{ 'menu-open active': ['plans','projects'].includes(menu) }">
						<a href="#">
							<i class="fa fa-calendar"></i>
							<span>คำขอประจำปี</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu" ng-style="{ 'display': ['plans'].includes(menu) ? 'block' : 'none' }">
							<li ng-class="{ 'active': ['plans'].includes(menu) && ['assets','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/assets') }}">
									<i class="fa fa-circle-o"></i> ครุภัณฑ์
								</a>
							</li>
							<li ng-class="{ 'active': ['plans'].includes(menu) && ['materials','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/materials?in_stock=0') }}">
									<i class="fa fa-circle-o"></i> วัสดุนอกคลัง
								</a>
							</li>
							@if (
								Auth::user()->person_id == '1300200009261' ||
								Auth::user()->memberOf->depart_id == 2 ||
								count(Auth::user()->delegations) > 0
							)
								<li ng-class="{ 'active': ['plans'].includes(menu) && ['materials','list','add','edit','detail'].includes(submenu)}">
									<a href="{{ url('/plans/materials?in_stock=1') }}">
										<i class="fa fa-circle-o"></i> วัสดุในคลัง
									</a>
								</li>
							@endif
							<li ng-class="{ 'active': ['plans'].includes(menu) && ['services','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/services') }}">
									<i class="fa fa-circle-o"></i> จ้างบริการ
								</a>
							</li>
							<li ng-class="{ 'active': ['plans'].includes(menu) && ['constructs','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/constructs') }}">
									<i class="fa fa-circle-o"></i> ก่อสร้าง
								</a>
							</li>
							<li ng-class="{ 'active': ['plans'].includes(menu) && ['projects','list','add','edit','detail'].includes(submenu)}">
								<a href="{{ url('/plans/projects') }}">
									<i class="fa fa-circle-o"></i> โครงการ
								</a>
							</li>
						</ul>
					</li>

					<!-- การอนุมัติ -->
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
								<li ng-class="{ 'active': ['approvals'].includes(menu) && submenu == 'assets' }">
									<a href="{{ url('approvals/assets') }}">
										<i class="fa fa-circle-o"></i> ครุภัณฑ์
									</a>
								</li>
								<li ng-class="{ 'active': ['approvals'].includes(menu) && submenu == 'materials' }">
									<a href="{{ url('approvals/materials?in_stock=0') }}">
										<i class="fa fa-circle-o"></i> วัสดุนอกคลัง
									</a>
								</li>
								<li ng-class="{ 'active': ['plans','projects'].includes(menu) && ['materials','list','add','edit','detail'].includes(submenu)}">
									<a href="{{ url('/approvals/materials?in_stock=1') }}">
										<i class="fa fa-circle-o"></i> วัสดุในคลัง
									</a>
								</li>
								<li ng-class="{ 'active': ['approvals'].includes(menu) && submenu == 'services' }">
									<a href="{{ url('approvals/services') }}">
										<i class="fa fa-circle-o"></i> จ้างบริการ
									</a>
								</li>
								<li ng-class="{ 'active': ['approvals'].includes(menu) && submenu == 'constructs' }">
									<a href="{{ url('approvals/constructs') }}">
										<i class="fa fa-circle-o"></i> ก่อสร้าง
									</a>
								</li>
								<li ng-class="{ 'active': ['approvals'].includes(menu) && submenu == 'projects' }">
									<a href="{{ url('approvals/projects') }}">
										<i class="fa fa-circle-o"></i> โครงการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- ขอสนับสนุน -->
					<li class="treeview" ng-class="{ 'menu-open active': ['supports','repairs'].includes(menu) }">
						<a href="#">
							<i class="fa fa-handshake-o"></i>
							<span>ขอสนับสนุน</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li ng-class="{ 'active': menu == 'supports' && submenu == 'list' }">
								<a href="{{ url('supports/list') }}">
									<i class="fa fa-circle-o"></i> บันทึกขอสนับสนุน
								</a>
							</li>
							@if (
								Auth::user()->person_id == '1300200009261' ||
								Auth::user()->memberOf->depart_id == 1 ||
								Auth::user()->memberOf->depart_id == 39 ||
								Auth::user()->memberOf->depart_id == 72 ||
								count(Auth::user()->delegations) > 0
							)
								<li ng-class="{ 'active': menu == 'repairs' && submenu == 'list' }">
									<a href="{{ url('repairs/list') }}">
										<i class="fa fa-circle-o"></i> บันทึกขอจ้างซ่อม
									</a>
								</li>
							@endif
							<li ng-class="{ 'active': menu == 'supports' && submenu == 'timeline' }">
								<a href="{{ url('supports/timeline') }}">
									<i class="fa fa-circle-o"></i> ติดตามพัสดุ
								</a>
							</li>
						</ul>
					</li>

					<!-- จัดซื้อจัดจ้าง -->
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
										<i class="fa fa-circle-o"></i> ใบสั่งซื้อ/จ้าง
									</a>
								</li>
								<!-- <li ng-class="{ 'active': submenu == 'repairs-list' }">
									<a href="{{ url('orders/repairs-list') }}">
										<i class="fa fa-circle-o"></i> ใบสั่งจ้าง (งานซ่อม)
									</a>
								</li> -->
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

					<!-- บริหารสัญญา -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 2 ||
						count(Auth::user()->delegations) > 0
					)
						<!-- <li class="treeview" ng-class="{ 'menu-open active': menu == 'monthly' }">
							<a href="#">
								<i class="fa fa-gavel" aria-hidden="true"></i>
								<span>บริหารสัญญา</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('monthly/list') }}">
										<i class="fa fa-circle-o"></i> รายการสัญญา
									</a>
								</li>
							</ul>
						</li> -->
					@endif

					<!-- บริหารโครงการ -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 4 ||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': menu == 'projects' }">
							<a href="#">
								<i class="fa fa-users" aria-hidden="true"></i>
								<span>บริหารโครงการ</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('projects/list') }}">
										<i class="fa fa-circle-o"></i> รายการโครงการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- ค่าสาธารณูปโภค -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 1 ||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': ['utilities'].includes(menu) }">
							<a href="#">
								<i class="fa fa-bolt"></i>
								<span>ค่าสาธารณูปโภค</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu" ng-style="{ 'display': ['utilities'].includes(menu) ? 'block' : 'none' }">
								<li ng-class="{ 'active': submenu == 'summary' }">
									<a href="{{ url('utilities/summary') }}">
										<i class="fa fa-circle-o"></i> สรุปผลงาน
									</a>
								</li>
								<li ng-class="{ 'active': ['assets','list','add','edit','detail'].includes(submenu)}">
									<a href="{{ url('/utilities/list') }}">
										<i class="fa fa-circle-o"></i> รายการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- ควบคุมกำกับติดตาม -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						in_array(Auth::user()->memberOf->depart_id, [1,2,3,4,16,17,18,39,41]) ||
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
								<li ng-class="{ 'active': submenu == 'summary' }">
									<a href="{{ url('monthly/summary') }}">
										<i class="fa fa-circle-o"></i> สรุปผลงาน
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('monthly/list') }}">
										<i class="fa fa-circle-o"></i> รายการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- ประมาณการรายจ่าย -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						Auth::user()->memberOf->depart_id == 4||
						count(Auth::user()->delegations) > 0
					)
						<li class="treeview" ng-class="{ 'menu-open active': menu == 'budgets' }">
							<a href="#">
								<i class="fa fa-tags" aria-hidden="true"></i>
								<span>ประมาณการรายจ่าย</span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li ng-class="{ 'active': submenu == 'list' }">
									<a href="{{ url('budgets/list') }}">
										<i class="fa fa-circle-o"></i> รายการ
									</a>
								</li>
							</ul>
						</li>
					@endif

					<!-- รายงาน -->
					<li class="treeview" ng-class="{ 'menu-open active': menu == 'reports' }">
						<a href="#">
							<i class="fa fa-pie-chart"></i>
							<span>รายงาน</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li ng-class="{ 'active': submenu == 'daily' }">
								<a href="{{ url('reports/summary-depart') }}">
									<i class="fa fa-circle-o"></i> แผนเงินบำรุงรายหน่วยงาน
								</a>
							</li>
						</ul>
					</li>

					<!-- ข้อมูลระบบ -->
					@if (
						Auth::user()->person_id == '1300200009261' ||
						count(Auth::user()->delegations) > 0
					)
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
										<i class="fa fa-circle-o"></i> เจ้าหนี้
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'items' }">
									<a href="{{ url('/system/items') }}">
										<i class="fa fa-circle-o"></i> สินค้า/บริการ
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'expenses' }">
									<a href="{{ url('/system/expenses') }}">
										<i class="fa fa-circle-o"></i> รายจ่าย
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'persons' }">
									<a href="{{ url('/system/persons') }}">
										<i class="fa fa-circle-o"></i> ข้อมูลบุคลากร
									</a>
								</li>
								<li ng-class="{ 'active': submenu == 'kpis' }">
									<a href="{{ url('/system/kpis') }}">
										<i class="fa fa-circle-o"></i> ตัวชี้วัด (KPI)
									</a>
								</li>
							</ul>
						</li>
					@endif

				</ul>
			</section><!-- /.sidebar -->

		</aside>
