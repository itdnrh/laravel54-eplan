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
        ng-init="getPersons(); initValues({ departs: {{ $departs }}, divisions: {{ $divisions }} });"
    >
        <!-- Main row -->
        <div class="row">
            <section class="col-lg-12 connectedSortable">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form action="" method="POST" class="form-inline">
                        <div class="box-body">
                            <div class="form-group">
                                <label>กลุ่มงาน :</label>
                                <select
                                    class="form-control mr-2 ml-2"
                                    id="cboDepart"
                                    name="cboDepart"								
                                    ng-model="cboDepart"								
                                    ng-change="onDepartChange($event)"
                                >
                                    <option value="">-- เลือกกลุ่มงาน --</option>
                                    <option ng-repeat="dep in departs" value="@{{ dep.depart_id }}">
                                        @{{ dep.depart_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>งาน :</label>
                                <select
                                    class="form-control mr-2 ml-2"
                                    id="cboDivision"
                                    name="cboDivision"								
                                    ng-model="cboDivision"								
                                    ng-change="onDivisionChange($event)"
                                >
                                    <option value="">-- เลือกงาน --</option>
                                    <option ng-repeat="div in divisions" value="@{{ div.ward_id }}">
                                        @{{ div.ward_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>ชื่อ :</label>
                                <input
                                    class="form-control mr-2 ml-2"
                                    id="searchFname"
                                    name="searchFname"								
                                    ng-model="searchFname"								
                                    ng-change="onFnameChange($event)"
                                />
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายการบุคลากร</h3>
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
                                    <th style="text-align: center;">ชื่อ-สกุล</th>
                                    <!-- <th style="width: 15%; text-align: center;">จ.18</th> -->
                                    <th style="width: 7%; text-align: center;">ว/ด/ป เกิด</th>
                                    <th style="width: 6%; text-align: center;">อายุ</th>
                                    <th style="width: 20%; text-align: center;">กลุ่มงาน</th>
                                    <th style="width: 7%; text-align: center;">ว/ด/ป บรรจุ</th>
                                    <th style="width: 5%; text-align: center;">อายุงาน</th>
                                    <th style="width: 8%; text-align: center;">ประเภทตำแหน่ง</th>
                                    <th style="width: 8%; text-align: center;">ตำแหน่ง</th>
                                    <th style="width: 8%; text-align: center;">ระดับ</th>
                                    <th style="width: 8%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, row) in persons">	
                                    <td style="text-align: center;">@{{ pager.from + index }}</td>
                                    <td>
                                        @{{ row.prefix.prefix_name+row.person_firstname+ ' ' +row.person_lastname }}
                                    </td>
                                    <!-- <td style="text-align: center;">@{{ row.hosppay18.name }}</td> -->
                                    <td style="text-align: center;">@{{ row.person_birth | thdate }}</td>
                                    <td style="text-align: center;">@{{ row.ageY+ 'ป ' +row.ageM+ 'ด' }}</td>
                                    <td style="text-align: center;">
                                        <span ng-show="row.member_of.duty_id === '1'">
                                            หัวหน้ากลุ่มภารกิจ
                                        </span>
                                        <span ng-show="row.member_of.duty_id !== '1'">
                                            <span ng-show="row.member_of.duty_id === '2'">
                                                หัวหน้า
                                            </span>
                                            @{{ row.member_of.depart.depart_name }}<br />
                                            <span ng-show="row.member_of.duty_id != '1' && row.member_of.duty_id != '2'">
                                                (@{{ row.member_of.division.ward_name }})
                                            </span>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">@{{ row.person_singin | thdate }}</td>
                                    <td style="text-align: center;">@{{ row.level+ 'ปี' }}</td>
                                    <td style="text-align: center;">@{{ row.typeposition.typeposition_name }}</td>
                                    <td style="text-align: center;">@{{ row.position.position_name }}</td>
                                    <td style="text-align: center;">@{{ row.academic.ac_name }}</td>
                                    <td style="text-align: center;">
                                        <span ng-show="(row.person_state === '1')">ปฏิบัติราชการ</span>
                                        <span ng-show="(row.person_state === '2')">มาช่วยราชการ</span>
                                        <span ng-show="(row.person_state === '3')">ไปช่วยราชการ</span>
                                        <span ng-show="(row.person_state === '4')">ลาศึกษาต่อ</span>
                                        <span ng-show="(row.person_state === '5')">เพิ่มพูนทักษะ</span>
                                        <span ng-show="(row.person_state === '6')">ลาออก</span>
                                        <span ng-show="(row.person_state === '7')">เกษียณอายุราชการ</span>
                                        <span ng-show="(row.person_state === '8')">โอน/ย้าย</span>
                                        <span ng-show="(row.person_state === '9')">ถูกให้ออก</span>
                                        <span ng-show="(row.person_state === '99')">ไม่ทราบสถานะ</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <!-- <div class="btn-group" role="group">
                                                <button id="btnGroupDrop1" type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                                    <a class="dropdown-item" href="#" ng-click="showMoveForm($event, 'S', row)">
                                                        ย้ายภายใน ก.ภารกิจ
                                                    </a>
                                                    <a class="dropdown-item" href="#" ng-click="showMoveForm($event, 'M', row)">
                                                        ย้ายออกภายใน รพ.
                                                    </a>
                                                    <a class="dropdown-item" href="#" ng-click="showTransferForm($event, row)">
                                                        โอน/ย้ายออก (ภายนอก)
                                                    </a>
                                                    <a class="dropdown-item" href="#">ลาศึกษาต่อ</a>
                                                    <a class="dropdown-item" href="#" ng-click="showLeaveForm($event, row)">
                                                        ออก
                                                    </a>
                                                    <a class="dropdown-item" href="#" ng-click="unknown($event, row.person_id)">
                                                        ไม่ทราบสถานะ
                                                    </a>
                                                </div>
                                            </div> -->
                                            <a  href="{{ url('/persons/detail') }}/@{{ row.person_id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(leave.id)"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/persons/delete') }}"
                                            >
                                                <input type="hidden" id="id" name="id" value="@{{ leave.id }}" />
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, leave.id)"
                                                    class="btn btn-danger btn-xs"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div><!-- /.card-body -->
                    <div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-3 float-left" ng-show="persons.length > 0">
                                <a href="#" class="btn btn-success">Excel</a>
                            </div>
                            
                            <div class="col-md-6" ng-show="persons.length > 0" style="text-align: center; padding-top: 5px;">
                                <span>จำนวนทั้งหมด @{{ pager.total }} ราย</span>
                            </div>
                            
                            <div class="col-md-3" ng-show="persons.length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager">
                                    <li class="page-item" ng-class="{disabled: pager.current_page==1}">
                                        <a class="page-link" href="#" ng-click="getDataWithURL($event, pager.path + '?page=1')">
                                            First
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==1}">
                                        <a class="page-link" href="#" ng-click="getDataWithURL($event, pager.prev_page_url)">
                                            Prev
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==pager.last_page}">
                                        <a class="page-link" href="#" ng-click="getDataWithURL($event, pager.next_page_url)">
                                            Next
                                        </a>
                                    </li>
                                    <li class="page-item" ng-class="{disabled: pager.current_page==pager.last_page}">
                                        <a class="page-link" href="#" ng-click="getDataWithURL($event, pager.path + '?page=' +pager.last_page)">
                                            Last
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-footer -->
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
