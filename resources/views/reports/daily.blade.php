@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานสรุปผู้ลาประจำวัน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายงานสรุปผู้ลาประจำวัน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getDaily();
            initForm({ factions: {{ $factions }}, departs: {{ $departs }}, divisions: {{ $divisions }} });
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
                            <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->person_id }} == '1300200009261'">
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
                                </div><!-- /.form group -->
                            </div><!-- /.col -->

                            <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->person_id }} == '1300200009261'">
                                <div class="form-group">
                                    <label>กลุ่มงาน</label>
                                    <select
                                        id="depart"
                                        name="depart"
                                        ng-model="cboDepart"
                                        class="form-control select2"
                                        style="width: 100%; font-size: 12px;"
                                        ng-change="getDaily(); onSelectedDepart(cboDepart);"
                                    >
                                        <option value="" selected="selected">-- กรุณาเลือก --</option>
                                        <option
                                            ng-repeat="depart in filteredDeparts"
                                            value="@{{ depart.depart_id }}"
                                        >
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                            </div><!-- /.col -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>งาน</label>
                                    <select
                                        id="division"
                                        name="division"
                                        ng-model="cboDivision"
                                        class="form-control select2"
                                        style="width: 100%; font-size: 12px;"
                                        ng-change="getDaily()"
                                    >
                                        <option value="" selected="selected">-- กรุณาเลือก --</option>
                                        <option
                                            ng-repeat="division in filteredDivisions"
                                            value="@{{ division.ward_id }}"
                                        >
                                            @{{ division.ward_name }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                            </div><!-- /.col -->

                            <!-- // TODO: should use datepicker instead -->
                            <div class="form-group col-md-6">
                                <label>ประจำวันที่</label>
                                <input
                                    id="dtpDate"
                                    name="dtpDate"
                                    ng-model="dtpDate"
                                    class="form-control"
                                />
                            </div><!-- /.form group -->

                            <div class="col-md-12">                            
                                <div class="form-group">
                                    <label>ค้นหาชื่อบุคลากร</label>
                                    <input
                                        type="text"
                                        id="searchKeyword"
                                        name="searchKeyword"
                                        ng-model="searchKeyword"
                                        ng-keyup="getDaily()"
                                        class="form-control">
                                </div><!-- /.form group -->
                            </div>

                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายงานสรุปผู้ลาประจำวัน @{{ dtpDate }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th style="width: 15%;">ตำแหน่ง</th>
                                    <th style="width: 22%;">สังกัด</th>
                                    <th style="width: 12%; text-align: center;">ประเภทการลา</th>
                                    <th style="width: 15%; text-align: center;">ระหว่างวันที่</th>
                                    <th style="width: 5%; text-align: center;">มีกำหนด</th>
                                    <th
                                        style="width: 8%; text-align: center;"
                                        ng-show="{{ Auth::user()->person_id }} == '1300200009261'"
                                    >
                                        สถานะ
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, leave) in data">
                                    <td style="text-align: center;">@{{ pager.from + index }}</td>
                                    <td>
                                        @{{ leave.person.prefix.prefix_name + leave.person.person_firstname + ' ' + leave.person.person_lastname }}
                                    </td>
                                    <td>
                                        @{{ leave.person.position.position_name + leave.person.academic.ac_name }}
                                    </td>
                                    <td>
                                        <span ng-show="{{ Auth::user()->person_id }} == '1300200009261' ||
                                                    {{ Auth::user()->person_id }} == '1309900322504' ||
                                                    {{ Auth::user()->memberOf->duty_id }} == 1">
                                            @{{ leave.person.member_of.depart.depart_name }}
                                        </span>
                                        <span ng-show="{{ Auth::user()->memberOf->duty_id }} == 2">
                                            @{{ leave.person.member_of.division.ward_name }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">@{{ leave.type.name }}</td>
                                    <td style="text-align: center;">
                                        @{{ leave.start_date | thdate }} - @{{ leave.end_date | thdate }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ leave.leave_days }} วัน
                                    </td>
                                    <td style="text-align: center;" ng-show="{{ Auth::user()->person_id }} == '1300200009261'">
                                        <span ng-show="(leave.person.person_state == '1')">ปฏิบัติราชการ</span>
										<span ng-show="(leave.person.person_state == '2')">มาช่วยราชการ</span>
										<span ng-show="(leave.person.person_state == '3')">ไปช่วยราชการ</span>
										<span ng-show="(leave.person.person_state == '4')">ลาศึกษาต่อ</span>
										<span ng-show="(leave.person.person_state == '5')">เพิ่มพูนทักษะ</span>
										<span ng-show="(leave.person.person_state == '6')">ลาออก</span>
										<span ng-show="(leave.person.person_state == '7')">เกษียณอายุราชการ</span>
										<span ng-show="(leave.person.person_state == '8')">โอน/ย้าย</span>
										<span ng-show="(leave.person.person_state == '9')">ถูกให้ออก</span>
										<span ng-show="(leave.person.person_state == '99')">ไม่ทราบสถานะ</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
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