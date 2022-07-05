@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการเจ้าหนี้
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการเจ้าหนี้</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="supplierCtrl" ng-init="getAll()">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>
                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>ชื่อเจ้าหนี้</label>
                                    <input
                                        type="text"
                                        id="txtKeyword"
                                        name="txtKeyword"
                                        ng-model="txtKeyword"
                                        ng-keyup="getAll($event)"
                                        class="form-control">
                                </div>

                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ url('/suppliers/add') }}" class="btn btn-primary"> เพิ่มเจ้าหนี้</a>
                        </div>
                    </form>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายการเจ้าหนี้</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 5%; text-align: center;">รหัส</th>
                                    <th>เจ้าหนี้</th>
                                    <th style="width: 30%; text-align: center;">ที่อยู่</th>
                                    <th style="width: 12%; text-align: center;">โทรศัพท์</th>
                                    <th style="width: 12%; text-align: center;">เลขที่ใบกำกับภาษี</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, supplier) in suppliers">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td>@{{ supplier.supplier_id }}</td>
                                    <td>@{{ supplier.supplier_name }}</td>
                                    <td>
                                        <span ng-show="supplier.supplier_address1">
                                            @{{ supplier.supplier_address1+ ' ' +supplier.supplier_address2+ ' ' +supplier.supplier_address3 }}
                                        </span>
                                    </td>
                                    <td>@{{ supplier.supplier_phone }}</td>
                                    <td style="text-align: center;">@{{ supplier.supplier_taxid }}</td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/suppliers/detail') }}/@{{ plan.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a ng-click="edit(supplier.supplier_id)" class="btn btn-warning btn-xs">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        @if(Auth::user()->person_id == '1300200009261')

                                            <a ng-click="delete(supplier.supplier_id)" class="btn btn-danger btn-xs">
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
                                        <a ng-click="getSuppliersWithUrl($event, pager.path+ '?page=1')" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getSuppliersWithUrl($event, pager.prev_page_url)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getSuppliersWithUrl($event, pager.next_page_url)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getSuppliersWithUrl($event, pager.path+ '?page=' +pager.last_page)" aria-label="Previous">
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