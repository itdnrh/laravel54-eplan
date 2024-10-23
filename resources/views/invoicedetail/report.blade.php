@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            สรุปค่าใช้จ่ายจากการดำเนินงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">สรุปค่าใช้จ่ายจากการดำเนินงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceDetailCtrl"
        ng-init="
            getReportSummaryByInovice();
            initForms({ departs: {{ $departs }} });
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
                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getReportSummaryByInovice()"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="getReportSummaryByInovice()"
                                        >
                                            <option value="" selected="selected">-- กรุณาเลือก --</option>
                                            @foreach($factions as $faction)
                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="form-group col-md-6">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboApproved"
                                        name="cboApproved"
                                        ng-model="cboApproved"
                                        class="form-control"
                                        ng-change="getPlanByDepart()"
                                    >
                                        <option value="">ยังไม่อนุมัติ</option>
                                        <option value="A">อนุมัติ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ในแผน/นอกแผน</label>
                                        <select
                                            id="cboInPlan"
                                            name="cboInPlan"
                                            ng-model="cboInPlan"
                                            class="form-control"
                                            ng-change="getPlanByDepart()"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="I">ในแผน</option>
                                            <option value="O">นอกแผน</option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">สรุปค่าใช้จ่ายจากการดำเนินงาน ปีงบประมาณ @{{ cboYear }}</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-success pull-right" ng-click="exportToExcel('#tableData')">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableData">
                            <thead>
                                <!-- <tr>
                                    <th style="width: 3%; text-align: center;" rowspan="2">#</th>
                                    <th style="text-align: left;" rowspan="2">หน่วยงาน</th>
                                    <th style="text-align: center;" colspan="2">
                                        <a href="{{ url('/reports/asset-depart') }}">ครุภัณฑ์</a>
                                    </th>
                                    <th style="text-align: center;" colspan="2">
                                        <a href="{{ url('/reports/material-depart') }}">วัสดุ</a>
                                    </th>
                                    <th style="text-align: center;" colspan="2">จ้างบริการ</th>
                                    <th style="text-align: center;" colspan="2">ก่อสร้าง</th>
                                    <th style="text-align: center;" colspan="2">รวม</th>
                                </tr> -->
                                <tr>
                                    <th style="text-align: center; width: 5%;">ลำดับ</th>
                                    <th style="text-align: center; width: 30%;">รายการ</th>
                                    <th style="text-align: center; width: 6%;">ม.ค.</th>
                                    <th style="text-align: center; width: 6%;">ก.พ.</th>
                                    <th style="text-align: center; width: 6%;">มี.ค.</th>
                                    <th style="text-align: center; width: 6%;">เม.ย.</th>
                                    <th style="text-align: center; width: 6%;">พ.ค.</th>
                                    <th style="text-align: center; width: 6%;">มิ.ย.</th>
                                    <th style="text-align: center; width: 6%;">ก.ค.</th>
                                    <th style="text-align: center; width: 6%;">ส.ค.</th>
                                    <th style="text-align: center; width: 6%;">ก.ย.</th>
                                    <th style="text-align: center; width: 6%;">ต.ค.</th>
                                    <th style="text-align: center; width: 6%;">พ.ย.</th>
                                    <th style="text-align: center; width: 6%;">ธ.ค.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index,result) in results" style="font-size: 12px;">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>
                                        @{{ result.invoice_item_name }}
                                    </td>
                                    <td style="text-align: right;">@{{ result.jan | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.feb | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.mar | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.apr | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.may | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.jun | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.jul | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.aug | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.sep | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.oct | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.nov | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ result.dece | currency:'':2 }}</td>
                                </tr>
                               <tr style="font-weight: bold; font-size: 12px;">
                                    <td style="text-align: center;" colspan="2">รวม</td>
                                    <td style="text-align: right;">@{{ totalInvoice.jan_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.feb_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.mar_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.apr_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.may_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.jun_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.jul_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.aug_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.sep_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.oct_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.nov_amount | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalInvoice.dece_amount | currency:'':2 }}</td>
                                </tr>
                                <!--  <tr style="font-weight: bold; font-size: 12px;">
                                    <td style="text-align: center;" colspan="2">คิดเป็น (ร้อยละ) ของทั้งหมด</td>
                                    <td style="text-align: center;">@{{ (totalByPlanTypes.asset * 100)/totalByPlanTypes.total | currency:'':1 }}</td>
                                    <td style="text-align: right;">&nbsp;</td>
                                    <td style="text-align: center;">@{{ (totalByPlanTypes.material * 100)/totalByPlanTypes.total | currency:'':1 }}</td>
                                    <td style="text-align: right;">&nbsp;</td>
                                    <td style="text-align: center;">@{{ (totalByPlanTypes.service * 100)/totalByPlanTypes.total | currency:'':1 }}</td>
                                    <td style="text-align: right;">&nbsp;</td>
                                    <td style="text-align: center;">@{{ (totalByPlanTypes.construct * 100)/totalByPlanTypes.total | currency:'':1 }}</td>
                                    <td style="text-align: right;">&nbsp;</td>
                                    <td style="text-align: center;">100</td>
                                    <td style="text-align: right;">&nbsp;</td>
                                </tr> -->
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-12">

                                <!-- <div id="pieChartContainer" style="width: 100%; height: 400px; margin: 20px auto;"></div> -->

                            </div>
                        </div>

                        <!-- Loading (remove the following to stop the loading)-->
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->

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