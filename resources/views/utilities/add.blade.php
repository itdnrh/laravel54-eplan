@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มค่าสาธารณูปโภค
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มค่าสาธารณูปโภค</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="utilityCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มค่าสาธารณูปโภค</h3>
                    </div>

                    <form id="frmNewUtility" name="frmNewUtility" method="post" action="{{ url('/utilities/store') }}" role="form" enctype="multipart/form-data">
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
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'bill_no')}"
                                >
                                    <label>เลขที่บิล :</label>
                                    <input  type="text"
                                            id="bill_no"
                                            name="bill_no"
                                            ng-model="utility.bill_no"
                                            class="form-control"
                                            tabindex="6">
                                    <span class="help-block" ng-show="checkValidate(utility, 'bill_no')">
                                        @{{ formError.errors.bill_no[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'bill_date')}"
                                >
                                    <label>วันที่บิล :</label>
                                    <input
                                        type="text"
                                        id="bill_date"
                                        name="bill_date"
                                        ng-model="utility.bill_date"
                                        class="form-control"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(utility, 'bill_date')">
                                        @{{ formError.errors.bill_date[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'supplier_id')}"
                                >
                                    <label>เจ้าหนี้ :</label>
                                    <select id="supplier_id"
                                            name="supplier_id"
                                            ng-model="utility.supplier_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}">
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(utility, 'supplier_id')">
                                        @{{ formError.errors.supplier_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="utility.year"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(utility, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'month')}"
                                >
                                    <label>ประจำเดือน</label>
                                    <select
                                        id="month"
                                        name="month"
                                        ng-model="utility.month"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประจำเดือน --</option>
                                        <option ng-repeat="m in monthLists" value="@{{ m.id }}">
                                            @{{ m.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(utility, 'month')">
                                        @{{ formError.errors.month[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'utility_type_id')}"
                                >
                                    <label>ประเภท :</label>
                                    <select id="utility_type_id"
                                            name="utility_type_id"
                                            ng-model="utility.utility_type_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>
                                        @foreach($utilityTypes as $utilityType)
                                            <option value="{{ $utilityType->id }}">
                                                {{ $utilityType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(utility, 'utility_type_id')">
                                        @{{ formError.errors.utility_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'desc')}"
                                >
                                    <label>รายละเอียด :</label>
                                    <input
                                        type="text"
                                        id="desc"
                                        name="desc"
                                        ng-model="utility.desc"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(utility, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'quantity')}"
                                >
                                    <label>ปริมาณที่ใช้ (ถ้ามี) :</label>
                                    <input
                                        type="text"
                                        id="quantity"
                                        name="quantity"
                                        ng-model="utility.quantity"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(utility, 'quantity')">
                                        @{{ formError.errors.quantity[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'net_total')}"
                                >
                                    <label>ยอดเงิน :</label>
                                    <input
                                        type="text"
                                        id="net_total"
                                        name="net_total"
                                        ng-model="utility.net_total"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(utility, 'net_total')">
                                        @{{ formError.errors.net_total[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(utility, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        rows="4"
                                        ng-model="utility.remark"
                                        class="form-control"
                                        tabindex="1"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(utility, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <<button
                                ng-click="formValidate($event, '/utilities/validate', utility, 'frmNewUtility', store)"
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