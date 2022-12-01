@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ประมาณการรายจ่าย
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ประมาณการรายจ่าย</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="budgetCtrl"
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
                                        ng-change="getAll()"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ประเภทรายจ่าย</label>
                                        <select
                                            id="cboExpenseType"
                                            name="cboExpenseType"
                                            ng-model="cboExpenseType"
                                            ng-change="getAll()"
                                            class="form-control"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($expenseTypes as $expenseType)
                                                <option value="{{ $expenseType->id }}">
                                                    {{ $expenseType->name }}
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
                                            ng-change="getAll()"
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
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">ประมาณการรายจ่าย</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/budgets/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div ng-repeat="expenseType in expenseTypes">
                            <h4 ng-show="expenseType.budgets.length > 0">@{{ expenseType.name }}</h4>
                            <table
                                class="table table-bordered table-striped"
                                style="font-size: 14px; margin: 10px auto;"
                                ng-show="expenseType.budgets.length > 0"
                            >
                                <thead>
                                    <tr>
                                        <th style="width: 4%; text-align: center;">#</th>
                                        <th style="width: 10%; text-align: center;">ปีงบประมาณ</th>
                                        <th>ประเภท</th>
                                        <th style="width: 12%; text-align: center;">ยอดประมาณการ</th>
                                        <th style="width: 12%; text-align: center;">ยอดคงเหลือ</th>
                                        <th style="width: 20%;">หน่วยงาน</th>
                                        <th style="width: 8%; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="(index, budget) in expenseType.budgets">
                                        <td style="text-align: center;">@{{ index+1 }}</td>
                                        <td style="text-align: center;">@{{ budget.year }}</td>
                                        <td>@{{ budget.expense.name }}</td>
                                        <td style="text-align: right;">@{{ budget.budget | currency:'':2 }}</td>
                                        <td style="text-align: right;">@{{ budget.remain | currency:'':2 }}</td>
                                        <td>@{{ budget.depart.depart_name }}</td>
                                        <td style="text-align: center;">
                                            <a  href="{{ url('/budgets/detail') }}/@{{ budget.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  href="{{ url('/budgets/edit') }}/@{{ budget.id }}"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/budgets/delete') }}"
                                                style="display: inline;"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, budget.id)"
                                                    class="btn btn-danger btn-xs"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>             
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row" ng-show="false">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getBudgetsWithUrl($event, pager.path+ '?page=1', setBudgets)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getBudgetsWithUrl($event, pager.prev_page_url, setBudgets)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getBudgetsWithUrl($event, pager.path + '?page=' +i, setBudgets)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getBudgetsWithUrl($event, pager.next_page_url, setBudgets)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getBudgetsWithUrl($event, pager.path+ '?page=' +pager.last_page, setBudgets)" aria-label="Previous">
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