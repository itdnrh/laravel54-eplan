@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            กลุ่มภารกิจ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">กลุ่มภารกิจ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="factionCtrl" ng-init="getFactions()">
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
                                        <label>ชื่อเจ้าหนี้</label>
                                        <input
                                            type="text"
                                            id="txtKeyword"
                                            name="txtKeyword"
                                            ng-model="txtKeyword"
                                            ng-keyup="getSuppliers($event)"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>จังหวัด</label>
                                        <select
                                            id="cboChangwat"
                                            name="cboChangwat"
                                            ng-model="cboChangwat"
                                            ng-keyup="getSuppliers($event)"
                                            class="form-control"
                                        >

                                        </select>
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
                                <h3 class="box-title">กลุ่มภารกิจ</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/suppliers/add') }}" class="btn btn-primary pull-right">
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
                                    <th>ชื่อกลุ่มภารกิจ</th>
                                    <th style="width: 10%; text-align: center;">จน.กลุ่มงาน</th>
                                    <th style="width: 6%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, faction) in factions">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ faction.faction_id }}</td>
                                    <td>@{{ faction.faction_name }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ url('departs/list?faction=') }}@{{ faction.faction_id }}">
                                            @{{ faction.departs.length }} กลุ่มงาน
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        <i class="fa fa-circle text-success" aria-hidden="true" ng-show="faction.is_actived == '1'"></i>
                                        <i class="fa fa-circle text-danger" aria-hidden="true" ng-show="faction.is_actived == '0'"></i>
                                    </td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/factions/detail') }}/@{{ faction.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a ng-click="edit(faction.faction_id)" class="btn btn-warning btn-xs">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if(Auth::user()->person_id == '1300200009261')
                                            <a ng-click="delete(faction.faction_id)" class="btn btn-danger btn-xs">
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
                                        <a ng-click="getSuppliersWithUrl($event, pager.path+ '?page=1', setSuppliers)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getSuppliersWithUrl($event, pager.prev_page_url, setSuppliers)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getSuppliersWithUrl($event, pager.next_page_url, setSuppliers)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getSuppliersWithUrl($event, pager.path+ '?page=' +pager.last_page, setSuppliers)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
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