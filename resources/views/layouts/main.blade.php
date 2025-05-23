<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>E-Plan 2568 Version : 680212</title>

</head>

	<!-- icon -->
	<link rel="icon" type="image/ico" href="{{ asset('/img/favicon.ico') }}">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/img/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/img/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/img/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ asset('/img/site.webmanifest') }}">
	<!-- bootstrap -->
	<link rel="stylesheet" href="{{ asset('/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
	<!-- select2 -->
	<link rel="stylesheet" href="{{ asset('/node_modules/select2/dist/css/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}">
	<!-- Ionicons -->
	<link rel="stylesheet" href="{{ asset('/css/ionicons.min.css') }}">
	<!-- jQuery jvectormap -->
	<link rel="stylesheet" href="{{ asset('/css/jquery-jvectormap.css') }}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('/css/AdminLTE.min.css') }}">
	<!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="{{ asset('/css/skins/_all-skins.min.css') }}">
	<!-- Fonts -->
	<link rel='stylesheet' href='//fonts.googleapis.com/css?family=Roboto:400,300' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- 3rd parties -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/balloon-css/0.5.0/balloon.min.css">
	<link rel="stylesheet" href="{{ asset('/node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/daterangepicker/daterangepicker.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/jquery-ui-dist/jquery-ui.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/angularjs-toaster/toaster.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/fullcalendar/dist/fullcalendar.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/ng-tags-input/build/ng-tags-input.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/node_modules/angular-xeditable/dist/css/xeditable.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/main.css') }}">
	<!-- Inline Style -->
	<style type="text/css">
		.has-error .select2-selection {
			border-color:#a94442;
			-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);
			box-shadow:inset 0 1px 1px rgba(0,0,0,.075)
		}
	</style>

	<!-- Scripts -->
	<script type="text/javascript" src="{{ asset('/js/env.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/jquery/dist/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/angular/angular.min.js') }}"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0-beta.1/angular-route.js"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/angular-animate/angular-animate.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/angularjs-toaster/toaster.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/angular-modal-service/dst/angular-modal-service.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/angular-xeditable/dist/js/xeditable.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/moment/moment.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/underscore/underscore-min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/fullcalendar/dist/fullcalendar.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/fullcalendar/dist/locale/th.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/ng-tags-input/build/ng-tags-input.min.js') }}"></script>
	<!-- jQuery-UI -->
	<script type="text/javascript" src="{{ asset('/node_modules/jquery-ui-dist/jquery-ui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
	<!-- Other -->
	<!--<script type="text/javascript" src="{{ asset('/bower/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min.js') }}"></script>-->
	<script type="text/javascript" src="{{ asset('/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/daterangepicker/daterangepicker.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/libraries/bootstrap-datepicker-custom.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.th.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/libraries/jquery.knob.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/libraries/fastclick.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/utils/thaibath.js') }}"></script>
	<!-- Highcharts -->
	<script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>
	<script type="text/javascript" src="http://code.highcharts.com/highcharts-more.js"></script>	
	<!-- AdminLTE App -->
	<script type="text/javascript" src="{{ asset('/js/libraries/adminlte.min.js') }}"></script>
	<!-- AngularJS Components -->
	<script type="text/javascript" src="{{ asset('/js/main.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/mainCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/homeCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/approvalCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/planAssetCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/itemCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/planMaterialCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/planServiceCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/planConstructCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/inspectionCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/withdrawalCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/supportCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/orderCtrl.js') }}"></script>

	<!-- // MAXX -->
	<script type="text/javascript" src="{{ asset('/js/controllers/invoiceCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/invoiceDetailCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/approvedSupportCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/invoiceItemDetailCtrl.js') }}"></script>

	<script type="text/javascript" src="{{ asset('/js/controllers/receivingCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/personCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/reportCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/supplierCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/kpiCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/projectCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/monthlyCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/repairCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/utilityCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/budgetCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/expenseCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/provinceCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/delegationCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/factionCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/departCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/controllers/divisionCtrl.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/report.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/stringFormat.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/pagination.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/datetime.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/chart.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/services/excel.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/filters/thdate.js') }}"></script>

	<!--<script type="text/javascript" src="{{ asset('/js/directives/highcharts.js') }}"></script>-->

	<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
	<!--<script type="text/javascript" src="{{ asset('/js/services/dashboard.js') }}"></script>-->
	<!-- AdminLTE for demo purposes -->
	<!--<script type="text/javascript" src="{{ asset('/js/services/demo.js') }}"></script>-->

	
	<!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<!-- To set sidebar mini style on init use .sidebar-collapse to body tag -->
<body class="skin-blue hold-transition sidebar-mini" ng-app="app" ng-controller="mainCtrl" ng-init="setActivedMenu()"> 
	<div class="wrapper">
		<!-- header -->		
		@include('layouts.header')		

		<!-- sidebar -->
		@include('layouts.sidebar')

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">

            @yield('content')

            <toaster-container></toaster-container>
				
		</div><!-- /.content-wrapper -->

		<!-- Footer -->
		@include('layouts.footer')

		<!-- Control Sidebar -->
		@include('layouts.control-sidebar')

	</div><!-- ./wrapper -->
</body>
</html>
