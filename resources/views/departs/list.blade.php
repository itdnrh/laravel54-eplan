@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            กลุ่มงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">กลุ่มงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="departCtrl" ng-init="setFaction({{ $faction }}); getDeparts();">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            ng-change="getDeparts($event)"
                                            class="form-control"
                                        >
                                            <option value="">-- กรุณาเลือก --</option>
                                            @foreach($factions as $faction)
                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ชื่อกลุ่มงาน</label>
                                        <input
                                            type="text"
                                            id="txtKeyword"
                                            name="txtKeyword"
                                            ng-model="txtKeyword"
                                            ng-keyup="getDeparts($event)"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">กลุ่มงาน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/departs/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 8%; text-align: center;">รหัส</th>
                                    <th>ชื่อกลุ่มงาน</th>
                                    <th style="width: 10%; text-align: center;">เลขหนังสือออก</th>
                                    <th style="width: 10%; text-align: center;">เบอร์ภายใน</th>
                                    <th style="width: 10%; text-align: center;">จน.หน่วยงาน</th>
                                    <th style="width: 6%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, depart) in departs">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ depart.depart_id }}</td>
                                    <td>@{{ depart.depart_name }}</td>
                                    <td style="text-align: center;">@{{ depart.memo_no }}</td>
                                    <td style="text-align: center;">@{{ depart.tel_no }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ url('divisions/list') }}?faction=@{{ depart.faction_id }}&depart=@{{ depart.depart_id }}">
                                            @{{ depart.divisions.length }} หน่วยงาน
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        <a
                                            href="#"
                                            ng-click="active($event, depart.depart_id, !depart.is_actived)"
                                            ng-show="depart.is_actived == '1'"
                                        >
                                            <i class="fa fa-circle text-success" aria-hidden="true"></i>
                                        </a>
                                        <a 
                                            href="#"
                                            ng-click="active($event, depart.depart_id, !depart.is_actived)"
                                            ng-show="depart.is_actived != '1'"
                                        >
                                            <i class="fa fa-circle text-danger" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        <!-- <a  href="{{ url('/departs/detail') }}/@{{ depart.depart_id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a> -->
                                        <a  href="{{ url('/departs/edit') }}/@{{ depart.depart_id }}"
                                            class="btn btn-warning btn-xs" 
                                            title="แก้ไข">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if(Auth::user()->person_id == '1300200009261')
                                            <a ng-click="delete(depart.depart_id)" class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endif
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
                                จำนวน @{{ pager.total | currency:'':0 }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right">
                                    <li ng-if="pager.current_page !== 1">
                                        <a ng-click="getDepartsWithUrl($event, pager.path+ '?page=1', getDeparts)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getDepartsWithUrl($event, pager.prev_page_url, getDeparts)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getDepartsWithUrl($event, pager.next_page_url, getDeparts)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getDepartsWithUrl($event, pager.path+ '?page=' +pager.last_page, getDeparts)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-footer -->

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