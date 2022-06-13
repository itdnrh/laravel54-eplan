@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มรายการควบคุมกำกับติดตาม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มรายการควบคุมกำกับติดตาม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="monthlyCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
        }, 4);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มรายการควบคุมกำกับติดตาม</h3>
                    </div>

                    <form id="frmNewMonthly" name="frmNewMonthly" method="post" action="{{ url('/constructs/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="monthly.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'month')}"
                                >
                                    <label>ประจำเดือน :</label>
                                    <select
                                        id="month"
                                        name="month"
                                        ng-model="monthly.month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'month')">
                                        @{{ formError.errors.month[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'expense_id')}"
                                >
                                    <label>รายการ :</label>
                                    <select
                                        id="expense_id"
                                        ng-model="monthly.expense_id"
                                        class="form-control"
                                    >
                                        <option value="">เลือกรายการ</option>
                                        @foreach($expenses as $expense)
                                            <option value="{{ $expense->id }}">
                                                {{ $expense->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'expense_id')">
                                        @{{ formError.errors.expense_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'total')}"
                                >
                                    <label>ยอดการใช้ (บาท) :</label>
                                    <input  type="text"
                                            id="total"
                                            name="total"
                                            ng-model="monthly.total"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(monthly, 'total')">
                                        @{{ formError.errors.total[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'remain')}"
                                >
                                    <label>ยอดคงเหลือ (บาท) :</label>
                                    <input  type="text"
                                            id="remain"
                                            name="remain"
                                            ng-model="monthly.remain"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(monthly, 'remain')">
                                        @{{ formError.errors.remain[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'faction_id')}"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="monthly.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(monthly.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)

                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'depart_id')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="monthly.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(monthly.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(monthly, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        row="4"
                                        ng-model="monthly.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(monthly, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/monthly/validate', monthly, 'frmNewMonthly', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
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