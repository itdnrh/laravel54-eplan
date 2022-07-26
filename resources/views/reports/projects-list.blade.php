@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานโครงการ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานโครงการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="getProjects();"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายงานโครงการ</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 6%; text-align: center;">เลขที่</th>
                                    <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                    <th>รายการ</th>
                                    <th style="width: 8%; text-align: center;">งบที่อนุมัติ</th>
                                    <th style="width: 8%; text-align: center;">ยอดคงเหลือ</th>
                                    <th style="width: 8%; text-align: center;">แหล่งงบฯ</th>
                                    <th style="width: 12%;">ผู้รับผิดชอบ</th>
                                    <th style="width: 8%; text-align: center;">วันที่ดำเนินการ</th>
                                    <th style="width: 15%; text-align: center;">ยอดเบิก</th>
                                    <th style="width: 5%; text-align: center;">AAR</th>
                                    <th style="width: 10%; text-align: center;">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, project) in projects">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">
                                        <h3 class="label label-success" ng-show="project.approved">
                                            @{{ project.project_no }}
                                        </h3>
                                    </td>
                                    <!-- <td style="text-align: center;">@{{ project.year }}</td> -->
                                    <td>
                                        <h5 style="margin: 0; font-weight: bold;">ตัวชี้วัด: @{{ project.kpi.kpi_name }}</h5>
                                        <p style="margin: 0;">
                                            @{{ project.project_name }}
                                        </p>
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                            class="btn btn-default btn-xs" 
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="asset.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.total_budget | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.total_budget - project.actual | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.budget_src.name }}
                                    </td>
                                    <td>
                                        @{{ project.owner.person_firstname+ ' ' +project.owner.person_lastname }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.timeline.start_date | thdate }}
                                    </td>
                                    <td>
                                        <ul style="list-style: none; padding: 5px;" ng-show="project.payments.length > 0">
                                            <li ng-repeat="(index, payment) in project.payments">
                                                ครั้งที่ @{{ index+1 }}. @{{ payment.net_total | currency:'':2 }} บาท
                                            </li>
                                            <li style="font-weight: bold;">
                                                รวม @{{ project.actual | currency:'':2 }} บาท
                                            </li>
                                        </ul>
                                    </td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                            class="btn btn-default btn-xs" 
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="asset.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row" ng-show="false">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setConstructs)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setConstructs)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setConstructs)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setConstructs)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>

@endsection