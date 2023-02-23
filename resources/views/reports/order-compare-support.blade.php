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
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <h3 class="box-title">รายงานจำนวนการออกใบสั่งซื้อ/จ้าง ประจำเดือน :</h3>
                                    <input
                                        type="text"
                                        id="dtpMonth"
                                        name="dtpMonth"
                                        ng-model="dtpMonth"
                                        ng-change="getOrderCompareSupport()"
                                        class="form-control"
                                        style="width: 20%;"
                                    />
                                </div>
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
                                <th style="text-align: center; width: 4%;">#</th>
                                <th>ประเภท</th>
                                <th style="text-align: center; width: 10%;">รับเอกสารแล้ว</th>
                                <th style="text-align: center; width: 10%;">ออก PO แล้ว</th>
                                <th style="text-align: center; width: 10%;">คงเหลือ</th>
                                <th style="text-align: center; width: 10%;">คิดเป็น (%)</th>
                            </tr>
                            <tr ng-repeat="(index, support) in supports">
                                <td style="text-align: center;">@{{ index+1 }}</td>
                                <td>@{{ support.name }}</td>
                                <td style="text-align: center;">@{{ support.received }}</td>
                                <td style="text-align: center;">@{{ support.ordered }}</td>
                                <td style="text-align: center;">@{{ support.received - support.ordered }}</td>
                                <td style="text-align: center;">
                                    <span class="label label-success" ng-show="((support.ordered * 100)/support.received) > 90">
                                        @{{ (support.ordered * 100)/support.received | currency:'':1 }}
                                    </span>
                                    <span class="label label-warning" ng-show="((support.ordered * 100)/support.received) > 60 && ((support.ordered * 100)/support.received) <= 90">
                                        @{{ (support.ordered * 100)/support.received | currency:'':1 }}
                                    </span>
                                    <span class="label label-danger" ng-show="((support.ordered * 100)/support.received) <= 60">
                                        @{{ (support.ordered * 100)/support.received | currency:'':1 }}
                                    </span>
                                </td>
                            </tr>
                            <tr style="font-weight: bold;">
                                <td style="text-align: center;" colspan="2">รวม</td>
                                <td style="text-align: center;">@{{ totalOrderCompareSupport.received }}</td>
                                <td style="text-align: center;">@{{ totalOrderCompareSupport.ordered }}</td>
                                <td style="text-align: center;">
                                    @{{ totalOrderCompareSupport.received - totalOrderCompareSupport.ordered }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ (totalOrderCompareSupport.ordered * 100)/totalOrderCompareSupport.received | currency:'':1 }}
                                </td>
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