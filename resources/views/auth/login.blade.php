<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>E-Plan Management System</title>

    <link rel="stylesheet" href="{{ asset('/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('/css/skins/_all-skins.min.css') }}">
    <!-- Fonts -->
    <link rel='stylesheet' href='//fonts.googleapis.com/css?family=Roboto:400,300' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body class="hold-transition skin-blue sidebar-mini" ng-app="app" ng-controller="mainCtrl">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="display: flex; justify-content: center; align-items: center;">

            <div class="row" style="width: 100vw">
                <div class="col-md-8 col-md-offset-2">

                    @if (session('status'))
                        <div class="alert alert-danger">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div style="text-align: center;">
                        <h1>E-Plan <b>MS<b></h1>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">ลงชื่อเข้าใช้งานระบบ</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/signin') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('person_username') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">ชื่อผู้ใช้</label>

                                    <div class="col-md-6">
                                        <input id="person_username" type="text" class="form-control" name="person_username" value="{{ old('person_username') }}">

                                        @if ($errors->has('person_username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('person_username') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('person_password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">รหัสผ่าน</label>

                                    <div class="col-md-6">
                                        <input id="person_password" type="password" class="form-control" name="person_password">

                                        @if ($errors->has('person_password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('person_password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember"> Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-btn fa-sign-in"></i> ลงชื่อเข้า
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-md-12 col-md-offset-4">
                                    <a href="{{ url('/auth/checking') }}" style="margin-right: 10px;">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        ตรวจสอบชื่อผู้ใช้และรหัสผ่าน
                                    </a>
                                    <!-- |  -->
                                    <!-- <a href="{{ url('/password/reset') }}" style="margin-left: 10px;">
                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                        Forgot Your Password?
                                    </a> -->
                                </div>
                            </div>

                            <!-- <hr /> -->

                            <!-- <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12 col-md-offset-4">
                                    [ <a href="{{ asset('/uploads/manuals/') }}">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        คู่มือการใช้งาน (สำหรับผู้ใช้ทั่วไป)
                                    </a> ]
                                </div>
                            </div> -->
                        </div><!--- /.panel-body -->
                    </div><!--- /.panel -->

                </div><!-- /.col -->
            </div><!-- /.row -->

        </div><!-- /.content-wrapper -->

        <!-- Footer -->
        @extends('layouts.footer')
        <!-- Footer -->

    </div><!-- /.wrapper -->

    <!-- Scripts -->
    <script src="{{ asset('/node_modules/jquery/dist/jquery.min.js') }}"></script>
	<script src="{{ asset('/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/js/libraries/adminlte.min.js') }}"></script>
</body>
</html>