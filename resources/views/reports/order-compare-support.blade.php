@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานจำนวนการออกใบสั่งซื้อ/จ้างประจำเดือน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานจำนวนการออกใบสั่งซื้อ/จ้างประจำเดือน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            initForm({ 
                factions: {{ $factions }},
                departs: {{ $departs }}
            });
            getOrderCompareSupport();
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
                                        ng-change="getOrderCompareSupport()"
                                        class="form-control"
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
                                        ng-change="getOrderCompareSupport()"
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
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>ประจำเดือน</label>
                                    <input
                                        type="text"
                                        id="dtpMonth"
                                        name="dtpMonth"
                                        ng-model="dtpMonth"
                                        class="form-control"
                                    />
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายงานจำนวนการออกใบสั่งซื้อ/จ้างประจำเดือน</h3>
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
                        <table class="table table-bordered">
                            <tr>
                                <th style="text-align: center; width: 5%;">#</th>
                                <th>ประเภท</th>
                                <th style="text-align: center; width: 10%;">ส่งเอกสารแล้ว</th>
                                <th style="text-align: center; width: 10%;">ออก PO แล้ว</th>
                                <th style="text-align: center; width: 10%;">คงเหลือ</th>
                                <th style="text-align: center; width: 10%;">คิดเป็น (%)</th>
                            </tr>
                            <tr ng-repeat="(index, support) in supports">
                                <td style="text-align: center;">@{{ index+1 }}</td>
                                <td>@{{ support.name }}</td>
                                <td style="text-align: center;">@{{ support.sent }}</td>
                                <td style="text-align: center;">@{{ support.ordered }}</td>
                                <td style="text-align: center;">@{{ support.sent - support.ordered }}</td>
                                <td style="text-align: center;">
                                    <span class="label label-success" ng-show="((support.ordered * 100)/support.sent) > 90">
                                        @{{ (support.ordered * 100)/support.sent | currency:'':1 }}
                                    </span>
                                    <span class="label label-warning" ng-show="((support.ordered * 100)/support.sent) > 60 && ((support.ordered * 100)/support.sent) <= 90">
                                        @{{ (support.ordered * 100)/support.sent | currency:'':1 }}
                                    </span>
                                    <span class="label label-danger" ng-show="((support.ordered * 100)/support.sent) <= 60">
                                        @{{ (support.ordered * 100)/support.sent | currency:'':1 }}
                                    </span>
                                </td>
                            </tr>
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