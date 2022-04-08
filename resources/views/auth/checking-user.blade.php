<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Accounts Payable System</title>

    <link rel="stylesheet" href="{{ asset('/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('/css/skins/_all-skins.min.css') }}">

    <!-- Fonts -->
    <link rel='stylesheet' href='//fonts.googleapis.com/css?family=Roboto:400,300' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Scripts -->
    <script src="{{ asset('/node_modules/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    
    <!-- AdminLTE App -->
    <script src="{{ asset('/js/adminlte.min.js') }}"></script>

</head>
<body class="hold-transition skin-blue sidebar-mini" ng-app="app" ng-controller="mainCtrl">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <div class="row">
                <div class="col-md-8 col-md-offset-2">

                    @if (session('status'))
                        <div class="alert alert-danger">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="box box-default" style="margin-top: 15%;">
                        <div class="box-header">
                            <h3 class="box-title">ตรวจสอบชื่อผู้ใช้และรหัสผ่าน</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/checking') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('cid') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">เลขบัตรประชาชน (13 หลัก) :</label>

                                    <div class="col-md-6">
                                        <input type="text" id="cid" name="cid"  class="form-control" value="{{ old('cid') }}">

                                        @if ($errors->has('person_username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('cid') }}</strong>
                                            </span>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('birthdate') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">วันเดือนปีเกิด (ตัวอย่าง 01012525 ) :</label>
                                    <div class="col-md-6">
                                        <input type="text" id="birthdate" name="birthdate" class="form-control" />

                                        @if ($errors->has('birthdate'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('birthdate') }}</strong>
                                            </span>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-btn fa-paper-plane"></i> ตรวจสอบ
                                        </button>

                                        <!-- <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a> -->
                                    </div>
                                </div>
                            </form>

                            @if (session('existed_user'))
                                <div class="panel panel-success" style="margin: 20px;">
                                    <div class="panel-heading">ผลการตรวจสอบ</div>
                                    <div class="panel-body">
                                        <h3 style="margin: 10px;">ข้อมูลผู้ใช้ของคุณ</h3>

                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 10%; text-align: right;">ชื่อผู้ใช้ : </td>
                                                    <td style="font-weight: bold;">{{ session('existed_user')->person_username }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">รหัสผ่าน : </td>
                                                    <td style="font-weight: bold;">{{ session('existed_user')->person_password }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        [ <a href="{{ url('/') }}">
                                                            <i class="fa fa-btn fa-sign-in"></i>
                                                            ลงชื่อเข้าใช้งานระบบ
                                                        </a> ]
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <span style="color: red;">
                                            (กรุณาเก็บไว้เป็นความลับและอย่าให้ผู้อื่นรู้เด็ดขาด ทั้งนี้เพื่อความปลอดภัยและความเป็นส่วนตัวของคุณ)
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="panel panel-danger" style="margin: 20px;">
                                    <div class="panel-heading">ผลการตรวจสอบ</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: center; color: red;">
                                                -- ไม่พบข้อมูล --
                                                <p style="margin-top: 10px; color: red;">
                                                    (กรุณาติดต่อผู้ดูแลระบบ โทรภายใน 2505)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                                
                        </div>
                    </div><!--- /.panel -->

                </div><!-- /.col -->
            </div><!-- /.row -->

        </div><!-- /.content-wrapper -->

        <!-- Footer -->
        @extends('layouts.footer')
        <!-- Footer -->

    </div><!-- /.wrapper -->
</body>
</html>