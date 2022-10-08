@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขประมาณการรายจ่าย
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขประมาณการรายจ่าย</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="budgetCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                expenses: {{ $expenses }}
            }, 4);
            getById({{ $budget->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">
                            แก้ไขประมาณการรายจ่าย
                            <span>(ID : {{ $budget->id }})</span>
                        </h3>
                    </div>

                    <form id="frmEditBudget" name="frmEditBudget" method="post" action="{{ url('/budget/update/'.$budget->id) }}" role="form" enctype="multipart/form-data">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="depart_id"
                            name="depart_id"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                            ng-model="budget.depart_id"
                        />
                        <input
                            type="hidden"
                            id="division"
                            name="division"
                            value="{{ Auth::user()->memberOf->division_id }}"
                            ng-model="budget.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                        <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'expense_type_id')}"
                                >
                                    <label>รายจ่าย :</label>
                                    <select
                                        id="expense_type_id"
                                        name="expense_type_id"
                                        ng-model="budget.expense_type_id"
                                        ng-change="onFilterExpenses(budget.expense_type_id)"
                                        class="form-control select2"
                                    >
                                        <option value="">เลือกประเภทรายจ่าย</option>
                                        @foreach($expenseTypes as $expenseType)
                                            <option value="{{ $expenseType->id }}">
                                                {{ $expenseType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(budget, 'expense_type_id')">
                                        @{{ formError.errors.expense_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'expense_id')}"
                                >
                                    <label>&nbsp;</label>
                                    <select
                                        id="expense_id"
                                        name="expense_id"
                                        ng-model="budget.expense_id"
                                        class="form-control"
                                    >
                                        <option value="">เลือกรายจ่าย</option>
                                        <option ng-repeat="expense in forms.expenses"value="@{{ expense.id }}">
                                            @{{ expense.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(budget, 'expense_id')">
                                        @{{ formError.errors.expense_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="budget.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(budget, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'budget')}"
                                >
                                    <label>ยอดประมาณการ (บาท) :</label>
                                    <input  type="text"
                                            id="budget"
                                            name="budget"
                                            ng-model="budget.budget"
                                            class="form-control pull-right"
                                            tabindex="8" />
                                    <span class="help-block" ng-show="checkValidate(budget, 'budget')">
                                        @{{ formError.errors.budget[0] }}
                                    </span>
                                </div>

                                <!-- <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'remain')}"
                                >
                                    <label>ยอดคงเหลือ (บาท) :</label>
                                    <input  type="text"
                                            id="remain"
                                            name="remain"
                                            ng-model="budget.remain"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(budget, 'remain')">
                                        @{{ formError.errors.remain[0] }}
                                    </span>
                                </div> -->
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'faction_id')}"
                                >
                                    <label>หน่วยงานผู้รายงาน :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="budget.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(budget.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(budget, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'owner_depart')}"
                                >
                                    <label>&nbsp;</label>
                                    <select id="owner_depart" 
                                            name="owner_depart"
                                            ng-model="budget.owner_depart" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(budget, 'owner_depart')">
                                        @{{ formError.errors.owner_depart[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(budget, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        row="4"
                                        ng-model="budget.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(budget, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/budgets/validate', budget, 'frmEditBudget', update)"
                                class="btn btn-warning pull-right"
                            >
                                แก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            $('.select2').select2();
            $('#budget').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection