@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงาน
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
                <div class="box box-primary" style="height: 80vh;">
                    <div class="box-header with-border table-striped">
                        <h3 class="box-title">รายงาน</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="row" style="margin: auto 10px;">
                            <div class="col-md-6">
                                <h4>แผนเงินบำรุง</h4>
                                <ul style="list-style: none; padding: 5px 5px 5px 20px; font-size: 16px;">
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-faction') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนเงินบำรุงตามกลุ่มภารกิจ
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-depart') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนเงินบำรุงตามหน่วยงาน
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-item') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนเงินบำรุงตามรายการ
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-type') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนเงินบำรุงตามประเภทแผน
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-quarter') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนเงินบำรุงตามไตรมาส
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/plan-process-quarter') }}">
                                            <i class="fa fa-circle-o"></i> รายงานการดำเนินการตามแผนเงินบำรุงตามไตรมาส
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>โครงการ</h4>
                                <ul style="list-style: none; padding: 5px 5px 5px 20px; font-size: 16px;">
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/project-faction') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนงาน/โครงการตามกลุ่มภารกิจ
                                        </a>
                                    </li>    
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/project-depart') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนงาน/โครงการตามหน่วยงาน
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/project-strategic') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนงาน/โครงการตามยุทธศาสตร์
                                        </a>
                                    </li>
                                    <li style="margin: 5px;">
                                        <a href="{{ url('reports/project-quarter') }}">
                                            <i class="fa fa-circle-o"></i> รายงานแผนงาน/โครงการตามไตรมาส
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section>

@endsection