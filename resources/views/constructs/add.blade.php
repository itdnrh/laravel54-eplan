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
            categories: {{ $categories }},
            groups: {{ $groups }}
        }, 4);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มแผนก่อสร้าง</h3>
                    </div>

                    <form id="frmNewConstruct" name="frmNewConstruct" method="post" action="{{ url('/constructs/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'in_plan')}"
                                >
                                    <label>ในแผน/นอกแผน :</label>
                                    <div class="form-control checkbox-groups">
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="in_plan"
                                                    name="in_plan"
                                                    value="I"
                                                    ng-model="construct.in_plan"
                                                    tabindex="3"> ในแผน
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="in_plan"
                                                    name="in_plan"
                                                    value="O"
                                                    ng-model="construct.in_plan"
                                                    tabindex="3"> นอกแผน
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(construct, 'in_plan')">
                                        @{{ formError.errors.in_plan[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="construct.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <!-- <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'plan_no')}"
                                >
                                    <label>เลขที่ :</label>
                                    <input  type="text"
                                            id="plan_no"
                                            name="plan_no"
                                            ng-model="construct.plan_no"
                                            class="form-control"
                                            tabindex="3">
                                    <span class="help-block" ng-show="checkValidate(construct, 'plan_no')">
                                        @{{ formError.errors.plan_no[0] }}
                                    </span>
                                </div> -->
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'faction_id')}"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="construct.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(construct.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'depart_id')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="construct.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(construct.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'division_id')}"
                                >
                                    <label>งาน :</label>
                                    <select id="division_id" 
                                            name="division_id"
                                            ng-model="construct.division_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="13">
                                        <option value="">-- เลือกงาน --</option>
                                        <option ng-repeat="division in forms.divisions" value="@{{ division.ward_id }}">
                                            @{{ division.ward_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'division_id')">
                                        @{{ formError.errors.division_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'desc')}"
                                >
                                    <label>รายการ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="desc"
                                            name="desc"
                                            ng-model="construct.desc"
                                            class="form-control pull-right"
                                            tabindex="4"
                                        />
                                        <input type="hidden" id="item_id" name="item_id" ng-model="construct.item_id" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat" ng-click="showItemsList()">
                                                ...
                                            </button>
                                            <button type="button" class="btn btn-primary btn-flat" ng-click="showNewItemForm()">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(construct, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'location')}"
                                >
                                    <label>สถานที่ :</label>
                                    <input
                                        type="text"
                                        id="location"
                                        name="location"
                                        ng-model="construct.location"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(construct, 'location')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'building_id')}"
                                >
                                    <label>อาคาร :</label>
                                    <select id="building_id"
                                            name="building_id"
                                            ng-model="construct.building_id"
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
                                    <span class="help-block" ng-show="checkValidate(construct, 'building_id')">
                                        @{{ formError.errors.construct_type_id[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'boq_no')}"
                                >
                                    <label>เลขที่ BOQ :</label>
                                    <input
                                        type="text"
                                        id="boq_no"
                                        name="boq_no"
                                        ng-model="construct.boq_no"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(construct, 'boq_no')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'price_per_unit')}"
                                >
                                    <label>ราคาต่อหน่วย :</label>
                                    <input  type="text"
                                            id="price_per_unit"
                                            name="price_per_unit"
                                            ng-model="construct.price_per_unit"
                                            value=""
                                            class="form-control"
                                            tabindex="6"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(construct, 'price_per_unit')">
                                        @{{ formError.errors.price_per_unit[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'unit_id')}"
                                >
                                    <label>หน่วย :</label>
                                    <select id="unit_id" 
                                            name="unit_id"
                                            ng-model="construct.unit_id" 
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกหน่วย --</option>

                                        @foreach($units as $unit)

                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'unit_id')">
                                        @{{ formError.errors.unit_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'amount')}"
                                >
                                    <label>จำนวนที่ขอ :</label>
                                    <input  type="text"
                                            id="amount"
                                            name="amount"
                                            ng-model="construct.amount"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(construct, 'amount')">
                                        @{{ formError.errors.amount[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'sum_price')}"
                                >
                                    <label>รวมเป็นเงิน :</label>
                                    <input  type="text"
                                            id="sum_price"
                                            name="sum_price"
                                            ng-model="construct.sum_price"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(construct, 'sum_price')">
                                        @{{ formError.errors.sum_price[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'request_cause')}"
                                >
                                    <label>สาเหตุที่ขอ :</label>
                                    <div class="form-control checkbox-groups">
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="N"
                                                    ng-model="construct.request_cause"
                                                    tabindex="3"> ขอใหม่
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="R"
                                                    ng-model="construct.request_cause"
                                                    tabindex="3"> ทดแทน
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="E"
                                                    ng-model="construct.request_cause"
                                                    tabindex="3"> ขยายงาน
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(construct, 'request_cause')">
                                        @{{ formError.errors.request_cause[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'budget_src_id')}"
                                >
                                    <label>แหล่งเงินงบประมาณ :</label>
                                    <select
                                        id="budget_src_id"
                                        name="budget_src_id"
                                        ng-model="construct.budget_src_id"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- เลือกแหล่งเงินงบประมาณ --</option>
                                        @foreach($budgetSources as $budgetSource)
                                            <option value="{{ $budgetSource->id }}">{{ $budgetSource->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'budget_src_id')">
                                        กรุณาเลือกแหล่งเงินงบประมาณ
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'start_month')}"
                                >
                                    <label>เดือนที่จะดำเนินการ :</label>
                                    <input
                                        type="text"
                                        id="start_month"
                                        name="start_month"
                                        ng-model="construct.start_month"
                                        class="form-control"
                                    />
                                    <!-- <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="construct.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select> -->
                                    <span class="help-block" ng-show="checkValidate(construct, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <select id="strategic_id" 
                                            name="strategic_id"
                                            ng-model="construct.strategic_id"
                                            ng-change="onStrategicSelected(construct.strategic_id);"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกยุทธศาสตร์ --</option>
                                        @foreach($strategics as $strategic)
                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'strategic_id')">
                                        @{{ formError.errors.strategic_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'service_plan_id')}"
                                >
                                    <label>Service Plan :</label>
                                    <select id="service_plan_id" 
                                            name="service_plan_id"
                                            ng-model="construct.service_plan_id"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือก Service Plan --</option>
                                        @foreach($servicePlans as $servicePlan)
                                            <option value="{{ $servicePlan->id }}">
                                                {{ $servicePlan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'service_plan_id')">
                                        @{{ formError.errors.service_plan_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row" style="">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'reason')}"
                                >
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="construct.reason" 
                                        class="form-control"
                                        tabindex="14"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(construct, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="construct.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(construct, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-12" ng-class="{'has-error has-feedback': checkValidate(construct, 'attachment')}">
                                    <label>แนบเอกสาร :</label>
                                    <input type="file"
                                            id="attachment" 
                                            name="attachment"
                                            class="form-control" />
                                    <span class="help-block" ng-show="checkValidate(construct, 'attachment')">กรุณาแนบเอกสาร</span>
                                </div>
                            </div> -->
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/constructs/validate', construct, 'frmNewConstruct', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._items-list')
        @include('shared._item-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection