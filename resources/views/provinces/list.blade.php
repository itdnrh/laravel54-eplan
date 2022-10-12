@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            คำสั่งจังหวัด
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">คำสั่งจังหวัด</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="provinceCtrl" ng-init="getProvinces()">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>ปี</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getProvinces($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>เลขที่คำสั่ง</label>
                                        <input
                                            type="text"
                                            id="txtKeyword"
                                            name="txtKeyword"
                                            ng-model="txtKeyword"
                                            ng-keyup="getProvinces($event)"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-6">
                                <h3 class="box-title">คำสั่งจังหวัด</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/provinces/add') }}" class="btn btn-primary pull-right">
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
                                    <th style="width: 8%; text-align: center;">ปี</th>
                                    <th style="width: 12%; text-align: center;">เลขที่คำสั่ง</th>
                                    <th style="width: 12%; text-align: center;">วันที่</th>
                                    <th>รายละเอียด</th>
                                    <th style="width: 8%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, province) in provinces">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ province.year }}</td>
                                    <td style="text-align: center;">@{{ province.order_no }}</td>
                                    <td style="text-align: center;">@{{ province.order_date | thdate }}</td>
                                    <td>@{{ province.detail }}</td>
                                    <td style="text-align: center;">
                                        <i class="fa fa-check-circle-o text-success" aria-hidden="true" ng-show="province.is_activated == 1"></i>
                                        <i class="fa fa-times-circle-o text-danger" aria-hidden="true" ng-show="province.is_activated != 1"></i>
                                    </td>
                                    <td style="text-align: center;">
                                        <!-- <a  href="{{ url('/provinces/detail') }}/@{{ province.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a> -->
                                        <a ng-click="edit(province.id)" class="btn btn-warning btn-xs">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if(Auth::user()->person_id == '1300200009261')
                                            <a ng-click="delete(province.id)" class="btn btn-danger btn-xs">
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
                                        <a ng-click="getProvincesWithUrl($event, pager.path+ '?page=1', setProvinces)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getProvincesWithUrl($event, pager.prev_page_url, setProvinces)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getProvincesWithUrl($event, pager.next_page_url, setProvinces)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getProvincesWithUrl($event, pager.path+ '?page=' +pager.last_page, setProvinces)" aria-label="Previous">
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