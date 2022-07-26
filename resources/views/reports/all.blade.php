@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แผนเงินบำรุงรายหน่วยงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="reportCtrl">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border table-striped">
                        <h3 class="box-title">รายงาน</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul style="list-style: none; padding: 5px; padding-left: 10px;">
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/summary-depart') }}">
                                            <i class="fa fa-circle-o"></i> แผนเงินบำรุงรายหน่วยงาน
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/summary-depart') }}">
                                            <i class="fa fa-circle-o"></i> รายการครุภัณฑ์ราคา > 100,000 บาท
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul style="list-style: none; padding: 5px; padding-left: 10px;">
                                    <!-- <li>
                                        <a href="{{ url('reports/summary-depart') }}">
                                            <i class="fa fa-circle-o"></i> แผนเงินบำรุงรายหน่วยงาน
                                        </a>
                                    </li> -->
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section>

@endsection