@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานแผนเงินบำรุงตามกลุ่มภารกิจ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานแผนเงินบำรุงตามกลุ่มภารกิจ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getPlanByFaction();
            initForm({
                departs: {{ $departs }}
            });
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <!-- // TODO: should use datepicker instead -->
                            <div class="form-group col-md-6">
                                <label>ปีงบประมาณ</label>
                                <select
                                    id="cboYear"
                                    name="cboYear"
                                    ng-model="cboYear"
                                    class="form-control"
                                    ng-change="getPlanByFaction()"
                                >
                                    <option value="">-- ทั้งหมด --</option>
                                    <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                        @{{ y }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>สถานะ</label>
                                <select
                                    id="cboApproved"
                                    name="cboApproved"
                                    ng-model="cboApproved"
                                    class="form-control"
                                    ng-change="getPlanByFaction()"
                                >
                                    <option value="">ยังไม่อนุมัติ</option>
                                    <option value="A">อนุมัติ</option>
                                </select>
                            </div>

                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <h3 class="box-title">รายงานแผนเงินบำรุงตามกลุ่มภารกิจ ปีงบประมาณ @{{ cboYear }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="text-align: left;">หน่วยงาน</th>
                                    <th style="text-align: right;">ครุภัณฑ์</th>
                                    <th style="text-align: right;">วัสดุ</th>
                                    <th style="text-align: right;">จ้างบริการ</th>
                                    <th style="text-align: right;">ก่อสร้าง</th>
                                    <th style="text-align: right;">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>
                                        @{{ plan.faction_name }}
                                    </td>
                                    <td style="text-align: right;">@{{ plan.asset | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.material | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.service | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.construct | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.total | currency:'':0 }}</td>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td style="text-align: center;" colspan="2">รวม</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.asset | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.material | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.service | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.construct | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.total | currency:'':0 }}</td>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td style="text-align: center;" colspan="2">ร้อยละ</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.asset*100/totalByPlanTypes.total | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.material*100/totalByPlanTypes.total | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.service*100/totalByPlanTypes.total | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanTypes.construct*100/totalByPlanTypes.total | currency:'':2 }}</td>
                                    <td style="text-align: right;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" ng-show="false">
                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right">
                                    <li ng-if="pager.current_page !== 1">
                                        <a ng-click="getDataWithURL(pager.path+ '?page=1')" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getDataWithURL(pager.prev_page_url)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
        
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getDataWithURL(pager.next_page_url)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
        
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getDataWithURL(pager.path+ '?page=' +pager.last_page)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-footer -->

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