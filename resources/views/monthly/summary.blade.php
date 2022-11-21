@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            สรุปผลการดำเนินงานแผนเงินบำรุง
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">สรุปผลการดำเนินงานแผนเงินบำรุง</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="monthlyCtrl"
        ng-init="getSummary();"
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
                                        ng-change="getSummary()"
                                    >
                                        <!-- <option value="">-- ทั้งหมด --</option> -->
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ประเภทค่าใช้จ่าย</label>
                                        <select
                                            id="cboExpenseType"
                                            name="cboExpenseType"
                                            ng-model="cboExpenseType"
                                            ng-change="getSummary()"
                                            class="form-control"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($expenseTypes as $type)
                                                <option value="{{ $type->id }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">สรุปผลการดำเนินงานแผนเงินบำรุง</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/monthly/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;" rowspan="2">#</th>
                                    <th rowspan="2">รายการ</th>
                                    <th style="width: 8%; text-align: center;" rowspan="2">ประมาณการ</th>
                                    <th style="width: 8%; text-align: center;" colspan="13">ผลการดำเนินงาน</th>
                                    <th style="width: 5%; text-align: center;" rowspan="2">คงเหลือ</th>
                                    <th style="width: 5%; text-align: center;" rowspan="2">ใช้ไปร้อยละ</th>
                                </tr>
                                <tr>
                                    <th style="width: 5%; text-align: center;">ต.ค.</th>
                                    <th style="width: 5%; text-align: center;">พ.ย.</th>
                                    <th style="width: 5%; text-align: center;">ธ.ค.</th>
                                    <th style="width: 5%; text-align: center;">ม.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.พ.</th>
                                    <th style="width: 5%; text-align: center;">มี.ค.</th>
                                    <th style="width: 5%; text-align: center;">เม.ย.</th>
                                    <th style="width: 5%; text-align: center;">พ.ค.</th>
                                    <th style="width: 5%; text-align: center;">มิ.ย.</th>
                                    <th style="width: 5%; text-align: center;">ก.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.ย.</th>
                                    <th style="width: 5%; text-align: center;">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, sum) in summary" style="font-size: 12px;">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>@{{ sum.name }}</td>
                                    <td style="text-align: right;">
                                        @{{ sum.budget | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.oct_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.nov_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.dec_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.jan_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.feb_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.mar_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.apr_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.may_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.jun_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.jul_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.aug_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.sep_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ sum.budget - sum.total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: center;">
                                        <h4 ng-class="{
                                            'label label-danger': ((sum.total * 100) / sum.budget) > 80,
                                            'label label-warning': ((sum.total * 100) / sum.budget) > 60,
                                            'label label-success': ((sum.total * 100) / sum.budget) <= 50,
                                        }">
                                            @{{ (sum.total * 100) / sum.budget | currency:'':1 }}
                                        </h4>
                                    </td>
                                </tr>
                                <tr style="font-size: 12px;">
                                    <td style="text-align: center;" colspan="2">รวม</td>
                                    <td style="text-align: right;">@{{ totalSummary.budget | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.oct | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.nov | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.dec | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.jan | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.feb | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.mar | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.apr | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.may | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.jun | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.jul | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.aug | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.sep | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.total | currency:'':2 }}</td>
                                    <td style="text-align: right;">@{{ totalSummary.remain | currency:'':2 }}</td>
                                    <td style="text-align: center;">
                                        <h4 ng-class="{
                                            'label label-danger': ((totalSummary.total * 100) / totalSummary.budget) > 80,
                                            'label label-warning': ((totalSummary.total * 100) / totalSummary.budget) > 60,
                                            'label label-success': ((totalSummary.total * 100) / totalSummary.budget) <= 50,
                                        }">
                                            @{{ (totalSummary.total * 100) / totalSummary.budget | currency:'':2 }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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