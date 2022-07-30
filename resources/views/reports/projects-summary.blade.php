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
        ng-init="getProjectSummary();"
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
                                    <th>กลุ่มภารกิจ</th>
                                    <th style="width: 8%; text-align: center;">จำนวนโครงการ</th>
                                    <th style="width: 8%; text-align: center;">ดำเนินการแล้ว</th>
                                    <th style="width: 10%; text-align: center;">ยอดประมาณการ</th>
                                    <th style="width: 10%; text-align: center;">ยอดดำเนินการ</th>
                                    <th style="width: 10%; text-align: center;">ยอดเบิกจ่าย</th>
                                    <th style="width: 10%; text-align: center;">ยอดเบิกร้อยละ</th>
                                    <th style="width: 10%; text-align: center;">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, faction) in factions">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>@{{ faction.faction_name }}</td>
                                    <td style="text-align: center;">
                                        @{{ faction.projects.length | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ faction.done.length | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ faction.total_budget | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ faction.total_actual | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ faction.patment | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ (faction.patment * 100) / faction.total_budget | currency:'':2 }}%
                                    </td>
                                    <td>
                                        <!-- หมายเหตุ -->
                                    </td>
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