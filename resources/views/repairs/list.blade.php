@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บันทึกขอจ้างซ่อม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">บันทึกขอจ้างซ่อม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="repairCtrl" ng-init="getAll();">

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
                                </div>
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
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>เลขที่บันทึกขอสนับสนุน</label>
                                    <input
                                        id="searchKey"
                                        name="searchKey"
                                        ng-model="searchKey"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>รายละเอียดการซ่อม</label>
                                    <input
                                        id="txtDesc"
                                        name="txtDesc"
                                        ng-model="txtDesc"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">บันทึกขอจ้างซ่อม</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/repairs/add') }}" class="btn btn-primary pull-right">
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
                                    <th style="width: 12%; text-align: center;">บันทึก</th>
                                    <th style="width: 15%;">หน่วยงาน</th>
                                    <th style="width: 5%; text-align: center;">ปีงบ</th>
                                    <th>รายการ</th>
                                    <th style="width: 10%; text-align: center;">ยอดขอสนับสนุน</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, support) in supports">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td>
                                        <p style="margin: 0;">เลขที่ @{{ support.doc_no }}</p>
                                        <p style="margin: 0;">วันที่ @{{ support.doc_date | thdate }}</p>
                                    </td>
                                    <td>
                                        @{{ support.depart.depart_name }}
                                        <p style="margin: 0;" ng-show="support.division">
                                            @{{ support.division.ward_name }}
                                        </p>
                                    </td>
                                    <td style="text-align: center;">@{{ support.year }}</td>
                                    <td>
                                        <ul style="margin: 0; padding: 0 0 0 15px;">
                                            <li ng-repeat="(index, detail) in support.details">
                                                <span>@{{ detail.plan.plan_no }} - @{{ detail.plan.plan_item.item.item_name }}</span>
                                                <p style="margin: 0; font-size: 12px; color: red;">
                                                    (@{{ detail.desc }}
                                                    จำนวน <span>@{{ detail.amount | currency:'':0 }}</span>
                                                    <span>@{{ detail.unit.name }}</span>
                                                    ราคา @{{ detail.price_per_unit | currency:'':0 }} บาท)
                                                </p>
                                            </li>
                                        </ul>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ support.total | currency:'':0 }} บาท
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="support.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-warning" ng-show="support.status == 1">
                                            ส่งเอกสารแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="support.status == 2">
                                            รับเอกสารแล้ว
                                        </span>
                                        <span class="label bg-maroon" ng-show="support.status == 3">
                                            ออกใบสั่งซื้อแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="support.status == 4">
                                            ตรวจรับแล้ว
                                        </span>
                                        <span class="label bg-teal" ng-show="support.status == 5">
                                            ส่งเบิกเงินแล้ว
                                        </span>
                                        <span class="label label-danger" ng-show="support.status == 9">
                                            เอกสารถูกตีกลับ
                                        </span>
                                        <span class="label label-danger" ng-show="support.status == 99">
                                            ยกเลิก
                                        </span>
                                        <p style="margin: 0; font-size: 12px;" ng-show="support.status == 1">
                                            (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.sent_date | thdate }})
                                        </p>
                                        <p style="margin: 0; font-size: 12px;" ng-show="support.status == 2">
                                            (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.received_date | thdate }})
                                        </p>
                                    </td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/repairs/detail') }}/@{{ support.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a  href="{{ url('/repairs/edit') }}/@{{ support.id }}"
                                            class="btn btn-warning btn-xs"
                                            ng-show="support.status == 0"
                                            title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form
                                            id="frmDelete"
                                            method="POST"
                                            action="{{ url('/repairs/delete') }}"
                                            style="display: inline;"
                                            ng-show="support.status == 0"
                                        >
                                            {{ csrf_field() }}
                                            <button
                                                type="submit"
                                                ng-click="delete($event, support.id)"
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
                                        <a href="#" ng-click="getRepairsWithUrl($event, pager.path+ '?page=1', setSupports)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getRepairsWithUrl($event, pager.prev_page_url, setSupports)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getRepairsWithUrl($event, pager.path + '?page=' +i, setSupports)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getRepairsWithUrl($event, pager.next_page_url, setSupports)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getRepairsWithUrl($event, pager.path+ '?page=' +pager.last_page, setSupports)" aria-label="Previous">
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
    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();
        });
    </script>

@endsection