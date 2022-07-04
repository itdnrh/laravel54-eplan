@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายจ่าย
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายจ่าย</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="expenseCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }},
                expenseTypes: {{ $expenseTypes }}
            }, 1);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
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
                                    <label>ประเภทรายจ่าย</label>
                                    <select
                                        id="cboExpenseType"
                                        name="cboExpenseType"
                                        class="form-control"
                                        ng-model="cboExpenseType"
                                        ng-change="onPlanTypeSelected(cboExpenseType); getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($expenseTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>ชื่อรายจ่าย</label>
                                    <input
                                        id="txtExpenseName"
                                        name="txtExpenseName"
                                        class="form-control"
                                        ng-model="txtExpenseName"
                                        ng-keyup="getAll($event)"
                                    />
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-6">
                                <h3 class="box-title">รายจ่าย</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/expenses/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
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

                        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 20%; text-align: center;">ประเภทรายจ่าย</th>
                                    <th>รายจ่าย</th>
                                    <th style="width: 20%;">หน่วยงาน</th>
                                    <th style="width: 10%; text-align: center;">หมายเหตุ</th>
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, expense) in expenses">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ expense.expense_type.name }}</td>
                                    <td>@{{ expense.name }}</td>
                                    <td>@{{ expense.depart.depart_name }}</td>
                                    <!-- <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="expense.status == 0">
                                            อยู่ระหว่างดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="expense.status == 1">
                                            ส่งเอกสารแล้ว
                                        </span>
                                        <span class="label bg-navy" ng-show="expense.status == 2">
                                            รับเอกสารแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="expense.status == 3">
                                            ออกใบสั้งซื้อแล้ว
                                        </span>
                                        <span class="label bg-maroon" ng-show="expense.status == 4">
                                            ตรวจรับแล้ว
                                        </span>
                                        <span class="label label-warning" ng-show="expense.status == 5">
                                            ส่งเบิกเงินแล้ว
                                        </span>
                                        <span class="label label-danger" ng-show="expense.status == 6">
                                            ตั้งหนี้แล้ว
                                        </span>
                                        <span class="label label-default" ng-show="expense.status == 9">
                                            ยกเลิก
                                        </span>
                                    </td> -->
                                    <td style="text-align: center; font-size: 12px;">
                                        @{{ expense.remark }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/expenses/detail') }}/@{{ expense.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(expense.id)"
                                                ng-show="!expense.approved"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/expenses/delete') }}"
                                                ng-show="!expense.approved"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, expense.id)"
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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setExpenses)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setExpenses)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setExpenses)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setExpenses)" aria-label="Previous">
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
            $('.select2').select2()
        });
    </script>

@endsection