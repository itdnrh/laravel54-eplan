@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานสรุปการลา
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายงานสรุปการลา</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getSummary();
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
                                        ng-change="getSummary(); onSelectedDepart(cboDepart);"
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
                                        ng-change="getSummary()"
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
                                <label>ปีงบประมาณ</label>
                                <select
                                    id="dtpYear"
                                    name="dtpYear"
                                    ng-model="dtpYear"
                                    class="form-control"
                                    ng-change="getSummary()"
                                >
                                    <option value="">-- ทั้งหมด --</option>
                                    <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                        @{{ y }}
                                    </option>
                                </select>
                            </div><!-- /.form group -->

                            <div class="col-md-12">                            
                                <div class="form-group">
                                    <label>ค้นหาชื่อบุคลากร</label>
                                    <input
                                        type="text"
                                        id="searchKeyword"
                                        name="searchKeyword"
                                        ng-model="searchKeyword"
                                        ng-keyup="getSummary()"
                                        class="form-control">
                                </div><!-- /.form group -->
                            </div>

                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <h3 class="box-title">รายงานสรุปการลา ปีงบประมาณ @{{ dtpYear }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;" rowspan="2">#</th>
                                    <th style="text-align: left;" rowspan="2">ชื่อ-สกุล</th>
                                    <th style="width: 20%; text-align: center;" rowspan="2">ตำแหน่ง</th>
                                    <th style="text-align: center;" colspan="2">ลาป่วย</th>
                                    <th style="text-align: center;" colspan="2">ลากิจ</th>
                                    <th style="text-align: center;" colspan="2">ลาคลอด</th>
                                    <th style="text-align: center;" colspan="2">ลาพักผ่อน</th>
                                    <th style="text-align: center;" colspan="2">
                                        ลาเพื่อดูแลบุตร<br>และภริยาที่คลอดบุตร
                                    </th>
                                    <th style="text-align: center;" colspan="2">
                                        ลาอุปสมบท/<br>ประกอบพิธีฮัจย์
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width: 4%; text-align: center;">ครั้ง</th>
                                    <th style="width: 4%; text-align: center;">วัน</th>
                                    <th style="width: 4%; text-align: center;">ครั้ง</th>
                                    <th style="width: 4%; text-align: center;">วัน</th>
                                    <th style="width: 4%; text-align: center;">ครั้ง</th>
                                    <th style="width: 4%; text-align: center;">วัน</th>
                                    <th style="width: 4%; text-align: center;">ครั้ง</th>
                                    <th style="width: 4%; text-align: center;">วัน</th>
                                    <th style="width: 5%; text-align: center;">ครั้ง</th>
                                    <th style="width: 5%; text-align: center;">วัน</th>
                                    <th style="width: 4%; text-align: center;">ครั้ง</th>
                                    <th style="width: 4%; text-align: center;">วัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, person) in data">
                                    <td style="text-align: center;">@{{ pager.from + index }}</td>
                                    <td>
                                        @{{ person.prefix.prefix_name + person.person_firstname + ' ' + person.person_lastname }}
                                    </td>
                                    <td>
                                        @{{ person.position.position_name + person.academic.ac_name }}
                                    </td>
                                    <td style="text-align: center;">@{{ person.leave.ill_times }}</td>
                                    <td style="text-align: center;">@{{ person.leave.ill_days }}</td>
                                    <td style="text-align: center;">@{{ person.leave.per_times }}</td>
                                    <td style="text-align: center;">@{{ person.leave.per_days }}</td>
                                    <td style="text-align: center;">@{{ person.person_sex == '1' ? '-' : person.leave.lab_times }}</td>
                                    <td style="text-align: center;">@{{ person.person_sex == '1' ? '-' : person.leave.lab_days }}</td>
                                    <td style="text-align: center;">@{{ person.leave.vac_times }}</td>
                                    <td style="text-align: center;">@{{ person.leave.vac_days }}</td>
                                    <td style="text-align: center;">@{{ person.person_sex == '2' ? '-' : person.leave.hel_times }}</td>
                                    <td style="text-align: center;">@{{ person.person_sex == '2' ? '-' : person.leave.hel_days }}</td>
                                    <td style="text-align: center;">@{{ person.leave.ord_times }}</td>
                                    <td style="text-align: center;">@{{ person.leave.ord_days }}</td>
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