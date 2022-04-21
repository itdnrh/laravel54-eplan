@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">บันทึกขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="supportCtrl" ng-init="getAll();">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="depart"
                            name="depart"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                        />

                        <div class="box-body">
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                                <div class="form-group col-md-6">
                                    <label>ประเภทพัสดุ</label>
                                    <select
                                        id="cboSupplier"
                                        name="cboSupplier"
                                        ng-model="cboSupplier"
                                        ng-change="getAll($event)"
                                        class="form-control select2"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div><!-- /.form group -->
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">บันทึกขอสนับสนุน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/supports/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" style="font-size: 14px; margin: 10px auto;">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">#</th>
                                    <th style="width: 12%; text-align: center;">เลขที่บันทึก</th>
                                    <th style="width: 8%; text-align: center;">วันที่บันทึก</th>
                                    <th style="width: 10%; text-align: center;">ประเภทพัสดุ</th>
                                    <th>หน่วยงาน</th>
                                    <th style="width: 8%; text-align: center;">ปีงบ</th>
                                    <th style="width: 8%; text-align: center;">จำนวนรายการ</th>
                                    <th style="width: 8%; text-align: center;">ยอดขอสนับสนุน</th>
                                    <!-- <th style="width: 5%; text-align: center;">ไฟล์แนบ</th> -->
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, support) in supports">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ support.doc_no }}</td>
                                    <td style="text-align: center;">@{{ support.doc_date | thdate }}</td>
                                    <td style="text-align: center;">
                                        @{{ support.plan_type.plan_type_name }}
                                    </td>
                                    <td>
                                        @{{ support.depart.depart_name }}
                                        <p style="margin: 0;" ng-show="support.division">
                                            @{{ support.division.ward_name }}
                                        </p>
                                    </td>
                                    <td style="text-align: center;">@{{ support.year }}</td>
                                    <td style="text-align: center;">
                                        @{{ support.details.length }}
                                        <a  href="#"
                                            ng-click="showSupportDetails(support.details)"
                                            class="btn btn-default btn-xs" 
                                            title="รายการ">
                                            <i class="fa fa-clone"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;">@{{ support.total | currency:'':0 }}</td>
                                    <!-- <td style="text-align: center;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ support.attachment }}"
                                            class="btn btn-default btn-xs"
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="order.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a>
                                    </td> -->
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/orders/detail') }}/@{{ order.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a  href="{{ url('/orders/edit') }}/@{{ order.id }}"
                                            class="btn btn-warning btn-xs"
                                            title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form
                                            id="frmDelete"
                                            method="POST"
                                            action="{{ url('/orders/delete') }}"
                                            style="display: inline;"
                                        >
                                            {{ csrf_field() }}
                                            <button
                                                type="submit"
                                                ng-click="delete($event, order.id)"
                                                class="btn btn-danger btn-xs"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setSupports)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setSupports)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path + '?page=' +i, setSupports)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setSupports)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setSupports)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('supports._support-details')

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();
        });
    </script>

@endsection