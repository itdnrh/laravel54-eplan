@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานแผนเงินบำรุงตามไตรมาส
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานแผนเงินบำรุงตามไตรมาส</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getPlanByQuarter();
            initForm({ 
                factions: {{ $factions }},
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

                            <div class="row">
                                <!-- // TODO: should use datepicker instead -->
                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getPlanByQuarter()"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภทแผน</label>
                                    <select
                                        id="cboPlanType"
                                        name="cboPlanType"
                                        ng-model="cboPlanType"
                                        ng-change="getPlanByQuarter()"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ราคาต่อหน่วย</label>
                                    <select
                                        id="cboPrice"
                                        name="cboPrice"
                                        ng-model="cboPrice"
                                        class="form-control"
                                        ng-change="getPlanByQuarter()"
                                    >
                                        <option value="">-- เลือก --</option>
                                        <option value="1">ราคา 10,000 บาทขึ้นไป</option>
                                        <option value="2">ราคา น้อยกว่า 10,000 บาท</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เรียมลำดับ</label>
                                    <select
                                        id="cboSort"
                                        name="cboSort"
                                        ng-model="cboSort"
                                        class="form-control"
                                        ng-change="getPlanByQuarter()"
                                    >
                                        <option value="">-- เลือก --</option>
                                        <option value="sum_price">งบประมาณ</option>
                                        <option value="amount">จำนวนที่ขอ</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboApproved"
                                        name="cboApproved"
                                        ng-model="cboApproved"
                                        class="form-control"
                                        ng-change="getPlanByQuarter()"
                                    >
                                        <option value="">ยังไม่อนุมัติ</option>
                                        <option value="A">อนุมัติ</option>
                                    </select>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <h3 class="box-title">รายงานแผนเงินบำรุงตามไตรมาส ปีงบประมาณ @{{ cboYear }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;" rowspan="2">#</th>
                                    <th style="text-align: left;" rowspan="2">ประเภท</th>
                                    <th style="text-align: center;" colspan="2">ไตรมาส 1</th>
                                    <th style="text-align: center;" colspan="2">ไตรมาส 2</th>
                                    <th style="text-align: center;" colspan="2">ไตรมาส 3</th>
                                    <th style="text-align: center;" colspan="2">ไตรมาส 4</th>
                                </tr>
                                <tr>
                                    <th style="width: 6%; text-align: right;">จน.ที่ขอ</th>
                                    <th style="width: 10%; text-align: right;">งบประมาณ</th>
                                    <th style="width: 6%; text-align: right;">จน.ที่ขอ</th>
                                    <th style="width: 10%; text-align: right;">งบประมาณ</th>
                                    <th style="width: 6%; text-align: right;">จน.ที่ขอ</th>
                                    <th style="width: 10%; text-align: right;">งบประมาณ</th>
                                    <th style="width: 6%; text-align: right;">จน.ที่ขอ</th>
                                    <th style="width: 10%; text-align: right;">งบประมาณ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>
                                        @{{ plan.category_name }}
                                    </td>
                                    <td style="text-align: right;">@{{ plan.q1_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q1_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q2_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q2_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q3_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q3_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q4_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.q4_sum | currency:'':0 }}</td>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td style="text-align: center;" colspan="2">รวม</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q1_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q1_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q2_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q2_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q3_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q3_sum | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q4_amt | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByPlanQuarters.q4_sum | currency:'':0 }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-12">

                                <div id="pieChartContainer" style="width: 100%; height: 400px; margin: 20px auto;"></div>

                            </div>
                        </div>
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