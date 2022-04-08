@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ประวัติการลา
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ประวัติการลา</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="historyCtrl"
        ng-init="getPersonHistories({{ Auth::user()->person_id }}); getSummary({{ Auth::user()->person_id }});"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>ปีงบประมาณ</label>
                            <select
                                id="cboYear"
                                name="cboYear"
                                ng-model="cboYear"
                                class="form-control"
                                ng-change="
                                    getPersonHistories({{ Auth::user()->person_id }});
                                    getSummary({{ Auth::user()->person_id }});
                                "
                            >
                                <option value="">-- ทั้งหมด --</option>
                                <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                    @{{ y }}
                                </option>
                            </select>
                        </div><!-- /.form group -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <div class="row">
                    <div class="col-md-4">
                        <div class="box box-success">
                            <div class="box-header">
                                <h3 class="box-title">สถิติการลา ปีงบประมาณ @{{ cboYear }}</h3>
                            </div>
                            <div class="box-body">

                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fa fa-warning"></i>
                                    สถิติการลาที่แสดงนับเฉพาะที่ผู้ใช้ลาในระบบเท่านั้น !!
                                </div>

                                <div style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;">
                                    <h4>ลาป่วย (ุ60 วันทำการ)</h4>
                                    <p>จำนวนวันลาสะสม @{{ !histories ? 60 : 60 - histories.ill_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 60 : histories.ill_days }} วัน</p>
                                </div>
                                <div style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;">
                                    <h4>ลากิจส่วนตัว (45 วันทำการ)</h4>
                                    <p>จำนวนวันลาสะสม @{{ !histories ? 60 : 45 - histories.per_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 60 : histories.per_days }} วัน</p>
                                    <p style="color: red;">(รับราชการปีแรก ไม่เกิน 15 วันทำการ)</p>
                                </div>
                                <div style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;">
                                    <h4>ลาพักผ่อน (@{{ !vacation ? 10 : vacation.all_days }} วันทำการ)</h4>
                                    <p>จำนวนวันลาสะสม @{{ !vacation ? 10 : vacation.all_days - histories.vac_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 10 : histories.vac_days }} วัน</p>
                                    <p style="color: red;">(รับราชการปีแรกต้องทำงานครบ 6 เดือน จึงมีสิทธิ์ลาได้)</p>
                                </div>
                                <div
                                    style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;"
                                    ng-show="{{ Auth::user()->person_sex }} == 2"
                                >
                                    <h4>ลาคลอด (90 วัน)</h4>
                                    <p>จำนวนวันลาสะสม @{{ !histories ? 90 : 90 - histories.lab_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 90 : histories.lab_days }} วัน</p>
                                </div>
                                <div
                                    style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;"
                                    ng-show="{{ Auth::user()->person_sex }} == 1"
                                >
                                    <h5 style="font-weight: bold;">ลาเพื่อดูแลบุตรและภรรยาหลังคลอด (15 วันทำการ)</h5>
                                    <p>จำนวนวันลาสะสม @{{ !histories ? 15 : 15 - histories.hel_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 15 : histories.hel_days }} วัน</p>
                                    <p style="color: red;">(ขอลาภายใน 90 วัน นับแต่วันที่ภรรยาคลอดบุตร)</p>
                                </div>
                                <div style="border: 1px solid grey; margin-bottom: 1rem; padding: 0.5em;">
                                    <h4>ลาอุปสมบท (120 วัน)</h4>
                                    <p>จำนวนวันลาสะสม @{{ !histories ? 120 : 120 - histories.ord_days }} วัน</p>
                                    <p>จำนวนวันที่ลา @{{ !histories ? 120 : histories.ord_days }} วัน</p>
                                    <p style="color: red;">(ต้องรับราชการมาแล้ว 12 เดือน)</p>
                                </div>
                                <!-- TODO: ใช้การลากิจ/พักผ่อน + บันทึกข้อความไปต่างประเทศ -->
                                <!-- <div style="border: 1px solid grey; padding: 0.5em;">
                                    <h4>ลาไปต่างประเทศ</h4>
                                    <p>จำนวนวันลาสะสม - วัน</p>
                                    <p>จำนวนวันที่ลา - วัน</p>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="box box-danger">
                            <div class="box-header">
                                <div class="row">
                                    <div class="col-md-8" style="margin-top: 8px;">
                                        <h3 class="box-title">รายละเอียดข้อมูลการลา ปีงบประมาณ @{{ cboYear }}</h3>
                                    </div>
                                    <div class="form-group col-sm-4" style="margin-bottom: 0px;">
                                        <select
                                            id="cboLeaveType"
                                            name="cboLeaveType"
                                            ng-model="cboLeaveType"
                                            ng-change="getPersonHistories({{ Auth::user()->person_id }});"
                                            class="form-control"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($leave_types as $type)

                                                <option value="{{ $type->id }}">
                                                    {{ $type->name }}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.row -->
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped" style="font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; text-align: center;">#</th>
                                            <th>ประเภทการลา</th>
                                            <th style="width: 30%; text-align: center;">วันที่ลา</th>
                                            <th style="width: 10%; text-align: center;">จน.วัน</th>
                                            <th style="width: 10%; text-align: center;">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    <tr ng-repeat="(index, leave) in leaves" ng-show="leaves.length > 0">
                                            <td style="text-align: center;">
                                                @{{ index+pager.from }}
                                            </td>
                                            <td style="text-align: left;">
                                                @{{ leave.type.name }}
                                                <p ng-show="leave.cancellation.length > 0" style="color: red;">
                                                    ยกเลิกวันลา <span>@{{ leave.cancellation[0].days }} วัน</span>
                                                </p>
                                            </td>
                                            <td style="text-align: center;">
                                                <span>@{{ leave.start_date | thdate }} - </span>
                                                <span>@{{ leave.end_date | thdate }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                @{{ leave.leave_days | currency:"":0 }}
                                                <p ng-show="leave.cancellation.length > 0" style="color: red;">
                                                    (ลา @{{ leave.leave_days - leave.cancellation[0].days }})
                                                </p>
                                            </td>
                                            <td style="text-align: left;">
                                                <span class="label label-primary" ng-show="leave.status == 0">
                                                    อยู่ระหว่างดำเนินการ
                                                </span>
                                                <span class="label label-info" ng-show="leave.status == 1">
                                                    หัวหน้าลงความเห็นแล้ว
                                                </span>
                                                <span class="label label-info" ng-show="leave.status == 2">
                                                    รับเอกสารแล้ว
                                                </span>
                                                <span class="label label-success" ng-show="leave.status == 3">
                                                    ผ่านการอนุมัติ
                                                </span>
                                                <span class="label label-default" ng-show="leave.status == 4">
                                                    ไม่ผ่านการอนุมัติ
                                                </span>
                                                <span class="label label-default" ng-show="leave.status == 7">
                                                    หัวหน้าไม่อนุญาต
                                                </span>
                                                <span class="label label-warning" ng-show="leave.status == 5">
                                                    อยู่ระหว่างการยกเลิก
                                                </span>
                                                <span class="label label-success" ng-show="leave.status == 8">
                                                    ยกเลิกวันลา (บางส่วน)
                                                </span>
                                                <span class="label label-danger" ng-show="leave.status == 9">
                                                    ยกเลิกวันลา (ทั้งหมด)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr ng-show="leaves.length == 0">
                                            <td style="text-align: center;" colspan="5">
                                                <span style="color: red;">-- ไม่มีข้อมูลการลา --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-4">
                                        <span style="margin-top: 5px;">
                                            หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                                        </span>
                                    </div>
                                    <div class="col-md-4" style="text-align: center;">
                                        จำนวน @{{ pager.total }} รายการ
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="pagination pagination-sm no-margin pull-right">
                                            <li ng-if="pager.current_page !== 1">
                                                <a href="#" ng-click="getDataWithURL($event, pager.path+ '?page=1', setLeaves)" aria-label="Previous">
                                                    <span aria-hidden="true">First</span>
                                                </a>
                                            </li>
                                        
                                            <li ng-class="{'disabled': (pager.current_page==1)}">
                                                <a href="#" ng-click="getDataWithURL($event, pager.prev_page_url, setLeaves)" aria-label="Prev">
                                                    <span aria-hidden="true">Prev</span>
                                                </a>
                                            </li>

                                            <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                                <a href="#" ng-click="getDataWithURL(pager.path + '?page=' +i)">
                                                    @{{ i }}
                                                </a>
                                            </li> -->

                                            <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                                <a href="#" ng-click="pager.path">
                                                    ...
                                                </a>
                                            </li> -->

                                            <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                                <a href="#" ng-click="getDataWithURL($event, pager.next_page_url, setLeaves)" aria-label="Next">
                                                    <span aria-hidden="true">Next</span>
                                                </a>
                                            </li>

                                            <li ng-if="pager.current_page !== pager.last_page">
                                                <a href="#" ng-click="getDataWithURL($event, pager.path+ '?page=' +pager.last_page, setLeaves)" aria-label="Previous">
                                                    <span aria-hidden="true">Last</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- /.box-footer -->

                            <!-- Loading (remove the following to stop the loading)-->
                            <div ng-show="loading" class="overlay">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <!-- end loading -->

                        </div><!-- /.box -->
                    </div>
                </div><!-- /.row -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            $('#cal_date').datepicker({
                autoclose: true,
                language: 'th',
                format: 'dd/mm/yyyy',
                thaiyear: true
            });
        });
    </script>

@endsection