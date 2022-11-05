@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มรายจ่าย
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มรายจ่าย</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="expenseCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                expenseTypes: {{ $expenseTypes }}
            }, 4);
            getById({{ $expense ->id }}, setEditControls)
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มรายจ่าย</h3>
                    </div>

                    <form id="frmEditExpense" name="frmEditExpense" method="post" action="{{ url('/expenses/update'.$expense->id) }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}
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
                            ng-model="support.depart_id"
                        />
                        <input
                            type="hidden"
                            id="division"
                            name="division"
                            value="{{ Auth::user()->memberOf->division_id }}"
                            ng-model="support.division_id"
                        />

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(expense, 'expense_type_id')}"
                                >
                                    <label>รายจ่าย :</label>
                                    <select
                                        id="expense_type_id"
                                        name="expense_type_id"
                                        ng-model="expense.expense_type_id"
                                        ng-change="onFilterExpenses(expense.expense_type_id)"
                                        class="form-control"
                                    >
                                        <option value="">เลือกประเภทรายจ่าย</option>
                                        @foreach($expenseTypes as $expenseType)
                                            <option value="{{ $expenseType->id }}">
                                                {{ $expenseType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(expense, 'expense_type_id')">
                                        @{{ formError.errors.expense_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(expense, 'name')}"
                                >
                                    <label>ชื่อรายจ่าย :</label>
                                    <input  type="text"
                                            id="name"
                                            name="name"
                                            ng-model="expense.name"
                                            class="form-control pull-right"
                                            tabindex="8" />
                                    <span class="help-block" ng-show="checkValidate(expense, 'name')">
                                        @{{ formError.errors.name[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(expense, 'faction_id')}"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="expense.faction_id" 
                                            class="form-control"
                                            ng-change="onFactionSelected(expense.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(expense, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(expense, 'owner_depart')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="owner_depart" 
                                            name="owner_depart"
                                            ng-model="expense.owner_depart" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(expense.owner_depart)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(expense, 'owner_depart')">
                                        @{{ formError.errors.owner_depart[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(expense, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        row="4"
                                        ng-model="expense.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(expense, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/expenses/validate', expense, 'frmEditExpense', update)"
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
        });
    </script>

@endsection