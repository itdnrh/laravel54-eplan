@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานจำนวนการออกใบสั่งซื้อ/จ้างย้อนหลังประจำเดือน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานจำนวนการออกใบสั่งซื้อ/จ้างย้อนหลังประจำเดือน</li>
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
            getOrderBackwardMonth();
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
                                        ng-change="getOrderBackwardMonth()"
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
                                        ng-change="getOrderBackwardMonth()"
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
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <div class="row">
                            <div class="col-md-8">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <h3 class="box-title">
                                        รายงานจำนวนการออกใบสั่งซื้อ/จ้างย้อนหลัง (30 วันขึ้นไป) ประจำเดือน : 
                                    </h3>
                                    <input
                                        type="text"
                                        id="dtpMonth"
                                        name="dtpMonth"
                                        ng-model="dtpMonth"
                                        ng-change="getOrderBackwardMonth()"
                                        class="form-control"
                                        style="width: 20%;"
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
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
                                <th style="text-align: center; width: 4%;" rowspan="2">#</th>
                                <th rowspan="2">ประเภท</th>
                                <th style="text-align: center; width: 10%;" colspan="2">PO ทั้งหมด</th>
                                <th style="text-align: center; width: 10%;" colspan="2">PO ย้อนหลัง</th>
                            </tr>
                            <tr>
                                <th style="text-align: center; width: 8%;">จำนวน</th>
                                <th style="text-align: center; width: 10%;">ยอดเงิน</th>
                                <th style="text-align: center; width: 8%;">จำนวน</th>
                                <th style="text-align: center; width: 10%;">ยอดเงิน</th>
                                <!-- <th style="text-align: center; width: 10%;">คิดเป็น (%)</th> -->
                            </tr>
                            <tr ng-repeat="(index, order) in orders">
                                <td style="text-align: center;">@{{ index+1 }}</td>
                                <td>@{{ order.name }}</td>
                                <td style="text-align: center;">@{{ order.all_po }}</td>
                                <td style="text-align: right;">@{{ order.all_net | currency:'':2 }}</td>
                                <td style="text-align: center;">@{{ order.back_po }}</td>
                                <td style="text-align: right;">@{{ order.back_net | currency:'':2 }}</td>
                            </tr>
                            <tr style="font-weight: bold;">
                                <td style="text-align: center;" colspan="2">รวม</td>
                                <td style="text-align: center;">@{{ totalOrderBackwardMonth.all_po }}</td>
                                <td style="text-align: right;">@{{ totalOrderBackwardMonth.all_net | currency:'':2 }}</td>
                                <td style="text-align: center;">@{{ totalOrderBackwardMonth.back_po }}</td>
                                <td style="text-align: right;">@{{ totalOrderBackwardMonth.back_net | currency:'':2 }}</td>
                            </tr>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" ng-show="false">
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