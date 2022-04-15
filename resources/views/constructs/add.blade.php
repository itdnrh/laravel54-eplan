@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มแผนก่อสร้าง
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มแผนก่อสร้าง</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planConstructCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มแผนก่อสร้าง</h3>
                    </div>

                    <form id="frmNewService" name="frmNewService" method="post" action="{{ url('/services/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="service.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'category_id')}"
                                >
                                    <label>ประเภทงานก่อสร้าง :</label>
                                    <select id="category_id"
                                            name="category_id"
                                            ng-model="service.category_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($categories as $category)

                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'category_id')">
                                        @{{ formError.errors.service_type_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'group_id')}"
                                >
                                    <label>กลุ่มงานก่อสร้าง :</label>
                                    <select id="group_id"
                                            name="group_id"
                                            ng-model="service.group_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($groups as $group)

                                            <option value="{{ $group->id }}">
                                                {{ $group->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'group_id')">
                                        @{{ formError.errors.service_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'plan_no')}"
                                >
                                    <label>เลขที่ :</label>
                                    <input  type="text"
                                            id="plan_no"
                                            name="plan_no"
                                            ng-model="service.plan_no"
                                            class="form-control"
                                            tabindex="3">
                                    <span class="help-block" ng-show="checkValidate(service, 'plan_no')">
                                        @{{ formError.errors.plan_no[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'desc')}"
                                >
                                    <label>รายการ :</label>
                                    <input
                                        type="text"
                                        id="desc"
                                        name="desc"
                                        ng-model="service.desc"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(service, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'location')}"
                                >
                                    <label>สถานที่ :</label>
                                    <input
                                        type="text"
                                        id="location"
                                        name="location"
                                        ng-model="service.location"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(service, 'location')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'building_id')}"
                                >
                                    <label>อาคาร :</label>
                                    <select id="building_id"
                                            name="building_id"
                                            ng-model="service.building_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($buildings as $building)

                                            <option value="{{ $building->id }}">
                                                {{ $building->building_name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'building_id')">
                                        @{{ formError.errors.service_type_id[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'boq_no')}"
                                >
                                    <label>เลขที่ BOQ :</label>
                                    <input
                                        type="text"
                                        id="boq_no"
                                        name="boq_no"
                                        ng-model="service.boq_no"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(service, 'boq_no')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'price_per_unit')}"
                                >
                                    <label>ราคาต่อหน่วย :</label>
                                    <input  type="text"
                                            id="price_per_unit"
                                            name="price_per_unit"
                                            ng-model="service.price_per_unit"
                                            value=""
                                            class="form-control"
                                            tabindex="6"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(service, 'price_per_unit')">
                                        @{{ formError.errors.price_per_unit[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'unit_id')}"
                                >
                                    <label>หน่วย :</label>
                                    <select id="unit_id" 
                                            name="unit_id"
                                            ng-model="service.unit_id" 
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
                                    <span class="help-block" ng-show="checkValidate(service, 'unit_id')">
                                        @{{ formError.errors.unit_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'amount')}"
                                >
                                    <label>จำนวน :</label>
                                    <input  type="text"
                                            id="amount"
                                            name="amount"
                                            ng-model="service.amount"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(service, 'amount')">
                                        @{{ formError.errors.amount[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'sum_price')}"
                                >
                                    <label>รวมเป็นเงิน :</label>
                                    <input  type="text"
                                            id="sum_price"
                                            name="sum_price"
                                            ng-model="service.sum_price"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(service, 'sum_price')">
                                        @{{ formError.errors.sum_price[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'start_month')}"
                                >
                                    <label>เริ่มเดือน :</label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="service.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'faction_id')}"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="service.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(service.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)

                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'depart_id')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="service.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(service.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'division_id')}"
                                >
                                    <label>งาน :</label>
                                    <select id="division_id" 
                                            name="division_id"
                                            ng-model="service.division_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="13">
                                        <option value="">-- เลือกงาน --</option>
                                        <option ng-repeat="division in forms.divisions" value="@{{ division.ward_id }}">
                                            @{{ division.ward_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(service, 'division_id')">
                                        @{{ formError.errors.division_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'reason')}"
                                >
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="service.reason" 
                                        class="form-control"
                                        tabindex="14"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(service, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="service.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(service, 'remark')">
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
                                ng-click="formValidate($event, '/services/validate', service, 'frmNewService', store)"
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