@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานแผนเงินบำรุงตามหน่วยงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานแผนเงินบำรุงตามหน่วยงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getSummaryByDepart();
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
                            <!-- <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->person_id }} == '1300200009261'">
                                <div class="form-group">
                                    <label>กลุ่มภารกิจ</label>
                                    <select
                                        id="faction"
                                        name="faction"
                                        ng-model="cboFaction"
                                        class="form-control select2"
                                        style="width: 100%; font-size: 12px;"
                                        ng-change="onSelectedFaction(cboFaction)"
                                    >
                                        <option value="" selected="selected">-- กรุณาเลือก --</option>
                                        <option
                                            ng-repeat="faction in initFormValues.factions"
                                            value="@{{ faction.faction_id }}"
                                        >
                                            @{{ faction.faction_name }}
                                        </option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->person_id }} == '1300200009261'">
                                <div class="form-group">
                                    <label>กลุ่มงาน</label>
                                    <select
                                        id="depart"
                                        name="depart"
                                        ng-model="cboDepart"
                                        class="form-control select2"
                                        style="width: 100%; font-size: 12px;"
                                        ng-change="getSummaryByDepart();"
                                    >
                                        <option value="" selected="selected">-- กรุณาเลือก --</option>
                                        <option
                                            ng-repeat="depart in filteredDeparts"
                                            value="@{{ depart.depart_id }}"
                                        >
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                </div>
                            </div> -->
                            <!-- // TODO: should use datepicker instead -->
                            <div class="form-group col-md-6">
                                <label>ปีงบประมาณ</label>
                                <select
                                    id="cboYear"
                                    name="cboYear"
                                    ng-model="cboYear"
                                    class="form-control"
                                    ng-change="getSummaryByDepart()"
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
                                    ng-change="getSummaryByDepart()"
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
                        <h3 class="box-title">รายงานแผนเงินบำรุงตามหน่วยงาน ปีงบประมาณ @{{ dtpYear }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="text-align: left;">หน่วยงาน</th>
                                    <th style="text-align: right;">
                                        <a href="{{ url('/reports/asset-depart') }}">ครุภัณฑ์</a>
                                    </th>
                                    <th style="text-align: right;">
                                        <a href="{{ url('/reports/material-depart') }}">วัสดุ</a>
                                    </th>
                                    <th style="text-align: right;">จ้างบริการ</th>
                                    <th style="text-align: right;">ก่อสร้าง</th>
                                    <th style="text-align: right;">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>
                                        @{{ plan.depart_name }}
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