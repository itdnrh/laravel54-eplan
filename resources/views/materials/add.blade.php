@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มรายการวัสดุ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มรายการวัสดุ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="materialCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มรายการวัสดุ</h3>
                    </div>

                    <form id="frmNewMaterial" name="frmNewMaterial" method="post" action="{{ url('/materials/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group"
                                    ng-class="{
                                        'col-md-12': material.in_stock == 0,
                                        'col-md-6': material.in_stock == 1,
                                        'has-error has-feedback': checkValidate(material, 'in_stock')
                                    }"
                                >
                                    <label>วัสดุใน/นอกคลัง :</label>
                                    <div style="display: flex; gap: 10px;">
                                        <div>
                                            <input
                                                type="radio"
                                                id="in_stock"
                                                name="in_stock"
                                                ng-value="1"
                                                ng-model="material.in_stock"
                                            /> วัสดุในคลัง
                                        </div>
                                        <div>
                                            <input
                                                type="radio"
                                                id="in_stock"
                                                name="in_stock"
                                                ng-value="0"
                                                ng-model="material.in_stock"
                                            /> วัสดุนอกคลัง
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(material, 'in_stock')">
                                        @{{ formError.errors.in_stock[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'year')}"
                                    ng-show="material.in_stock == 0"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="material.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'category_id')}"
                                >
                                    <label>ประเภทวัสดุ :</label>
                                    <select id="category_id"
                                            name="category_id"
                                            ng-model="material.category_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภทวัสดุ --</option>

                                        @foreach($categories as $category)

                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'category_id')">
                                        @{{ formError.errors.category_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'plan_no')}"
                                    ng-show="material.in_stock == 0"
                                >
                                    <label>เลขที่ :</label>
                                    <input  type="text"
                                            id="plan_no"
                                            name="plan_no"
                                            ng-model="material.plan_no"
                                            class="form-control"
                                            tabindex="3">
                                    <span class="help-block" ng-show="checkValidate(material, 'plan_no')">
                                        @{{ formError.errors.plan_no[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'desc')}"
                                >
                                    <label>ชื่อวัสดุ :</label>
                                    <input
                                        type="text"
                                        id="desc"
                                        name="desc"
                                        ng-model="material.desc"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(material, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'price_per_unit')}"
                                >
                                    <label>ราคาต่อหน่วย :</label>
                                    <input  type="text"
                                            id="price_per_unit"
                                            name="price_per_unit"
                                            ng-model="material.price_per_unit"
                                            value=""
                                            class="form-control"
                                            tabindex="6"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(material, 'price_per_unit')">
                                        @{{ formError.errors.price_per_unit[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'unit_id')}"
                                >
                                    <label>หน่วย :</label>
                                    <select id="unit_id" 
                                            name="unit_id"
                                            ng-model="material.unit_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="7">
                                        <option value="">-- เลือกหน่วย --</option>

                                        @foreach($units as $unit)

                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'unit_id')">
                                        @{{ formError.errors.unit_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'amount')}"
                                >
                                    <label>จำนวน :</label>
                                    <input  type="text"
                                            id="amount"
                                            name="amount"
                                            ng-model="material.amount"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(material, 'amount')">
                                        @{{ formError.errors.amount[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'sum_price')}"
                                >
                                    <label>รวมเป็นเงิน :</label>
                                    <input  type="text"
                                            id="sum_price"
                                            name="sum_price"
                                            ng-model="material.sum_price"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(material, 'sum_price')">
                                        @{{ formError.errors.sum_price[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'start_month')}"
                                    ng-show="material.in_stock == 0"
                                >
                                    <label>เริ่มเดือน :</label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="material.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group"
                                    ng-class="{
                                        'col-md-6': material.in_stock == 0,
                                        'col-md-12': material.in_stock == 1,
                                        'has-error has-feedback': checkValidate(material, 'faction_id')
                                    }"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="material.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(material.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)

                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'depart_id')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="material.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(material.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'division_id')}"
                                >
                                    <label>งาน :</label>
                                    <select id="division_id" 
                                            name="division_id"
                                            ng-model="material.division_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="13">
                                        <option value="">-- เลือกงาน --</option>
                                        <option ng-repeat="division in forms.divisions" value="@{{ division.ward_id }}">
                                            @{{ division.ward_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(material, 'division_id')">
                                        @{{ formError.errors.division_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'reason')}"
                                >
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="material.reason" 
                                        class="form-control"
                                        tabindex="14"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(material, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(material, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="material.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(material, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-12" ng-class="{'has-error has-feedback': checkValidate(leave, 'attachment')}">
                                    <label>แนบเอกสาร :</label>
                                    <input type="file"
                                            id="attachment" 
                                            name="attachment"
                                            class="form-control" />
                                    <span class="help-block" ng-show="checkValidate(leave, 'attachment')">กรุณาแนบเอกสาร</span>
                                </div>
                            </div> -->

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/materials/validate', material, 'frmNewMaterial', store)"
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