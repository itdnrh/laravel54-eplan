@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการบุคลากร
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการบุคลากร</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="personCtrl"
        ng-init="
            getPersons();
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }}
            }, 0);
        "
    >
        <!-- Main row -->
        <div class="row">
            <section class="col-lg-12 connectedSortable">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form action="" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>กลุ่มภารกิจ :</label>
                                    <select
                                        class="form-control mr-2 ml-2"
                                        id="cboFaction"
                                        name="cboFaction"								
                                        ng-model="cboFaction"								
                                        ng-change="onFactionSelected(cboFaction); getPersons();"
                                    >
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>กลุ่มงาน :</label>
                                    <select
                                        class="form-control mr-2 ml-2"
                                        id="cboDepart"
                                        name="cboDepart"								
                                        ng-model="cboDepart"								
                                        ng-change="onDepartSelected(cboDepart); getPersons();"
                                    >
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                            @{{ dep.depart_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>งาน :</label>
                                    <select
                                        class="form-control mr-2 ml-2"
                                        id="cboDivision"
                                        name="cboDivision"								
                                        ng-model="cboDivision"								
                                        ng-change="getPersons();"
                                    >
                                        <option value="">-- เลือกงาน --</option>
                                        <option ng-repeat="div in forms.divisions" value="@{{ div.ward_id }}">
                                            @{{ div.ward_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>ชื่อ :</label>
                                    <input
                                        class="form-control mr-2 ml-2"
                                        id="keyword"
                                        name="keyword"
                                        ng-model="keyword"
                                        ng-change="getPersons();"
                                    />
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>สถานะ :</label>
                                    <select
                                        class="form-control mr-2 ml-2"
                                        id="cboStatus"
                                        name="cboStatus"								
                                        ng-model="cboStatus"								
                                        ng-change="getPersons();"
                                    >
                                        <option value="">ทั้งหมด</option>
                                        <option value="1">ปฏิบัติราชการ</option>
                                        <option value="2">มาช่วยราชการ</option>
                                        <option value="3">ไปช่วยราชการ</option>
                                        <option value="4">ลาศึกษาต่อ</option>
                                        <option value="5">เพิ่มพูนทักษะ</option>
                                        <option value="6">ลาออก</option>
                                        <option value="7">เกษียณอายุราชการ</option>
                                        <option value="8">โอน/ย้าย (ออก)</option>
                                        <option value="9">ถูกให้ออก</option>
                                        <option value="99">ไม่ทราบสถานะ</option>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการบุคลากร</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/persons/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มบุคลากร
                                </a>
                                <a href="{{ url('/duties/list') }}" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    กำหนดหัวหน้าหน่วยงาน
                                </a>
                                <a href="{{ url('/delegations/list') }}" class="btn bg-maroon pull-right" style="margin-right: 5px;">
                                    ระบุผู้ปฏิบัติงานแทน
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

                        <table class="table table-bordered table-striped" style="font-size: 14px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">ลำดับ</th>
                                    <th>ชื่อ-สกุล</th>
                                    <!-- <th style="width: 15%; text-align: center;">จ.18</th> -->
                                    <!-- <th style="width: 7%; text-align: center;">ว/ด/ป เกิด</th> -->
                                    <!-- <th style="width: 6%; text-align: center;">อายุ</th> -->
                                    <th style="width: 7%; text-align: center;">ว/ด/ป บรรจุ</th>
                                    <th style="width: 5%; text-align: center;">อายุงาน</th>
                                    <th style="width: 10%; text-align: center;">ประเภทตำแหน่ง</th>
                                    <th style="width: 20%; text-align: center;">ตำแหน่ง</th>
                                    <th style="width: 20%; text-align: center;">สังกัด</th>
                                    <th style="width: 8%; text-align: center;">สถานะ</th>
                                    <th style="width: 6%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, row) in persons">	
                                    <td style="text-align: center;">@{{ pager.from + index }}</td>
                                    <td>
                                        @{{ row.prefix.prefix_name+row.person_firstname+ ' ' +row.person_lastname }}
                                    </td>
                                    <!-- <td style="text-align: center;">@{{ row.hosppay18.name }}</td> -->
                                    <!-- <td style="text-align: center;">@{{ row.person_birth | thdate }}</td> -->
                                    <!-- <td style="text-align: center;">@{{ row.ageY+ 'ป ' +row.ageM+ 'ด' }}</td> -->
                                    <td style="text-align: center;">@{{ row.person_singin | thdate }}</td>
                                    <td style="text-align: center;">@{{ calcAge(row.person_singin, "years") }}ปี</td>
                                    <td style="text-align: center;">@{{ row.typeposition.typeposition_name }}</td>
                                    <td style="text-align: center;">
                                        @{{ row.position.position_name }}@{{ row.academic.ac_name }}
                                    </td>
                                    <td style="text-align: center;" ng-show="row.duty_of.length == 1">
                                        <span ng-show="row.member_of.duty_id == 1">
                                            หัวหน้ากลุ่มภารกิจ
                                        </span>
                                        <span ng-show="row.member_of.duty_id != 1">
                                            <span ng-show="row.member_of.duty_id == 2">หัวหน้า</span>@{{ row.member_of.depart.depart_name }}<br />
                                            <span ng-show="row.member_of.duty_id != 1 && row.member_of.duty_id != 2 && row.member_of.ward_id != 0">
                                                (@{{ row.member_of.division.ward_name }})
                                            </span>
                                        </span>
                                    </td>
                                    <td style="text-align: center;" ng-show="row.duty_of.length > 1">
                                        <span ng-repeat="duty in row.duty_of">
                                            <span ng-show="duty.duty_id == 1">
                                                หัวหน้ากลุ่มภารกิจ และ
                                            </span>
                                            <span ng-show="duty.duty_id != 1">
                                                <span ng-show="duty.duty_id == 2">หัวหน้า</span>@{{ duty.depart.depart_name }}<br />
                                                <span ng-show="duty.duty_id != 1 && duty.duty_id != 2 && duty.ward_id != 0">
                                                    (@{{ duty.division.ward_name }})
                                                </span>
                                            </span>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="label label-success" ng-show="(row.person_state == 1)">
                                            ปฏิบัติราชการ
                                        </span>
                                        <span class="label bg-olive" ng-show="(row.person_state == 2)">
                                            มาช่วยราชการ
                                        </span>
                                        <span class="label bg-maroon" ng-show="(row.person_state == 3)">
                                            ไปช่วยราชการ
                                        </span>
                                        <span class="label bg-navy" ng-show="(row.person_state == 4)">
                                            ลาศึกษาต่อ
                                        </span>
                                        <span class="label bg-purple" ng-show="(row.person_state == 5)">
                                            เพิ่มพูนทักษะ
                                        </span>
                                        <span class="label label-danger" ng-show="(row.person_state == 6)">
                                            ลาออก
                                        </span>
                                        <span class="label label-warning" ng-show="(row.person_state == 7)">
                                            เกษียณอายุราชการ
                                        </span>
                                        <span class="label label-primary" ng-show="(row.person_state == 8)">
                                            โอน/ย้าย
                                        </span>
                                        <span class="label label-danger" ng-show="(row.person_state == 9)">
                                            ถูกให้ออก
                                        </span>
                                        <span class="label label-default" ng-show="(row.person_state == 99)">
                                            ไม่ทราบสถานะ
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/persons/detail') }}/@{{ row.person_id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                                รายละเอียด
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Loading (remove the following to stop the loading)-->
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->

                    </div><!-- /.card-body -->
                    <div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4" ng-show="persons.length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager">
                                    <li class="page-item" ng-class="{disabled: pager.current_page==1}">
                                        <a class="page-link" href="#" ng-click="getPersonsWithUrl($event, pager.path + '?page=1', setPersons)">
                                            First
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==1}">
                                        <a class="page-link" href="#" ng-click="getPersonsWithUrl($event, pager.prev_page_url, setPersons)">
                                            Prev
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==pager.last_page}">
                                        <a class="page-link" href="#" ng-click="getPersonsWithUrl($event, pager.next_page_url, setPersons)">
                                            Next
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==pager.last_page}">
                                        <a class="page-link" href="#" ng-click="getPersonsWithUrl($event, pager.path + '?page=' +pager.last_page, setPersons)">
                                            Last
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

            </section>
        </div><!-- Main row -->
    </section><!-- /.content -->

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Date range picker with time picker
            $('#debtDate').daterangepicker({
                timePickerIncrement: 30,
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: " , ",
                }
            }, function(e) {
                console.log(e);
            });
        });
    </script>

@endsection
