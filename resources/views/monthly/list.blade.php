@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการควบคุมกำกับติดตาม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการควบคุมกำกับติดตาม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="monthlyCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }}
            }, '');
        ">

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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>รายการ</label>
                                        <select
                                            id="cboExpense"
                                            name="cboExpense"
                                            ng-model="cboExpense"
                                            ng-change="getAll()"
                                            class="form-control"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($expenses as $expense)

                                                <option value="{{ $expense->id }}">
                                                    {{ $expense->name }}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.row -->

                            <div class="row" ng-show="{{ Auth::user()->person_id }} == '1300200009261'">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="onFactionSelected(cboFaction)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
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
                                        <label>กลุ่มงาน</label>
                                        <select
                                            id="cboDepart"
                                            name="cboDepart"
                                            ng-model="cboDepart"
                                            class="form-control select2"
                                            ng-change="getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการควบคุมกำกับติดตาม</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/monthly/add') }}" class="btn btn-primary pull-right">
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
                                    <th style="width: 10%; text-align: center;">ประจำเดือน</th>
                                    <th style="width: 15%; text-align: center;">ประเภท</th>
                                    <th>หน่วยงาน</th>
                                    <th style="width: 10%; text-align: center;">ยอดการใช้</th>
                                    <th style="width: 10%; text-align: center;">ยอดคงเหลือ</th>
                                    <!-- <th style="width: 8%; text-align: center;">สถานะ</th> -->
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ plan.month }}/@{{ plan.year }}</td>
                                    <td style="text-align: center;">
                                        @{{ plan.expense.name }}
                                    </td>
                                    <td>
                                        @{{ plan.depart.depart_name }}
                                    </td>
                                    <td style="text-align: center;">@{{ plan.total | currency:'':0 }}</td>
                                    <td style="text-align: center;">@{{ plan.remain | currency:'':0 }}</td>
                                    <!-- <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="plan.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-warning" ng-show="plan.status == 1">
                                            ส่งเอกสารแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="plan.status == 2">
                                            รับเอกสารแล้ว
                                        </span>
                                        <span class="label label-danger" ng-show="plan.status == 9">
                                            ยกเลิก
                                        </span>
                                    </td> -->
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/monthly/detail') }}/@{{ plan.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a  href="{{ url('/monthly/edit') }}/@{{ plan.id }}"
                                            class="btn btn-warning btn-xs"
                                            title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form
                                            id="frmDelete"
                                            method="POST"
                                            action="{{ url('/monthly/delete') }}"
                                            style="display: inline;"
                                        >
                                            {{ csrf_field() }}
                                            <button
                                                type="submit"
                                                ng-click="delete($event, plan.id)"
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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setMonthlys)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setMonthlys)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path + '?page=' +i, setMonthlys)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setMonthlys)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setMonthlys)" aria-label="Previous">
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